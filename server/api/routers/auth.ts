import { TRPCError } from "@trpc/server";
import { z } from "zod";
import { createTRPCRouter, publicProcedure } from "@/server/api/trpc";
import { throwDatabaseError } from "@/server/api/helpers";

const credentialsSchema = z.object({
  email: z.string().trim().pipe(z.email().max(255)),
  password: z.string().min(1, "Masukkan kata sandi.").max(128),
});

function dashboardForRole(role: "mahasiswa" | "dosen" | "admin"): string {
  if (role === "admin") {
    return "/admin/dashboard";
  }
  if (role === "dosen") {
    return "/dosen/dashboard";
  }
  return "/mahasiswa/dashboard";
}

export const authRouter = createTRPCRouter({
  current: publicProcedure.query(({ ctx }) => ({
    user: ctx.user ? { id: ctx.user.id, email: ctx.user.email ?? "" } : null,
    profile: ctx.profile,
  })),

  signIn: publicProcedure.input(credentialsSchema).mutation(async ({ ctx, input }) => {
    const { data: authData, error } = await ctx.supabase.auth.signInWithPassword(input);
    if (error) {
      if (error.status === 429) {
        throw new TRPCError({ code: "TOO_MANY_REQUESTS", message: "Terlalu banyak percobaan masuk. Silakan tunggu beberapa saat." });
      }
      if (!error.status || error.status >= 500) {
        throw new TRPCError({ code: "SERVICE_UNAVAILABLE", message: "Layanan masuk belum dapat dihubungi. Silakan coba lagi." });
      }
      if (error.code === "email_not_confirmed") {
        throw new TRPCError({ code: "UNAUTHORIZED", message: "Konfirmasi email terlebih dahulu sebelum masuk." });
      }
      throw new TRPCError({ code: "UNAUTHORIZED", message: "Email atau kata sandi tidak sesuai." });
    }

    if (!authData.user) {
      throw new TRPCError({ code: "UNAUTHORIZED", message: "Sesi login tidak dapat dibuat." });
    }

    const { data: profile, error: profileError } = await ctx.supabase
      .from("profiles")
      .select("*")
      .eq("id", authData.user.id)
      .maybeSingle();

    if (profileError || !profile || profile.status !== "active") {
      await ctx.supabase.auth.signOut();
      throwDatabaseError(profileError, "Profil akun gagal dimuat. Silakan coba lagi.");
      if (!profile) {
        throw new TRPCError({ code: "FORBIDDEN", message: "Profil akun belum tersedia. Hubungi administrator." });
      }
      throw new TRPCError({ code: "FORBIDDEN", message: "Akun sedang dinonaktifkan." });
    }

    return { redirectTo: dashboardForRole(profile.role) };
  }),

  signUp: publicProcedure
    .input(credentialsSchema.extend({ password: z.string().min(8).max(128), name: z.string().trim().min(2).max(255), institution: z.string().trim().max(255).optional() }))
    .mutation(async ({ ctx, input }) => {
      const { createSupabaseAdminClient } = await import("@/lib/supabase/admin");
      const admin = createSupabaseAdminClient();
      const { data, error } = await admin.auth.admin.createUser({
        email: input.email,
        password: input.password,
        email_confirm: true,
        user_metadata: {
          name: input.name,
          institution: input.institution ?? "",
        },
      });

      if (error || !data.user) {
        throw new TRPCError({ code: "BAD_REQUEST", message: error?.message ?? "Akun gagal dibuat." });
      }

      const { data: sessionData, error: sessionError } = await ctx.supabase.auth.signInWithPassword({
        email: input.email,
        password: input.password,
      });

      return {
        signedIn: !sessionError && Boolean(sessionData.user),
        redirectTo: !sessionError && sessionData.user ? "/mahasiswa/dashboard" : "/login?registered=1",
      };
    }),

  signOut: publicProcedure.mutation(async ({ ctx }) => {
    const { error } = await ctx.supabase.auth.signOut();
    if (error) {
      throw new TRPCError({ code: "INTERNAL_SERVER_ERROR", message: "Gagal keluar dari akun." });
    }
    return { redirectTo: "/" };
  }),
});
