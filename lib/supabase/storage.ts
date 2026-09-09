import type { SupabaseClient } from "@supabase/supabase-js";
import { getPublicEnv } from "@/lib/env";
import type { Database } from "@/types/database";

export function getStoragePublicUrl(
  supabase: SupabaseClient<Database>,
  path: string | null,
): string | null {
  if (!path) {
    return null;
  }

  const env = getPublicEnv();
  return supabase.storage.from(env.NEXT_PUBLIC_STORAGE_BUCKET).getPublicUrl(path).data.publicUrl;
}
