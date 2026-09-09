import { z } from "zod";

const publicEnvSchema = z.object({
  NEXT_PUBLIC_SUPABASE_URL: z.url(),
  NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY: z.string().min(1),
  NEXT_PUBLIC_SITE_URL: z.url(),
  NEXT_PUBLIC_STORAGE_BUCKET: z.string().min(1),
});

const serviceEnvSchema = publicEnvSchema.extend({
  SUPABASE_SECRET_KEY: z.string().min(1),
});

const databaseEnvSchema = z.object({
  DATABASE_URL: z.string().min(1).startsWith("postgresql://"),
});

const migrationEnvSchema = z.object({
  DIRECT_URL: z.string().min(1).startsWith("postgresql://"),
});

export type PublicEnv = z.infer<typeof publicEnvSchema>;
export type ServiceEnv = z.infer<typeof serviceEnvSchema>;
export type DatabaseEnv = z.infer<typeof databaseEnvSchema>;
export type MigrationEnv = z.infer<typeof migrationEnvSchema>;

function readableEnvError(error: z.ZodError): Error {
  const fields = error.issues.map((issue) => issue.path.join(".")).join(", ");
  return new Error(`Environment variable belum lengkap atau tidak valid: ${fields}`);
}

export function getPublicEnv(): PublicEnv {
  const result = publicEnvSchema.safeParse({
    NEXT_PUBLIC_SUPABASE_URL: process.env.NEXT_PUBLIC_SUPABASE_URL,
    NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY: process.env.NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY,
    NEXT_PUBLIC_SITE_URL: process.env.NEXT_PUBLIC_SITE_URL,
    NEXT_PUBLIC_STORAGE_BUCKET: process.env.NEXT_PUBLIC_STORAGE_BUCKET,
  });

  if (!result.success) {
    throw readableEnvError(result.error);
  }

  return result.data;
}

export function getServiceEnv(): ServiceEnv {
  const result = serviceEnvSchema.safeParse({
    ...getPublicEnv(),
    SUPABASE_SECRET_KEY: process.env.SUPABASE_SECRET_KEY,
  });

  if (!result.success) {
    throw readableEnvError(result.error);
  }

  return result.data;
}

export function getDatabaseEnv(): DatabaseEnv {
  const result = databaseEnvSchema.safeParse({
    DATABASE_URL: process.env.DATABASE_URL,
  });

  if (!result.success) {
    throw readableEnvError(result.error);
  }

  return result.data;
}

export function getMigrationEnv(): MigrationEnv {
  const result = migrationEnvSchema.safeParse({
    DIRECT_URL: process.env.DIRECT_URL,
  });

  if (!result.success) {
    throw readableEnvError(result.error);
  }

  return result.data;
}
