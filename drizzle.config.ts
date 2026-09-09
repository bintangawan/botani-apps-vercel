import "./scripts/load-env";
import { defineConfig } from "drizzle-kit";

const migrationUrl = process.env.DIRECT_URL;

if (!migrationUrl) {
  throw new Error("DIRECT_URL wajib diisi untuk menjalankan Drizzle Kit.");
}

export default defineConfig({
  dialect: "postgresql",
  schema: "./db/schema.ts",
  out: "./drizzle",
  dbCredentials: { url: migrationUrl },
  strict: true,
  verbose: true,
});
