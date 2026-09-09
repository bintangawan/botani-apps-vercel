import { TRPCError } from "@trpc/server";
import { z } from "zod";
import { slugify } from "@/lib/utils";
import { moduleInputSchema } from "@/lib/validation/manage-forms";
import { createUniqueSlug, decoratePlantCards, throwDatabaseError } from "@/server/api/helpers";
import { createTRPCRouter, managerProcedure, publicProcedure } from "@/server/api/trpc";
import type { Json } from "@/types/database";

function safeSearch(value: string): string {
  return value.replace(/[%_,().]/g, " ").replace(/\s+/g, " ").trim();
}

async function attachModuleCounts(
  supabase: Parameters<typeof decoratePlantCards>[0],
  modules: Awaited<ReturnType<typeof getModuleRows>>,
) {
  const ids = modules.map((module) => module.id);
  if (ids.length === 0) {
    return [];
  }
  const [lessons, quizzes, species] = await Promise.all([
    supabase.from("learning_lessons").select("module_id").in("module_id", ids),
    supabase.from("quizzes").select("module_id").in("module_id", ids),
    supabase.from("module_species").select("module_id").in("module_id", ids),
  ]);
  throwDatabaseError(lessons.error, "Jumlah subbab gagal dimuat.");
  if (quizzes.error && quizzes.error.code !== "42501") {
    throwDatabaseError(quizzes.error, "Jumlah kuis gagal dimuat.");
  }
  throwDatabaseError(species.error, "Jumlah spesies terkait gagal dimuat.");

  return modules.map((module) => ({
    ...module,
    lessonCount: (lessons.data ?? []).filter((item) => item.module_id === module.id).length,
    quizCount: (quizzes.data ?? []).filter((item) => item.module_id === module.id).length,
    speciesCount: (species.data ?? []).filter((item) => item.module_id === module.id).length,
  }));
}

async function getModuleRows(
  supabase: Parameters<typeof decoratePlantCards>[0],
  publishedOnly: boolean,
) {
  let query = supabase.from("learning_modules").select("*");
  if (publishedOnly) {
    query = query.eq("status", "published");
  }
  const { data, error } = await query.order("module_order");
  throwDatabaseError(error, "Modul pembelajaran gagal dimuat.");
  return data ?? [];
}

