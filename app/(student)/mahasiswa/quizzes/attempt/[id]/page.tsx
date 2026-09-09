import { redirect } from "next/navigation";
import { connection } from "next/server";
import { Suspense } from "react";
import { QuizAttemptForm } from "@/components/quiz/quiz-attempt-form";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

type AttemptPageProps = { params: Promise<{ id: string }> };

export default function AttemptPage({ params }: AttemptPageProps) {
  return (
    <Suspense
      fallback={
        <div className="page-container py-10">
          <PageContentFallback label="Memuat soal kuis" maxWidth="4xl" />
        </div>
      }
    >
      <AttemptContent params={params} />
    </Suspense>
  );
}

async function AttemptContent({ params }: AttemptPageProps) {
  await connection();
  await requireRoles(["mahasiswa"]);
  const attemptId = Number((await params).id);
  if (!Number.isInteger(attemptId) || attemptId <= 0) redirect("/mahasiswa/quizzes");
  const data = await (await api()).quizzes.attempt({ attemptId });
  if (data.attempt.completed_at) redirect(`/mahasiswa/quizzes/result/${attemptId}`);
  return <div className="page-container max-w-4xl py-10"><p className="eyebrow">{data.module.title}</p><h1 className="mt-2 text-3xl font-black text-slate-950">{data.quiz.title}</h1><p className="mt-3 text-sm text-slate-600">Jawaban akan dinilai otomatis untuk soal pilihan ganda.</p><div className="mt-8"><QuizAttemptForm attemptId={attemptId} startedAt={data.attempt.started_at} durationMinutes={data.quiz.duration} questions={data.questions} /></div></div>;
}
