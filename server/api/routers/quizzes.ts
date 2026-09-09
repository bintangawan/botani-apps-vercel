import { TRPCError } from "@trpc/server";
import { z } from "zod";
import { quizInputSchema, quizTypeSchema } from "@/lib/validation/manage-forms";
import { throwDatabaseError } from "@/server/api/helpers";
import {
  createTRPCRouter,
  managerProcedure,
  protectedProcedure,
  studentProcedure,
} from "@/server/api/trpc";
import type { Json } from "@/types/database";

export const questionInputSchema = z.object({
  id: z.number().int().positive().optional(),
  quizId: z.number().int().positive(),
  questionText: z.string().trim().min(2).max(20000),
  questionType: z.enum(["pilihan_ganda", "esai"]),
  scoreWeight: z.number().int().min(1).max(100),
  options: z.array(z.string().trim().min(1).max(5000)).max(10).default([]),
  correctOptionIndex: z.number().int().min(0).default(0),
}).superRefine((input, context) => {
  if (input.questionType === "pilihan_ganda" && input.options.length < 2) {
    context.addIssue({ code: "custom", path: ["options"], message: "Pilihan ganda membutuhkan minimal dua opsi." });
  }
  if (input.questionType === "pilihan_ganda" && input.correctOptionIndex >= input.options.length) {
    context.addIssue({ code: "custom", path: ["correctOptionIndex"], message: "Kunci jawaban tidak valid." });
  }
});

const resultSchema = z.object({
  attempt: z.object({
    id: z.number(),
    user_id: z.string(),
    quiz_id: z.number(),
    started_at: z.string(),
    completed_at: z.string().nullable(),
    score: z.number().nullable(),
    total_correct: z.number(),
    created_at: z.string(),
    updated_at: z.string(),
  }),
  quiz: z.object({
    id: z.number(),
    module_id: z.number(),
    title: z.string(),
    quiz_type: quizTypeSchema,
    passing_score: z.number(),
    duration: z.number(),
    status: z.enum(["draft", "published"]),
    created_at: z.string(),
    updated_at: z.string(),
  }),
  module: z.object({
    id: z.number(),
    title: z.string(),
    slug: z.string(),
    description: z.string(),
    module_order: z.number(),
    estimated_minutes: z.number(),
    chapter_summary: z.unknown(),
    source_file: z.string().nullable(),
    status: z.enum(["draft", "published"]),
    created_at: z.string(),
    updated_at: z.string(),
  }),
  questions: z.array(z.object({
    question: z.object({
      id: z.number(),
      quiz_id: z.number(),
      question_text: z.string(),
      question_type: z.enum(["pilihan_ganda", "esai", "multiple_choice", "true_false"]),
      score_weight: z.number(),
      created_at: z.string(),
      updated_at: z.string(),
    }),
    options: z.array(z.object({
      id: z.number(),
      question_id: z.number(),
      option_text: z.string(),
      created_at: z.string(),
      updated_at: z.string(),
    })),
    answer: z.object({
      id: z.number(),
      attempt_id: z.number(),
      question_id: z.number(),
      selected_option_id: z.number().nullable(),
      essay_answer: z.string().nullable(),
      is_correct: z.boolean(),
      created_at: z.string(),
      updated_at: z.string(),
    }).nullable(),
    correct_option_id: z.number().nullable(),
  })),
});

function safeSearch(value: string): string {
  return value.replace(/[%_,().]/g, " ").replace(/\s+/g, " ").trim();
}

