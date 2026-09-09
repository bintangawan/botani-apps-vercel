import "@/scripts/load-env";
import { createClient, type SupabaseClient } from "@supabase/supabase-js";
import { z } from "zod";
import { getServiceEnv } from "@/lib/env";
import type { Database, UserRole } from "@/types/database";

const accountSchema = z.object({
  name: z.string().trim().min(2),
  email: z.email().transform((email) => email.toLowerCase()),
  password: z.string().min(8),
  institution: z.string().trim().transform((institution) => institution || null),
});

type SeedAccount = z.infer<typeof accountSchema> & {
  role: UserRole;
};

const seedEnvironmentKeys = [
  "SEED_MAHASISWA_NAME",
  "SEED_MAHASISWA_EMAIL",
  "SEED_MAHASISWA_PASSWORD",
  "SEED_MAHASISWA_INSTITUTION",
  "SEED_DOSEN_NAME",
  "SEED_DOSEN_EMAIL",
  "SEED_DOSEN_PASSWORD",
  "SEED_DOSEN_INSTITUTION",
  "SEED_ADMIN_NAME",
  "SEED_ADMIN_EMAIL",
  "SEED_ADMIN_PASSWORD",
  "SEED_ADMIN_INSTITUTION",
] as const;

function readSeedAccounts(): SeedAccount[] {
  const missingKeys = seedEnvironmentKeys.filter(
    (key) => process.env[key] === undefined,
  );

  if (missingKeys.length > 0) {
    throw new Error(
      `Lengkapi environment untuk seed akun: ${missingKeys.join(", ")}.`,
    );
  }

  return [
    {
      role: "mahasiswa",
      ...accountSchema.parse({
        name: process.env.SEED_MAHASISWA_NAME,
        email: process.env.SEED_MAHASISWA_EMAIL,
        password: process.env.SEED_MAHASISWA_PASSWORD,
        institution: process.env.SEED_MAHASISWA_INSTITUTION,
      }),
    },
    {
      role: "dosen",
      ...accountSchema.parse({
        name: process.env.SEED_DOSEN_NAME,
        email: process.env.SEED_DOSEN_EMAIL,
        password: process.env.SEED_DOSEN_PASSWORD,
        institution: process.env.SEED_DOSEN_INSTITUTION,
      }),
    },
    {
      role: "admin",
      ...accountSchema.parse({
        name: process.env.SEED_ADMIN_NAME,
        email: process.env.SEED_ADMIN_EMAIL,
        password: process.env.SEED_ADMIN_PASSWORD,
        institution: process.env.SEED_ADMIN_INSTITUTION,
      }),
    },
  ];
}

async function findAuthUserId(
  supabase: SupabaseClient<Database>,
  email: string,
): Promise<string | null> {
  const perPage = 1_000;

  for (let page = 1; ; page += 1) {
    const { data, error } = await supabase.auth.admin.listUsers({ page, perPage });
    if (error) throw error;

    const user = data.users.find(
      (candidate) => candidate.email?.toLowerCase() === email,
    );
    if (user) return user.id;
    if (data.users.length < perPage) return null;
  }
}

async function seedAccount(
  supabase: SupabaseClient<Database>,
  account: SeedAccount,
): Promise<void> {
  const existingUserId = await findAuthUserId(supabase, account.email);
  const userAttributes = {
    email: account.email,
    password: account.password,
    email_confirm: true,
    user_metadata: {
      name: account.name,
      institution: account.institution,
    },
  };

  let userId: string;
  if (existingUserId) {
    const { data, error } = await supabase.auth.admin.updateUserById(
      existingUserId,
      userAttributes,
    );
    if (error) throw error;
    userId = data.user.id;
  } else {
    const { data, error } = await supabase.auth.admin.createUser(userAttributes);
    if (error) throw error;
    userId = data.user.id;
  }

  const { error: profileError } = await supabase.from("profiles").upsert(
    {
      id: userId,
      name: account.name,
      email: account.email,
      role: account.role,
      institution: account.institution,
      status: "active",
    },
    { onConflict: "id" },
  );
  if (profileError) throw profileError;

  console.info(`Akun ${account.role} siap digunakan.`);
}

function createAdminClient(): SupabaseClient<Database> {
  const env = getServiceEnv();
  return createClient<Database>(
    env.NEXT_PUBLIC_SUPABASE_URL,
    env.SUPABASE_SECRET_KEY,
    { auth: { persistSession: false, autoRefreshToken: false } },
  );
}

async function auditUsers(): Promise<void> {
  const supabase = createAdminClient();
  const { data, error, count } = await supabase
    .from("profiles")
    .select("role", { count: "exact" });
  if (error) throw error;

  const roles: Record<UserRole, number> = {
    mahasiswa: 0,
    dosen: 0,
    admin: 0,
  };
  for (const profile of data) roles[profile.role] += 1;

  console.info(
    JSON.stringify({ profilesTable: true, total: count ?? 0, roles }),
  );
}

async function seedUsers(): Promise<void> {
  const supabase = createAdminClient();
  const accounts = readSeedAccounts();

  for (const account of accounts) await seedAccount(supabase, account);
  console.info("Seed tiga akun Supabase selesai.");
}

const action = process.argv.includes("--audit") ? auditUsers : seedUsers;
action().catch((error: unknown) => {
  console.error(error instanceof Error ? error.message : error);
  process.exitCode = 1;
});
