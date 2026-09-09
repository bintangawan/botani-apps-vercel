import { TRPCError } from "@trpc/server";
import { and, count, desc, eq, ilike, or, sql, type SQL } from "drizzle-orm";
import { z } from "zod";
import { getDatabase } from "@/db/client";
import { profiles } from "@/db/schema";
import { createSupabaseAdminClient } from "@/lib/supabase/admin";
import { throwDatabaseError } from "@/server/api/helpers";
import { adminProcedure, createTRPCRouter } from "@/server/api/trpc";

export const usersRouter = createTRPCRouter({
  list: adminProcedure
    .input(z.object({ search: z.string().trim().max(100).default(""), role: z.enum(["all", "mahasiswa", "dosen", "admin"]).default("all"), page: z.number().int().positive().default(1), pageSize: z.number().int().min(1).max(50).default(10) }))
    .query(async ({ input }) => {
      const db = getDatabase();
      const filters: SQL[] = [];
      const search = input.search.trim();

      if (search) {
        const searchFilter = or(
          ilike(profiles.name, `%${search}%`),
          ilike(profiles.email, `%${search}%`),
          ilike(profiles.institution, `%${search}%`),
        );
        if (searchFilter) filters.push(searchFilter);
      }
      if (input.role !== "all") {
        filters.push(eq(profiles.role, input.role));
      }

      const where = filters.length > 0 ? and(...filters) : undefined;
      const offset = (input.page - 1) * input.pageSize;
      const [items, totals] = await Promise.all([
        db
          .select({
            id: profiles.id,
            name: profiles.name,
            email: profiles.email,
            role: profiles.role,
            institution: profiles.institution,
            status: profiles.status,
            created_at: profiles.createdAt,
            updated_at: profiles.updatedAt,
          })
          .from(profiles)
          .where(where)
          .orderBy(desc(profiles.createdAt))
          .limit(input.pageSize)
          .offset(offset),
        db.select({ value: count() }).from(profiles).where(where),
      ]);

      return { items, total: totals[0]?.value ?? 0, page: input.page, pageSize: input.pageSize };
    }),

  create: adminProcedure
    .input(z.object({
      name: z.string().trim().min(2).max(255),
      email: z.email().max(255),
      password: z.string().min(8).max(128),
      role: z.enum(["mahasiswa", "dosen", "admin"]),
      institution: z.string().trim().max(255).optional(),
      status: z.enum(["active", "inactive"]),
    }))
    .mutation(async ({ input }) => {
      const admin = createSupabaseAdminClient();
      const { data, error } = await admin.auth.admin.createUser({
        email: input.email,
        password: input.password,
        email_confirm: true,
        user_metadata: { name: input.name, institution: input.institution ?? "" },
      });
      if (error || !data.user) {
        throw new TRPCError({ code: "BAD_REQUEST", message: error?.message ?? "Akun gagal dibuat." });
      }
      const { error: profileError } = await admin.from("profiles").update({
        name: input.name,
        role: input.role,
        institution: input.institution ?? null,
        status: input.status,
      }).eq("id", data.user.id);
      if (profileError) {
        await admin.auth.admin.deleteUser(data.user.id);
        throwDatabaseError(profileError, "Profil pengguna gagal disimpan.");
      }
      return { id: data.user.id };
    }),

  update: adminProcedure
    .input(z.object({ id: z.uuid(), role: z.enum(["mahasiswa", "dosen", "admin"]), status: z.enum(["active", "inactive"]), institution: z.string().trim().max(255).nullable() }))
    .mutation(async ({ ctx, input }) => {
      if (input.id === ctx.user.id && input.status === "inactive") {
        throw new TRPCError({ code: "BAD_REQUEST", message: "Akun yang sedang digunakan tidak dapat dinonaktifkan." });
      }
      await getDatabase()
        .update(profiles)
        .set({ role: input.role, status: input.status, institution: input.institution, updatedAt: sql`now()` })
        .where(eq(profiles.id, input.id));
      return { success: true };
    }),

  delete: adminProcedure.input(z.object({ id: z.uuid() })).mutation(async ({ ctx, input }) => {
    if (input.id === ctx.user.id) {
      throw new TRPCError({ code: "BAD_REQUEST", message: "Akun yang sedang digunakan tidak dapat dihapus." });
    }
    const admin = createSupabaseAdminClient();
    const { error } = await admin.auth.admin.deleteUser(input.id);
    if (error) {
      throw new TRPCError({ code: "INTERNAL_SERVER_ERROR", message: "Akun gagal dihapus.", cause: error });
    }
    return { success: true };
  }),
});