export const quizzesRouter = createTRPCRouter({
  listAvailable: studentProcedure
    .input(z.object({ moduleOrder: z.number().int().positive() }).optional())
    .query(async ({ ctx, input }) => {
      let moduleId: number | undefined;
      if (input?.moduleOrder) {
        const { data: selectedModule, error: moduleError } = await ctx.supabase
          .from("learning_modules")
          .select("id")
          .eq("module_order", input.moduleOrder)
          .maybeSingle();
        throwDatabaseError(moduleError, "Modul kuis gagal dimuat.");
        if (!selectedModule) {
          return [];
        }
        moduleId = selectedModule.id;
      }

      let quizzesQuery = ctx.supabase
        .from("quizzes")
        .select("*")
        .eq("status", "published");
      if (moduleId) {
        quizzesQuery = quizzesQuery.eq("module_id", moduleId);
      }
      const { data: quizzes, error } = await quizzesQuery.order("created_at", {
        ascending: false,
      });
      throwDatabaseError(error, "Daftar kuis gagal dimuat.");
      if (!quizzes?.length) {
        return [];
      }

      const moduleIds = [...new Set(quizzes.map((quiz) => quiz.module_id))];
      const quizIds = quizzes.map((quiz) => quiz.id);
      const [modules, questions, attempts] = await Promise.all([
        ctx.supabase
          .from("learning_modules")
          .select("id,title,slug,module_order")
          .in("id", moduleIds),
        ctx.supabase.from("questions").select("quiz_id").in("quiz_id", quizIds),
        ctx.supabase
          .from("quiz_attempts")
          .select("*")
          .eq("user_id", ctx.user.id)
          .in("quiz_id", quizIds)
          .order("created_at", { ascending: false }),
      ]);
      throwDatabaseError(modules.error, "Modul kuis gagal dimuat.");
      throwDatabaseError(questions.error, "Jumlah soal gagal dimuat.");
      throwDatabaseError(attempts.error, "Riwayat kuis gagal dimuat.");
      const moduleMap = new Map(
        (modules.data ?? []).map((module) => [module.id, module]),
      );

      return quizzes.map((quiz) => ({
        ...quiz,
        module: moduleMap.get(quiz.module_id) ?? null,
        questionCount: (questions.data ?? []).filter(
          (question) => question.quiz_id === quiz.id,
        ).length,
        attempts: (attempts.data ?? []).filter(
          (attempt) => attempt.quiz_id === quiz.id,
        ),
      }));
    }),

  start: studentProcedure.input(z.object({ quizId: z.number().int().positive() })).mutation(async ({ ctx, input }) => {
    const { data, error } = await ctx.supabase.rpc("start_quiz_attempt", { p_quiz_id: input.quizId });
    throwDatabaseError(error, "Kuis tidak dapat dimulai.");
    return { attemptId: data };
  }),

  attempt: studentProcedure.input(z.object({ attemptId: z.number().int().positive() })).query(async ({ ctx, input }) => {
    const { data: attempt, error } = await ctx.supabase
      .from("quiz_attempts")
      .select("*")
      .eq("id", input.attemptId)
      .eq("user_id", ctx.user.id)
      .maybeSingle();
    throwDatabaseError(error, "Percobaan kuis gagal dimuat.");
    if (!attempt) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Percobaan kuis tidak ditemukan." });
    }

    const { data: quiz, error: quizError } = await ctx.supabase.from("quizzes").select("*").eq("id", attempt.quiz_id).single();
    throwDatabaseError(quizError, "Kuis gagal dimuat.");
    if (!quiz) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Kuis tidak ditemukan." });
    }
    const [module, questions] = await Promise.all([
      ctx.supabase.from("learning_modules").select("id,title,slug").eq("id", quiz.module_id).single(),
      ctx.supabase.from("questions").select("*").eq("quiz_id", quiz.id).order("id"),
    ]);
    throwDatabaseError(module.error, "Modul kuis gagal dimuat.");
    throwDatabaseError(questions.error, "Soal kuis gagal dimuat.");
    if (!module.data) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Modul kuis tidak ditemukan." });
    }
    const questionIds = (questions.data ?? []).map((question) => question.id);
    const options = questionIds.length
      ? await ctx.supabase.from("question_options").select("*").in("question_id", questionIds).order("id")
      : { data: [], error: null };
    throwDatabaseError(options.error, "Pilihan jawaban gagal dimuat.");
    return {
      attempt,
      quiz,
      module: module.data,
      questions: (questions.data ?? []).map((question) => ({
        ...question,
        options: (options.data ?? []).filter((option) => option.question_id === question.id),
      })),
    };
  }),

  submit: studentProcedure
    .input(z.object({ attemptId: z.number().int().positive(), answers: z.record(z.string(), z.union([z.string(), z.number()])) }))
    .mutation(async ({ ctx, input }) => {
      const answers: Json = Object.fromEntries(Object.entries(input.answers).map(([key, value]) => [key, String(value)]));
      const { data, error } = await ctx.supabase.rpc("submit_quiz_attempt", {
        p_attempt_id: input.attemptId,
        p_answers: answers,
      });
      throwDatabaseError(error, "Jawaban kuis gagal dikirim.");
      return z.object({ attempt_id: z.number(), score: z.number(), total_correct: z.number() }).parse(data);
    }),

  result: protectedProcedure.input(z.object({ attemptId: z.number().int().positive() })).query(async ({ ctx, input }) => {
    const { data, error } = await ctx.supabase.rpc("get_quiz_result", { p_attempt_id: input.attemptId });
    throwDatabaseError(error, "Hasil kuis gagal dimuat.");
    return resultSchema.parse(data);
  }),

  manageList: managerProcedure
    .input(z.object({ search: z.string().trim().max(100).default(""), moduleId: z.number().int().positive().optional(), page: z.number().int().positive().default(1), pageSize: z.number().int().min(1).max(50).default(10) }))
    .query(async ({ ctx, input }) => {
      let query = ctx.supabase.from("quizzes").select("*", { count: "exact" });
      const search = safeSearch(input.search);
      if (search) {
        query = query.ilike("title", `%${search}%`);
      }
      if (input.moduleId) {
        query = query.eq("module_id", input.moduleId);
      }
      const from = (input.page - 1) * input.pageSize;
      const { data, error, count } = await query.order("created_at", { ascending: false }).range(from, from + input.pageSize - 1);
      throwDatabaseError(error, "Data kuis gagal dimuat.");
      const quizzes = data ?? [];
      const quizIds = quizzes.map((quiz) => quiz.id);
      const moduleIds = [...new Set(quizzes.map((quiz) => quiz.module_id))];
      const [modules, questions, attempts] = await Promise.all([
        moduleIds.length ? ctx.supabase.from("learning_modules").select("id,title").in("id", moduleIds) : Promise.resolve({ data: [], error: null }),
        quizIds.length ? ctx.supabase.from("questions").select("quiz_id").in("quiz_id", quizIds) : Promise.resolve({ data: [], error: null }),
        quizIds.length ? ctx.supabase.from("quiz_attempts").select("quiz_id").in("quiz_id", quizIds) : Promise.resolve({ data: [], error: null }),
      ]);
      throwDatabaseError(modules.error, "Modul kuis gagal dimuat.");
      throwDatabaseError(questions.error, "Jumlah soal gagal dimuat.");
      throwDatabaseError(attempts.error, "Jumlah attempt gagal dimuat.");
      const moduleMap = new Map((modules.data ?? []).map((module) => [module.id, module.title]));
      return {
        items: quizzes.map((quiz) => ({
          ...quiz,
          moduleTitle: moduleMap.get(quiz.module_id) ?? "—",
          questionCount: (questions.data ?? []).filter((item) => item.quiz_id === quiz.id).length,
          attemptCount: (attempts.data ?? []).filter((item) => item.quiz_id === quiz.id).length,
        })),
        total: count ?? 0,
        page: input.page,
        pageSize: input.pageSize,
      };
    }),

  manageById: managerProcedure.input(z.object({ id: z.number().int().positive() })).query(async ({ ctx, input }) => {
    const { data, error } = await ctx.supabase.from("quizzes").select("*").eq("id", input.id).maybeSingle();
    throwDatabaseError(error, "Kuis gagal dimuat.");
    if (!data) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Kuis tidak ditemukan." });
    }
    return data;
  }),

  save: managerProcedure.input(quizInputSchema).mutation(async ({ ctx, input }) => {
    const payload = {
      module_id: input.moduleId,
      title: input.title,
      quiz_type: input.quizType,
      passing_score: input.passingScore,
      duration: input.duration,
      status: input.status,
    };
    const result = input.id
      ? await ctx.supabase.from("quizzes").update(payload).eq("id", input.id).select("id").single()
      : await ctx.supabase.from("quizzes").insert(payload).select("id").single();
    throwDatabaseError(result.error, "Kuis gagal disimpan.");
    if (!result.data) {
      throw new TRPCError({ code: "INTERNAL_SERVER_ERROR", message: "ID kuis tidak diterima dari database." });
    }
    return { id: result.data.id };
  }),

  delete: managerProcedure.input(z.object({ id: z.number().int().positive() })).mutation(async ({ ctx, input }) => {
    const { error } = await ctx.supabase.from("quizzes").delete().eq("id", input.id);
    throwDatabaseError(error, "Kuis gagal dihapus.");
    return { success: true };
  }),

  questions: managerProcedure.input(z.object({ quizId: z.number().int().positive() })).query(async ({ ctx, input }) => {
    const [quiz, questions] = await Promise.all([
      ctx.supabase.from("quizzes").select("*").eq("id", input.quizId).maybeSingle(),
      ctx.supabase.from("questions").select("*").eq("quiz_id", input.quizId).order("id"),
    ]);
    throwDatabaseError(quiz.error, "Kuis gagal dimuat.");
    throwDatabaseError(questions.error, "Bank soal gagal dimuat.");
    if (!quiz.data) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Kuis tidak ditemukan." });
    }
    const questionIds = (questions.data ?? []).map((question) => question.id);
    const [options, keys] = await Promise.all([
      questionIds.length ? ctx.supabase.from("question_options").select("*").in("question_id", questionIds).order("id") : Promise.resolve({ data: [], error: null }),
      questionIds.length ? ctx.supabase.from("question_keys").select("*").in("question_id", questionIds) : Promise.resolve({ data: [], error: null }),
    ]);
    throwDatabaseError(options.error, "Pilihan jawaban gagal dimuat.");
    throwDatabaseError(keys.error, "Kunci jawaban gagal dimuat.");
    const keyMap = new Map((keys.data ?? []).map((key) => [key.question_id, key.correct_option_id]));
    return {
      quiz: quiz.data,
      questions: (questions.data ?? []).map((question) => ({
        ...question,
        options: (options.data ?? []).filter((option) => option.question_id === question.id),
        correctOptionId: keyMap.get(question.id) ?? null,
      })),
    };
  }),

  saveQuestion: managerProcedure.input(questionInputSchema).mutation(async ({ ctx, input }) => {
    const questionPayload = {
      quiz_id: input.quizId,
      question_text: input.questionText,
      question_type: input.questionType,
      score_weight: input.scoreWeight,
    };
    const questionResult = input.id
      ? await ctx.supabase.from("questions").update(questionPayload).eq("id", input.id).eq("quiz_id", input.quizId).select("id").single()
      : await ctx.supabase.from("questions").insert(questionPayload).select("id").single();
    throwDatabaseError(questionResult.error, "Soal gagal disimpan.");
    if (!questionResult.data) {
      throw new TRPCError({ code: "INTERNAL_SERVER_ERROR", message: "ID soal tidak diterima dari database." });
    }
    const questionId = questionResult.data.id;

    const deleteResult = await ctx.supabase.from("question_options").delete().eq("question_id", questionId);
    throwDatabaseError(deleteResult.error, "Pilihan jawaban lama gagal dibersihkan.");

    if (input.questionType === "pilihan_ganda") {
      const { data: options, error: optionsError } = await ctx.supabase
        .from("question_options")
        .insert(input.options.map((option) => ({ question_id: questionId, option_text: option })))
        .select("*");
      throwDatabaseError(optionsError, "Pilihan jawaban gagal disimpan.");
      const correctOption = options?.[input.correctOptionIndex];
      if (!correctOption) {
        throw new TRPCError({ code: "BAD_REQUEST", message: "Kunci jawaban tidak valid." });
      }
      const { error: keyError } = await ctx.supabase.from("question_keys").upsert({
        question_id: questionId,
        correct_option_id: correctOption.id,
      });
      throwDatabaseError(keyError, "Kunci jawaban gagal disimpan.");
    } else {
      const { error: keyError } = await ctx.supabase.from("question_keys").delete().eq("question_id", questionId);
      throwDatabaseError(keyError, "Kunci jawaban gagal dibersihkan.");
    }
    return { id: questionId };
  }),

  deleteQuestion: managerProcedure
    .input(z.object({ quizId: z.number().int().positive(), questionId: z.number().int().positive() }))
    .mutation(async ({ ctx, input }) => {
      const { error } = await ctx.supabase.from("questions").delete().eq("id", input.questionId).eq("quiz_id", input.quizId);
      throwDatabaseError(error, "Soal gagal dihapus.");
      return { success: true };
    }),
});
