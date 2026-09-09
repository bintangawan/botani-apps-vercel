// Select the production file before importing the canonical migration runner.
process.env.BOTANI_ENV_FILE = ".env.production";
void import("./migrate-supabase").catch(() => {
  console.error("Gagal memulai migrasi production. Periksa .env.production.");
  process.exitCode = 1;
});
export {};
