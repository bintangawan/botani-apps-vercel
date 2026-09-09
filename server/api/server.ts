import "server-only";
import { cache } from "react";
import { createSupabasePublicClient } from "@/lib/supabase/public";
import { createTRPCContext } from "@/server/api/context";
import { createCaller } from "@/server/api/root";

export const api = cache(async () => createCaller(await createTRPCContext()));

export const publicApi = cache(async () =>
  createCaller({
    supabase: createSupabasePublicClient(),
    user: null,
    profile: null,
  }),
);
