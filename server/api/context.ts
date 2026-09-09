import { getAuthState, type AuthState } from "@/server/auth-state";

export type TRPCContext = AuthState;

export async function createTRPCContext(): Promise<TRPCContext> {
  return getAuthState();
}
