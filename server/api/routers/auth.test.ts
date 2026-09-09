import assert from "node:assert/strict";
import { test } from "node:test";
import { TRPCError } from "@trpc/server";
import { authRouter } from "./auth";
import type { TRPCContext } from "../context";

function fixture(options: {
  role?: "admin" | "dosen" | "mahasiswa";
  status?: string;
  missingProfile?: boolean;
  profileError?: { message: string };
  authError?: { status: number; code?: string };
} = {}) {
  let signedOut = false;
  const supabase = {
    auth: {
      signInWithPassword: async () => ({ data: { user: { id: "test-user" } }, error: options.authError ?? null }),
      signOut: async () => { signedOut = true; return { error: null }; },
    },
    from: () => ({ select: () => ({ eq: () => ({ maybeSingle: async () => ({
      data: options.missingProfile ? null : { role: options.role ?? "mahasiswa", status: options.status ?? "active" },
      error: options.profileError ?? null,
    }) }) }) }),
  };
  const caller = authRouter.createCaller({ supabase, user: null, profile: null } as unknown as TRPCContext);
  return { caller, signedOut: () => signedOut };
}

const input = { email: "student@example.test", password: "legacy" };

for (const role of ["admin", "dosen", "mahasiswa"] as const) {
  test(`active ${role} signs in to an existing dashboard route`, async () => {
    const { caller } = fixture({ role });
    assert.deepEqual(await caller.signIn(input), { redirectTo: `/${role}/dashboard` });
  });
}

test("database failures do not report an inactive account and clean up the session", async () => {
  const f = fixture({ profileError: { message: "fetch failed" } });
  await assert.rejects(f.caller.signIn(input), (error) => error instanceof TRPCError && error.code === "INTERNAL_SERVER_ERROR");
  assert.equal(f.signedOut(), true);
});

for (const options of [{ missingProfile: true }, { status: "inactive" }]) {
  test(`unavailable profile rejects access: ${JSON.stringify(options)}`, async () => {
    const f = fixture(options);
    await assert.rejects(f.caller.signIn(input), (error) => error instanceof TRPCError && error.code === "FORBIDDEN");
    assert.equal(f.signedOut(), true);
  });
}

for (const [status, code] of [[429, "TOO_MANY_REQUESTS"], [503, "SERVICE_UNAVAILABLE"], [400, "UNAUTHORIZED"]] as const) {
  test(`authentication HTTP ${status} returns ${code}`, async () => {
    const { caller } = fixture({ authError: { status } });
    await assert.rejects(caller.signIn(input), (error) => error instanceof TRPCError && error.code === code);
  });
}

test("users can sign out even without an accessible profile", async () => {
  const f = fixture({ missingProfile: true });
  assert.deepEqual(await f.caller.signOut(), { redirectTo: "/" });
  assert.equal(f.signedOut(), true);
});
