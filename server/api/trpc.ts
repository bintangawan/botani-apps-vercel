import { initTRPC, TRPCError } from "@trpc/server";
import { revalidateTag } from "next/cache";
import superjson from "superjson";
import { ZodError } from "zod";
import { PUBLIC_DATA_CACHE_TAG } from "@/server/cache-tags";
import type { UserRole } from "@/types/database";
import type { TRPCContext } from "@/server/api/context";

const t = initTRPC.context<TRPCContext>().create({
  transformer: superjson,
  errorFormatter({ shape, error }) {
    return {
      ...shape,
      data: {
        ...shape.data,
        zodError: error.cause instanceof ZodError ? error.cause.flatten() : null,
      },
    };
  },
});

export const createTRPCRouter = t.router;
export const createCallerFactory = t.createCallerFactory;
export const publicProcedure = t.procedure;

export const protectedProcedure = t.procedure.use(async ({ ctx, next }) => {
  if (!ctx.user || !ctx.profile) {
    throw new TRPCError({ code: "UNAUTHORIZED", message: "Silakan masuk terlebih dahulu." });
  }

  if (ctx.profile.status !== "active") {
    throw new TRPCError({ code: "FORBIDDEN", message: "Akun sedang dinonaktifkan." });
  }

  return next({
    ctx: {
      ...ctx,
      user: ctx.user,
      profile: ctx.profile,
    },
  });
});

function roleProcedure(roles: readonly UserRole[]) {
  return protectedProcedure.use(async ({ ctx, next }) => {
    if (!roles.includes(ctx.profile.role)) {
      throw new TRPCError({ code: "FORBIDDEN", message: "Role akun tidak memiliki akses." });
    }

    return next({ ctx });
  });
}

const revalidatePublicDataAfterMutation = t.middleware(async ({ next, type }) => {
  const result = await next();
  if (type === "mutation" && result.ok) {
    revalidateTag(PUBLIC_DATA_CACHE_TAG, "max");
  }
  return result;
});

export const studentProcedure = roleProcedure(["mahasiswa"]);
export const managerProcedure = roleProcedure(["admin", "dosen"]).use(
  revalidatePublicDataAfterMutation,
);
export const adminProcedure = roleProcedure(["admin"]);
