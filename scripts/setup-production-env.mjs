import { randomBytes } from "node:crypto";
import { readFile, writeFile } from "node:fs/promises";
import { parse } from "dotenv";

// Create a private deployment file without printing or overwriting credentials.
const runtimeKeys = [
  "NEXT_PUBLIC_SITE_URL", "NEXT_PUBLIC_SUPABASE_URL",
  "NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY", "NEXT_PUBLIC_STORAGE_BUCKET",
  "SUPABASE_SECRET_KEY", "DATABASE_URL", "DIRECT_URL",
];
try {
  if (process.argv.includes("--use-pooler-for-migrations")) {
    const source = await readFile(".env.production", "utf8");
    const values = parse(source);
    const direct = new URL(values.DIRECT_URL);
    const pooler = new URL(values.DATABASE_URL);
    const projectRef = direct.hostname.match(/^db\.([a-z0-9]+)\.supabase\.co$/)?.[1];
    if (!projectRef || decodeURIComponent(pooler.username) !== `postgres.${projectRef}`) {
      throw new Error("Cannot verify the same Supabase project.");
    }
    // Explicit opt-in for this SQL transaction-based runner after connection testing.
    const updated = source.replace(/^DIRECT_URL=.*$/m, () => `DIRECT_URL=${JSON.stringify(values.DATABASE_URL)}`);
    await writeFile(".env.production", updated, { mode: 0o600 });
    console.info("DIRECT_URL production memakai pooler proyek yang sama; kredensial tidak ditampilkan.");
    process.exit(0);
  }
  const local = parse(await readFile(".env.local", "utf8"));
  const lines = [
    "# Private production environment. Never commit this file.",
    "# Database credentials initialized from .env.local; verify the target before migration.",
    "# Import app values into Vercel Production. Supabase cron is activated by database migration.",
    "# CRON_SECRET is generated as requested but is unused by database-local pg_cron.",
    "# Set NEXT_PUBLIC_SITE_URL to the real HTTPS production domain before deployment.",
  ];
  for (const key of runtimeKeys) {
    let value = local[key] ?? "";
    if (key === "NEXT_PUBLIC_SITE_URL" && (!value || /localhost|127\.0\.0\.1/.test(value))) {
      value = "https://YOUR_PRODUCTION_DOMAIN";
    }
    lines.push(`${key}=${JSON.stringify(value)}`);
  }
  lines.push(`CRON_SECRET=${randomBytes(32).toString("base64url")}`, "");
  await writeFile(".env.production", lines.join("\n"), { flag: "wx", mode: 0o600 });
  console.info(".env.production dibuat; CRON_SECRET acak 256-bit disimpan tanpa ditampilkan.");
} catch (error) {
  if (error?.code === "EEXIST") {
    console.info(".env.production sudah ada; nilai yang ada dipertahankan.");
  } else {
    console.error("Gagal menyiapkan .env.production. Periksa file .env.local dan izin folder.");
    process.exitCode = 1;
  }
}
