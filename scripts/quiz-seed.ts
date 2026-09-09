import { readFile } from "node:fs/promises";
import path from "node:path";
import type { SupabaseClient } from "@supabase/supabase-js";
import { z } from "zod";
import type { Database } from "@/types/database";

const quizOptionSchema = z.object({
  id: z.string().trim().min(1),
  text: z.string().trim().min(1),
});

const quizQuestionSchema = z.object({
  id: z.number().int().positive(),
  question: z.string().trim().min(2),
  options: z.array(quizOptionSchema).min(2),
  correctAnswer: z.string().trim().min(1),
  correctAnswerText: z.string().trim().min(1),
});

const quizFileSchema = z.object({
  module: z.number().int().min(1).max(9),
  title: z.string().trim().min(2),
  quizType: z.enum(["pilihan_ganda", "esai", "campuran", "pretest", "practice", "posttest"]),
  passingScore: z.number().int().min(0).max(100),
  duration: z.number().int().min(1).max(360),
  status: z.enum(["draft", "published"]),
  questions: z.array(quizQuestionSchema).length(10),
});

const quizIndexSchema = z.object({
  quizzes: z.array(z.object({
    module: z.number().int().min(1).max(9),
    file: z.string().trim().min(1),
  })).length(9),
});

type QuizSeed = z.infer<typeof quizFileSchema>;

async function readQuizSeeds(): Promise<QuizSeed[]> {
  const directory = path.join(process.cwd(), "kuis-data");
  const indexRaw = await readFile(path.join(directory, "quizzes.json"), "utf8");
  const index = quizIndexSchema.parse(JSON.parse(indexRaw) as unknown);
  const moduleNumbers = new Set(index.quizzes.map((entry) => entry.module));

  if (moduleNumbers.size !== 9) {
    throw new Error("Indeks kuis wajib memuat tepat satu kuis untuk setiap modul 1–9.");
  }

  const seeds = await Promise.all(index.quizzes.map(async (entry) => {
    const raw = await readFile(path.join(directory, entry.file), "utf8");
    const seed = quizFileSchema.parse(JSON.parse(raw) as unknown);
    if (seed.module !== entry.module) {
      throw new Error(`Nomor modul pada ${entry.file} tidak sesuai dengan indeks kuis.`);
    }

    const questionIds = new Set(seed.questions.map((question) => question.id));
    if (questionIds.size !== seed.questions.length) {
      throw new Error(`ID pertanyaan pada ${entry.file} harus unik.`);
    }

    for (const question of seed.questions) {
      const optionIds = new Set(question.options.map((option) => option.id));
      if (optionIds.size !== question.options.length) {
        throw new Error(`ID opsi pada soal ${question.id} di ${entry.file} harus unik.`);
      }
      const correctOption = question.options.find(
        (option) => option.id === question.correctAnswer,
      );
      if (!correctOption || correctOption.text !== question.correctAnswerText) {
        throw new Error(`Kunci jawaban soal ${question.id} di ${entry.file} tidak konsisten.`);
      }
    }

    return seed;
  }));

  return seeds.sort((left, right) => left.module - right.module);
}

async function seedQuiz(
  supabase: SupabaseClient<Database>,
  seed: QuizSeed,
): Promise<void> {
  const { data: module, error: moduleError } = await supabase
    .from("learning_modules")
    .select("id")
    .eq("module_order", seed.module)
    .single();
  if (moduleError) throw moduleError;

  const { data: existingQuiz, error: findQuizError } = await supabase
    .from("quizzes")
    .select("id")
    .eq("module_id", module.id)
    .eq("title", seed.title)
    .limit(1)
    .maybeSingle();
  if (findQuizError) throw findQuizError;

  const quizPayload = {
    module_id: module.id,
    title: seed.title,
    quiz_type: seed.quizType,
    passing_score: seed.passingScore,
    duration: seed.duration,
    status: seed.status,
  };
  const quizResult = existingQuiz
    ? await supabase.from("quizzes").update(quizPayload).eq("id", existingQuiz.id).select("id").single()
    : await supabase.from("quizzes").insert(quizPayload).select("id").single();
  if (quizResult.error) throw quizResult.error;

  const { data: existingQuestions, error: questionsError } = await supabase
    .from("questions")
    .select("id,question_text")
    .eq("quiz_id", quizResult.data.id);
  if (questionsError) throw questionsError;
  const questionMap = new Map(
    (existingQuestions ?? []).map((question) => [question.question_text, question.id]),
  );

  for (const question of seed.questions) {
    const existingQuestionId = questionMap.get(question.question);
    const questionPayload = {
      quiz_id: quizResult.data.id,
      question_text: question.question,
      question_type: "pilihan_ganda" as const,
      score_weight: 10,
    };
    const questionResult = existingQuestionId
      ? await supabase.from("questions").update(questionPayload).eq("id", existingQuestionId).select("id").single()
      : await supabase.from("questions").insert(questionPayload).select("id").single();
    if (questionResult.error) throw questionResult.error;

    const { data: existingOptions, error: optionsError } = await supabase
      .from("question_options")
      .select("id,option_text")
      .eq("question_id", questionResult.data.id);
    if (optionsError) throw optionsError;
    const optionMap = new Map(
      (existingOptions ?? []).map((option) => [option.option_text, option.id]),
    );
    const savedOptions = new Map<string, number>();

    for (const option of question.options) {
      const existingOptionId = optionMap.get(option.text);
      if (existingOptionId) {
        savedOptions.set(option.id, existingOptionId);
        continue;
      }

      const { data: savedOption, error: optionError } = await supabase
        .from("question_options")
        .insert({ question_id: questionResult.data.id, option_text: option.text })
        .select("id")
        .single();
      if (optionError) throw optionError;
      savedOptions.set(option.id, savedOption.id);
    }

    const correctOptionId = savedOptions.get(question.correctAnswer);
    if (!correctOptionId) {
      throw new Error(`Kunci jawaban soal ${question.id} pada modul ${seed.module} tidak ditemukan.`);
    }

    const { error: keyError } = await supabase.from("question_keys").upsert({
      question_id: questionResult.data.id,
      correct_option_id: correctOptionId,
    });
    if (keyError) throw keyError;
  }

  console.info(`Kuis modul ${seed.module} selesai: ${seed.title}`);
}

export async function seedQuizzes(
  supabase: SupabaseClient<Database>,
): Promise<void> {
  const seeds = await readQuizSeeds();
  for (const seed of seeds) await seedQuiz(supabase, seed);
  console.info("Seluruh kuis modul 1–9 berhasil disinkronkan.");
}