export const modulesRouter = createTRPCRouter({
  selectOptions: managerProcedure.query(async ({ ctx }) => {
    const { data, error } = await ctx.supabase
      .from("learning_modules")
      .select("id,title,module_order")
      .order("module_order");
    throwDatabaseError(error, "Daftar pilihan modul gagal dimuat.");
    return data ?? [];
  }),

  listPublished: publicProcedure.query(async ({ ctx }) => {
    const { data, error } = await ctx.supabase.rpc("get_published_modules_with_counts");
    throwDatabaseError(error, "Modul pembelajaran gagal dimuat.");
    return (data ?? []).map(({ lesson_count, quiz_count, species_count, ...module }) => ({
      ...module,
      lessonCount: Number(lesson_count),
      quizCount: Number(quiz_count),
      speciesCount: Number(species_count),
    }));
  }),

  outline: publicProcedure.query(async ({ ctx }) => {
    const modules = await getModuleRows(ctx.supabase, true);
    if (modules.length === 0) {
      return [];
    }
    const { data: lessons, error } = await ctx.supabase
      .from("learning_lessons")
      .select("id,module_id,title,slug,lesson_order")
      .in("module_id", modules.map((module) => module.id))
      .order("lesson_order");
    throwDatabaseError(error, "Daftar isi materi gagal dimuat.");
    return modules.map((module) => ({
      ...module,
      lessons: (lessons ?? []).filter((lesson) => lesson.module_id === module.id),
    }));
  }),

  bySlug: publicProcedure
    .input(z.object({ slug: z.string().min(1).max(255), lessonSlug: z.string().max(255).optional() }))
    .query(async ({ ctx, input }) => {
      const { data: module, error } = await ctx.supabase
        .from("learning_modules")
        .select("*")
        .eq("slug", input.slug)
        .eq("status", "published")
        .maybeSingle();
      throwDatabaseError(error, "Bab pembelajaran gagal dimuat.");
      if (!module) {
        throw new TRPCError({ code: "NOT_FOUND", message: "Bab pembelajaran tidak ditemukan." });
      }

      const [lessonsResult, outline, speciesLinksResult] = await Promise.all([
        ctx.supabase.from("learning_lessons").select("*").eq("module_id", module.id).order("lesson_order"),
        getModuleRows(ctx.supabase, true),
        ctx.supabase.from("module_species").select("species_id").eq("module_id", module.id),
      ]);
      throwDatabaseError(lessonsResult.error, "Subbab pembelajaran gagal dimuat.");
      throwDatabaseError(speciesLinksResult.error, "Spesies terkait gagal dimuat.");

      const lessons = lessonsResult.data ?? [];
      const selectedLesson = input.lessonSlug
        ? lessons.find((lesson) => lesson.slug === input.lessonSlug)
        : lessons[0];
      if (!selectedLesson) {
        throw new TRPCError({ code: "NOT_FOUND", message: "Subbab pembelajaran tidak ditemukan." });
      }

      const allModuleIds = outline.map((item) => item.id);
      const { data: allLessons, error: allLessonsError } = allModuleIds.length
        ? await ctx.supabase.from("learning_lessons").select("id,module_id,title,slug,lesson_order").in("module_id", allModuleIds).order("lesson_order")
        : { data: [], error: null };
      throwDatabaseError(allLessonsError, "Navigasi subbab gagal dimuat.");

      const navigation = outline.flatMap((chapter) =>
        (allLessons ?? [])
          .filter((lesson) => lesson.module_id === chapter.id)
          .map((lesson) => ({
            id: lesson.id,
            moduleSlug: chapter.slug,
            moduleTitle: chapter.title,
            lessonSlug: lesson.slug,
            lessonTitle: lesson.title,
          })),
      );
      const position = navigation.findIndex((item) => item.id === selectedLesson.id);
      const speciesIds = (speciesLinksResult.data ?? []).map((item) => item.species_id);
      const relatedSpecies = speciesIds.length
        ? await ctx.supabase.from("plant_species").select("*").in("id", speciesIds).eq("status", "published")
        : { data: [], error: null };
      throwDatabaseError(relatedSpecies.error, "Spesies terkait gagal dimuat.");

      return {
        module,
        lesson: selectedLesson,
        lessons,
        outline: await Promise.all(outline.map(async (chapter) => ({
          ...chapter,
          lessons: (allLessons ?? []).filter((lesson) => lesson.module_id === chapter.id),
        }))),
        previous: position > 0 ? navigation[position - 1] : null,
        next: position >= 0 && position < navigation.length - 1 ? navigation[position + 1] : null,
        lessonNumber: position + 1,
        totalLessons: navigation.length,
        species: await decoratePlantCards(ctx.supabase, relatedSpecies.data ?? []),
      };
    }),

  manageList: managerProcedure
    .input(z.object({ search: z.string().trim().max(100).default(""), page: z.number().int().positive().default(1), pageSize: z.number().int().min(1).max(50).default(10) }))
    .query(async ({ ctx, input }) => {
      let query = ctx.supabase.from("learning_modules").select("*", { count: "exact" });
      const search = safeSearch(input.search);
      if (search) {
        query = query.or(`title.ilike.%${search}%,description.ilike.%${search}%`);
      }
      const from = (input.page - 1) * input.pageSize;
      const { data, error, count } = await query.order("module_order").range(from, from + input.pageSize - 1);
      throwDatabaseError(error, "Data pengelolaan bab gagal dimuat.");
      return { items: await attachModuleCounts(ctx.supabase, data ?? []), total: count ?? 0, page: input.page, pageSize: input.pageSize };
    }),

  manageSuggestions: managerProcedure
    .input(z.object({ search: z.string().trim().min(2).max(100) }))
    .query(async ({ ctx, input }) => {
      const search = safeSearch(input.search);
      if (!search) return [];
      const { data, error } = await ctx.supabase
        .from("learning_modules")
        .select("id,title,module_order,status")
        .or(`title.ilike.%${search}%,description.ilike.%${search}%`)
        .order("module_order")
        .limit(6);
      throwDatabaseError(error, "Saran pencarian modul gagal dimuat.");
      return data ?? [];
    }),

  manageById: managerProcedure.input(z.object({ id: z.number().int().positive() })).query(async ({ ctx, input }) => {
    const { data: module, error } = await ctx.supabase.from("learning_modules").select("*").eq("id", input.id).maybeSingle();
    throwDatabaseError(error, "Bab gagal dimuat.");
    if (!module) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Bab tidak ditemukan." });
    }
    const [lessons, species] = await Promise.all([
      ctx.supabase.from("learning_lessons").select("*").eq("module_id", module.id).order("lesson_order"),
      ctx.supabase.from("module_species").select("species_id").eq("module_id", module.id),
    ]);
    throwDatabaseError(lessons.error, "Subbab gagal dimuat.");
    throwDatabaseError(species.error, "Relasi spesies gagal dimuat.");
    return { ...module, lessons: lessons.data ?? [], speciesIds: (species.data ?? []).map((item) => item.species_id) };
  }),

  nextOrder: managerProcedure.query(async ({ ctx }) => {
    const { data, error } = await ctx.supabase.from("learning_modules").select("module_order").order("module_order", { ascending: false }).limit(1).maybeSingle();
    throwDatabaseError(error, "Urutan bab berikutnya gagal dihitung.");
    return (data?.module_order ?? 0) + 1;
  }),

  save: managerProcedure.input(moduleInputSchema).mutation(async ({ ctx, input }) => {
    const slug = await createUniqueSlug(ctx.supabase, "learning_modules", input.title, input.id);
    const sortedLessons = [...input.lessons].sort((left, right) => left.lessonOrder - right.lessonOrder);
    const usedSlugs = new Set<string>();
    const lessons: Json[] = sortedLessons.map((lesson, index) => {
      let lessonSlug = slugify(lesson.slug || lesson.title) || `subbab-${index + 1}`;
      let suffix = 2;
      const base = lessonSlug;
      while (usedSlugs.has(lessonSlug)) {
        lessonSlug = `${base}-${suffix}`;
        suffix += 1;
      }
      usedSlugs.add(lessonSlug);
      return {
        source_id: lesson.sourceId,
        title: lesson.title,
        slug: lessonSlug,
        content: lesson.content,
        content_format: "markdown",
        key_points: lesson.keyPoints,
        source_sections: lesson.sourceSections,
        lesson_order: index + 1,
      };
    });
    const payload: Json = {
      title: input.title,
      slug,
      description: input.description,
      module_order: input.moduleOrder,
      estimated_minutes: input.estimatedMinutes,
      chapter_summary: input.chapterSummary,
      source_file: input.sourceFile,
      status: input.status,
    };
    const { data, error } = await ctx.supabase.rpc("save_learning_module", {
      p_module_id: input.id ?? null,
      p_payload: payload,
      p_lessons: lessons,
      p_species_ids: input.speciesIds,
    });
    throwDatabaseError(error, "Bab dan subbab gagal disimpan.");
    return { id: data, slug };
  }),

  delete: managerProcedure.input(z.object({ id: z.number().int().positive() })).mutation(async ({ ctx, input }) => {
    const { error } = await ctx.supabase.from("learning_modules").delete().eq("id", input.id);
    throwDatabaseError(error, "Bab pembelajaran gagal dihapus.");
    return { success: true };
  }),
});
