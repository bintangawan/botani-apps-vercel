import "@/scripts/load-env";
import { createClient } from "@supabase/supabase-js";
import { getServiceEnv } from "@/lib/env";
import type { Database } from "@/types/database";

async function auditQuizzes(): Promise<void> {
  const env = getServiceEnv();
  const supabase = createClient<Database>(
    env.NEXT_PUBLIC_SUPABASE_URL,
    env.SUPABASE_SECRET_KEY,
    { auth: { persistSession: false, autoRefreshToken: false } },
  );

  const [modulesResult, quizzesResult, questionsResult, optionsResult, keysResult] =
    await Promise.all([
      supabase.from("learning_modules").select("id,module_order,title").order("module_order"),
      supabase.from("quizzes").select("id,module_id,title,status"),
      supabase.from("questions").select("id,quiz_id"),
      supabase.from("question_options").select("id,question_id"),
      supabase.from("question_keys").select("question_id"),
    ]);

  for (const result of [modulesResult, quizzesResult, questionsResult, optionsResult, keysResult]) {
    if (result.error) throw result.error;
  }

  const quizzes = quizzesResult.data ?? [];
  const questions = questionsResult.data ?? [];
  const options = optionsResult.data ?? [];
  const keyIds = new Set((keysResult.data ?? []).map((key) => key.question_id));
  const summary = (modulesResult.data ?? []).map((module) => {
    const moduleQuizzes = quizzes.filter((quiz) => quiz.module_id === module.id);
    const quizIds = new Set(moduleQuizzes.map((quiz) => quiz.id));
    const moduleQuestions = questions.filter((question) => quizIds.has(question.quiz_id));
    const questionIds = new Set(moduleQuestions.map((question) => question.id));
    return {
      module: module.module_order,
      title: module.title,
      quizzes: moduleQuizzes.length,
      questions: moduleQuestions.length,
      options: options.filter((option) => questionIds.has(option.question_id)).length,
      answerKeys: moduleQuestions.filter((question) => keyIds.has(question.id)).length,
    };
  });

  console.info(JSON.stringify(summary, null, 2));
}

auditQuizzes().catch((error: unknown) => {
  console.error(error instanceof Error ? error.message : error);
  process.exitCode = 1;
});
