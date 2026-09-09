import { decoratePlantCards, throwDatabaseError } from "@/server/api/helpers";
import { adminProcedure, createTRPCRouter, managerProcedure, studentProcedure } from "@/server/api/trpc";

export const dashboardRouter = createTRPCRouter({
  admin: adminProcedure.query(async ({ ctx }) => {
    const [species, observations, modules, users, recentSpecies, recentUsers] = await Promise.all([
      ctx.supabase.from("plant_species").select("id", { count: "exact", head: true }),
      ctx.supabase.from("plant_observations").select("id", { count: "exact", head: true }),
      ctx.supabase.from("learning_modules").select("id", { count: "exact", head: true }),
      ctx.supabase.from("profiles").select("id", { count: "exact", head: true }),
      ctx.supabase.from("plant_species").select("*").order("created_at", { ascending: false }).limit(5),
      ctx.supabase.from("profiles").select("*").order("created_at", { ascending: false }).limit(5),
    ]);
    for (const result of [species, observations, modules, users, recentSpecies, recentUsers]) {
      throwDatabaseError(result.error, "Dashboard admin gagal dimuat.");
    }
    return {
      stats: {
        totalSpecies: species.count ?? 0,
        totalObservations: observations.count ?? 0,
        totalModules: modules.count ?? 0,
        totalUsers: users.count ?? 0,
      },
      recentSpecies: await decoratePlantCards(ctx.supabase, recentSpecies.data ?? []),
      recentUsers: recentUsers.data ?? [],
    };
  }),

  lecturer: managerProcedure.query(async ({ ctx }) => {
    const [students, attemptsCount, scoreRows, recentAttempts] = await Promise.all([
      ctx.supabase.from("profiles").select("id", { count: "exact", head: true }).eq("role", "mahasiswa"),
      ctx.supabase.from("quiz_attempts").select("id", { count: "exact", head: true }),
      ctx.supabase.from("quiz_attempts").select("score").not("score", "is", null),
      ctx.supabase.from("quiz_attempts").select("*").order("created_at", { ascending: false }).limit(10),
    ]);
    for (const result of [students, attemptsCount, scoreRows, recentAttempts]) {
      throwDatabaseError(result.error, "Dashboard dosen gagal dimuat.");
    }
    const attempts = recentAttempts.data ?? [];
    const userIds = [...new Set(attempts.map((attempt) => attempt.user_id))];
    const quizIds = [...new Set(attempts.map((attempt) => attempt.quiz_id))];
    const [profiles, quizzes] = await Promise.all([
      userIds.length ? ctx.supabase.from("profiles").select("id,name,email").in("id", userIds) : Promise.resolve({ data: [], error: null }),
      quizIds.length ? ctx.supabase.from("quizzes").select("id,title,module_id").in("id", quizIds) : Promise.resolve({ data: [], error: null }),
    ]);
    throwDatabaseError(profiles.error, "Nama mahasiswa gagal dimuat.");
    throwDatabaseError(quizzes.error, "Nama kuis gagal dimuat.");
    const profileMap = new Map((profiles.data ?? []).map((profile) => [profile.id, profile]));
    const quizMap = new Map((quizzes.data ?? []).map((quiz) => [quiz.id, quiz]));
    const scores = (scoreRows.data ?? []).flatMap((row) => row.score === null ? [] : [row.score]);
    const averageScore = scores.length ? scores.reduce((sum, score) => sum + score, 0) / scores.length : 0;

    return {
      totalStudents: students.count ?? 0,
      totalAttempts: attemptsCount.count ?? 0,
      averageScore,
      recentAttempts: attempts.map((attempt) => ({
        ...attempt,
        student: profileMap.get(attempt.user_id) ?? null,
        quiz: quizMap.get(attempt.quiz_id) ?? null,
      })),
    };
  }),

  student: studentProcedure.query(async ({ ctx }) => {
    const [modules, attempts] = await Promise.all([
      ctx.supabase.from("learning_modules").select("*").eq("status", "published").order("module_order"),
      ctx.supabase.from("quiz_attempts").select("*").eq("user_id", ctx.user.id).order("created_at", { ascending: false }).limit(5),
    ]);
    throwDatabaseError(modules.error, "Modul mahasiswa gagal dimuat.");
    throwDatabaseError(attempts.error, "Riwayat mahasiswa gagal dimuat.");
    const quizIds = [...new Set((attempts.data ?? []).map((attempt) => attempt.quiz_id))];
    const quizzes = quizIds.length
      ? await ctx.supabase.from("quizzes").select("id,title,module_id").in("id", quizIds)
      : { data: [], error: null };
    throwDatabaseError(quizzes.error, "Nama kuis gagal dimuat.");
    const quizMap = new Map((quizzes.data ?? []).map((quiz) => [quiz.id, quiz]));
    return {
      profile: ctx.profile,
      modules: modules.data ?? [],
      attempts: (attempts.data ?? []).map((attempt) => ({ ...attempt, quiz: quizMap.get(attempt.quiz_id) ?? null })),
    };
  }),
});
