import Link from "next/link";
import { Award, ListChecks } from "lucide-react";
import { Suspense } from "react";
import { StartQuizButton } from "@/components/quiz/start-quiz-button";
import { EmptyState } from "@/components/ui/empty-state";
import { PageHeader } from "@/components/ui/page-header";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";

type StudentQuizzesPageProps = {
  searchParams: Promise<{ module?: string | string[] }>;
};

export default function StudentQuizzesPage({ searchParams }: StudentQuizzesPageProps) {
  return (
    <Suspense
      fallback={
        <div className="page-container py-12 sm:py-16">
          <PageContentFallback label="Memuat daftar kuis" />
        </div>
      }
    >
      <StudentQuizzesContent searchParams={searchParams} />
    </Suspense>
  );
}

async function StudentQuizzesContent({ searchParams }: StudentQuizzesPageProps) {
  const params = await searchParams;
  const requestedModule = Number(Array.isArray(params.module) ? params.module[0] : params.module);
  const moduleOrder = Number.isInteger(requestedModule) && requestedModule > 0
    ? requestedModule
    : undefined;
  const quizzes = await (await api()).quizzes.listAvailable(
    moduleOrder ? { moduleOrder } : undefined,
  );
  return (
    <div className="page-container py-12 sm:py-16">
      <PageHeader eyebrow="🎓 Pusat Ujian & Kuis Evaluasi" title="Evaluasi Pemahaman Botani Phanerogamae" description="Uji pemahaman mengenai klasifikasi, karakteristik morfologi, serta taksonomi tumbuhan tingkat tinggi." actions={<Link href="/mahasiswa/dashboard" className="btn-secondary">← Dashboard Mahasiswa</Link>} />
      {quizzes.length ? <div className="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">{quizzes.map((quiz) => { const latest = quiz.attempts[0]; return <article key={quiz.id} className="group flex flex-col rounded-md border border-emerald-200 bg-white p-6 shadow-xl transition-all hover:border-emerald-300 sm:p-8"><div className="flex items-start justify-between gap-3"><p className="rounded-md border border-emerald-300 bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{quiz.module?.title ?? "Evaluasi Umum"}</p><span className="shrink-0 font-mono text-xs text-slate-600">⏱️ {quiz.duration}m</span></div><h2 className="mt-4 text-xl font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">{quiz.title}</h2><div className="mt-5 grid grid-cols-2 gap-3 text-center"><div className="rounded-md border border-emerald-200 bg-white p-3"><ListChecks className="mx-auto size-4 text-emerald-600" /><p className="mt-1 text-xs font-bold">{quiz.questionCount} soal</p></div><div className="rounded-md border border-amber-200 bg-white p-3 text-amber-700"><Award className="mx-auto size-4" /><p className="mt-1 text-xs font-bold">KKM {quiz.passing_score}</p></div></div>{latest?.completed_at && <p className="mt-4 rounded-md border border-emerald-200 bg-white p-3.5 text-sm text-slate-600">Nilai terakhir: <strong className="text-emerald-700">{latest.score ?? 0} / 100</strong></p>}<div className="mt-auto border-t border-emerald-200 pt-6"><StartQuizButton quizId={quiz.id} disabled={quiz.questionCount === 0} /></div></article>; })}</div> : <div className="mt-8"><EmptyState title="Belum ada kuis" description="Kuis yang diterbitkan dosen akan tampil di sini." /></div>}
    </div>
  );
}
