import "@/scripts/load-env";

import { createHash } from "node:crypto";
import { readdir, readFile } from "node:fs/promises";
import path from "node:path";
import postgres from "postgres";
import { getMigrationEnv } from "@/lib/env";

type AppliedMigration = {
  checksum: string;
};

function unwrapTransaction(source: string): string {
  return source
    .replace(/^\uFEFF?\s*begin;\s*/i, "")
    .replace(/\s*commit;\s*$/i, "")
    .trim();
}

async function migrate(): Promise<void> {
  const { DIRECT_URL } = getMigrationEnv();

  if (DIRECT_URL.includes("YOUR_")) {
    throw new Error("DIRECT_URL masih berisi placeholder dari .env.example.");
  }

  const client = postgres(DIRECT_URL, {
    max: 1,
    prepare: false,
  });

  try {
    await client.unsafe(`
      create schema if not exists botani_internal;
      revoke all on schema botani_internal from public, anon, authenticated;
      create table if not exists botani_internal.schema_migrations (
        filename text primary key,
        checksum text not null,
        applied_at timestamptz not null default now()
      );
    `);

    const migrationsDirectory = path.join(process.cwd(), "supabase", "migrations");
    const filenames = (await readdir(migrationsDirectory))
      .filter((filename) => filename.endsWith(".sql"))
      .sort((left, right) => left.localeCompare(right));

    if (filenames.length === 0) {
      throw new Error("Tidak ada migration SQL di supabase/migrations.");
    }

    for (const filename of filenames) {
      const source = await readFile(path.join(migrationsDirectory, filename), "utf8");
      const checksum = createHash("sha256").update(source).digest("hex");
      const applied = await client<AppliedMigration[]>`
        select checksum
        from botani_internal.schema_migrations
        where filename = ${filename}
      `;
      const existing = applied[0];

      if (existing) {
        if (existing.checksum !== checksum) {
          throw new Error(`Migration ${filename} sudah diterapkan tetapi checksum berubah.`);
        }

        console.info(`Lewati ${filename} (sudah diterapkan).`);
        continue;
      }

      const migrationSql = unwrapTransaction(source);
      await client.begin(async (transaction) => {
        await transaction.unsafe(migrationSql);
        await transaction`
          insert into botani_internal.schema_migrations (filename, checksum)
          values (${filename}, ${checksum})
        `;
      });

      console.info(`Berhasil menerapkan ${filename}.`);
    }

    console.info("Seluruh migration Supabase sudah sinkron.");
  } finally {
    await client.end();
  }
}

migrate().catch((error: unknown) => {
  console.error(error instanceof Error ? error.message : error);
  process.exitCode = 1;
});
