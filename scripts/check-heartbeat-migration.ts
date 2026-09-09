import "./load-env";
import assert from "node:assert/strict";
import { readFile } from "node:fs/promises";
import postgres from "postgres";
import { getMigrationEnv } from "@/lib/env";

async function check(): Promise<void> {
  const client = postgres(getMigrationEnv().DIRECT_URL, { max: 1, prepare: false, connect_timeout: 10 });
  const rollback = new Error("heartbeat-check-rollback");
  try {
    const source = await readFile("supabase/migrations/202609090001_system_heartbeat.sql", "utf8");
    await client.begin(async (tx) => {
      await tx.unsafe(source.replace(/^\s*begin;\s*/i, "").replace(/\s*commit;\s*$/i, ""));
      await tx`select botani_internal.record_heartbeat()`;
      await tx`select botani_internal.record_heartbeat()`;
      const rows = await tx`select id, source, hit_count from public.system_heartbeat`;
      assert.equal(rows.length, 1);
      assert.equal(Number(rows[0]?.hit_count), 2);
      assert.equal(rows[0]?.source, "supabase-pg-cron");
      const jobs = await tx`select schedule, active from cron.job where jobname = 'botani-daily-heartbeat'`;
      assert.equal(jobs.length, 1);
      assert.equal(jobs[0]?.schedule, "0 17 * * *");
      assert.equal(jobs[0]?.active, true);
      const [permissions] = await tx`select
        has_table_privilege('anon', 'public.system_heartbeat', 'SELECT,INSERT,UPDATE,DELETE') as anon_access,
        has_table_privilege('authenticated', 'public.system_heartbeat', 'SELECT,INSERT,UPDATE,DELETE') as user_access,
        has_function_privilege('anon', 'botani_internal.record_heartbeat()', 'EXECUTE') as public_execute`;
      assert.equal(permissions?.anon_access, false);
      assert.equal(permissions?.user_access, false);
      assert.equal(permissions?.public_execute, false);
      throw rollback;
    });
  } catch (error) {
    if (error !== rollback) throw error;
    console.info("Heartbeat SQL, update satu baris, jadwal, dan izin lulus; seluruh perubahan di-rollback.");
  } finally {
    await client.end();
  }
}

check().catch((error: unknown) => {
  const code = error && typeof error === "object" && "code" in error ? String(error.code) : "CHECK_FAILED";
  console.error(`Pemeriksaan migrasi gagal (${code}); periksa koneksi/izin database atau apakah migrasi sudah diterapkan.`);
  process.exitCode = 1;
});
