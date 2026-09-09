import "@/scripts/load-env";
import { createClient } from "@supabase/supabase-js";
import { getServiceEnv } from "@/lib/env";
import { seedQuizzes } from "@/scripts/quiz-seed";
import type { Database } from "@/types/database";

async function seedQuizData(): Promise<void> {
  const env = getServiceEnv();
  const supabase = createClient<Database>(
    env.NEXT_PUBLIC_SUPABASE_URL,
    env.SUPABASE_SECRET_KEY,
    { auth: { persistSession: false, autoRefreshToken: false } },
  );
  await seedQuizzes(supabase);
}

seedQuizData().catch((error: unknown) => {
  console.error(error instanceof Error ? error.message : error);
  process.exitCode = 1;
});
