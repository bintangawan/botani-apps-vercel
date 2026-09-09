import "@/scripts/load-env";
import { createClient } from "@supabase/supabase-js";
import { z } from "zod";
import { getServiceEnv } from "@/lib/env";
import type { Database } from "@/types/database";

const inputSchema = z.object({ email: z.email(), role: z.enum(["mahasiswa", "dosen", "admin"]) });

async function promote(): Promise<void> {
  const input = inputSchema.parse({ email: process.argv[2], role: process.argv[3] });
  const env = getServiceEnv();
  const supabase = createClient<Database>(env.NEXT_PUBLIC_SUPABASE_URL, env.SUPABASE_SECRET_KEY, { auth: { persistSession: false, autoRefreshToken: false } });
  const { data: profile, error: findError } = await supabase.from("profiles").select("id,name").eq("email", input.email).maybeSingle();
  if (findError) throw findError;
  if (!profile) throw new Error("Pengguna belum terdaftar. Daftarkan akun melalui aplikasi terlebih dahulu.");
  const { error: updateError } = await supabase.from("profiles").update({ role: input.role, status: "active" }).eq("id", profile.id);
  if (updateError) throw updateError;
  console.info(`Role ${profile.name} berhasil diubah menjadi ${input.role}.`);
}

promote().catch((error: unknown) => { console.error(error instanceof Error ? error.message : error); process.exitCode = 1; });
