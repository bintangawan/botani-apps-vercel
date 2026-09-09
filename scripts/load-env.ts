import { config } from "dotenv";

// An explicit file never falls back to local development credentials.
const explicitEnvFile = process.env.BOTANI_ENV_FILE;
if (explicitEnvFile) {
  const result = config({ path: explicitEnvFile, quiet: true, override: true });
  if (result.error) throw new Error(`Gagal membaca environment: ${explicitEnvFile}`);
} else {
  config({ path: ".env.local", quiet: true });
  config({ path: ".env", quiet: true });
}
