import Link from "next/link";
import { CheckCircle2, XCircle } from "lucide-react";
import { connection } from "next/server";
import { Suspense } from "react";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { formatDate } from "@/lib/utils";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

type ResultPageProps = { params: Promise<{ id: string }> };

export default function ResultPage({ params }: ResultPageProps) {
  return (
    <Suspense
      fallback={
        <div className="px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
          <PageContentFallback label="Memuat hasil kuis" maxWidth="5xl" />
        </div>
      }
    >
      <ResultContent params={params} />
    </Suspense>
  );
}

async function ResultContent({ params }: ResultPageProps) {
  await connection();
  await requireRoles(["mahasiswa"]);
  const attemptId = Number((await params).id);
  const data = await (await api()).quizzes.result({ attemptId });
  const score = data.attempt.score ?? 0;
  const passed = score >= data.quiz.passing_score;
  const totalCorrect = data.questions.filter((item) => item.answer?.is_correct).length;
  return (
    <div className="mx-auto max-w-5xl space-y-12 px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
      <section className="space-y-6 rounded-md border border-emerald-300 bg-emerald-100 p-8 text-center shadow-lg sm:p-12">
        <span className="inline-block rounded-md border border-emerald-300 bg-emerald-100 px-3.5 py-1.5 text-xs font-bold text-emerald-700">🎉 Rekapitulasi &amp; Evaluasi Nilai Akhir</span>
        <h1 className="text-3xl font-extrabold text-slate-900 sm:text-4xl">{data.quiz.title}</h1>
        <p className="text-sm text-slate-600">{data.module.title}</p>
        <div className="flex flex-col items-center justify-center gap-2 py-6"><div className={`flex size-36 flex-col items-center justify-center rounded-md border-4 shadow-lg sm:size-44 ${passed ? "border-emerald-500 bg-emerald-50 shadow-emerald-900/10" : "border-rose-500 bg-rose-50 shadow-rose-500/30"}`}><strong className="text-4xl font-extrabold text-slate-900 sm:text-5xl">{score}</strong><span className="mt-1 text-xs font-bold uppercase text-slate-700">/ 100 Poin</span></div><span className={`mt-4 inline-flex rounded-md border px-5 py-2 text-sm font-extrabold shadow-lg ${passed ? "border-emerald-300 bg-emerald-100 text-emerald-700" : "border-rose-300 bg-rose-100 text-rose-700"}`}>{passed ? `✓ LULUS (Ketuntasan Minimal: ${data.quiz.passing_score}%)` : `× BELUM TUNTAS (KKM: ${data.quiz.passing_score}%)`}</span></div>
        <div className="mx-auto grid max-w-2xl grid-cols-2 gap-4 pt-4 text-left sm:grid-cols-4"><div className="rounded-md border border-emerald-200 bg-white p-4"><span className="block text-[10px] font-bold uppercase text-slate-600">Total Benar</span><strong className="text-lg text-emerald-700">{totalCorrect} Soal</strong></div><div className="rounded-md border border-emerald-200 bg-white p-4"><span className="block text-[10px] font-bold uppercase text-slate-600">Salah / Kosong</span><strong className="text-lg text-rose-700">{data.questions.length - totalCorrect} Soal</strong></div><div className="rounded-md border border-emerald-200 bg-white p-4"><span className="block text-[10px] font-bold uppercase text-slate-600">Nilai Lulus</span><strong className="text-sm text-slate-900">{data.quiz.passing_score} Poin</strong></div><div className="rounded-md border border-emerald-200 bg-white p-4"><span className="block text-[10px] font-bold uppercase text-slate-600">Selesai</span><strong className="text-xs text-slate-900">{formatDate(data.attempt.completed_at, true)}</strong></div></div>
        <div className="flex flex-wrap items-center justify-center gap-4 border-t border-emerald-200 pt-6"><Link href="/mahasiswa/quizzes" className="btn-secondary">← Kembali ke Daftar Kuis</Link><Link href={`/materi/${data.module.slug}`} className="btn-primary">Pelajari Materi Lagi →</Link></div>
      </section>
      <section className="space-y-6"><div className="flex items-center justify-between border-b border-emerald-200 pb-4"><h2 className="text-xl font-extrabold text-slate-900">📝 Pembahasan &amp; Tinjauan Soal</h2><span className="text-xs text-slate-600">Total {data.questions.length} Pertanyaan</span></div><div className="space-y-6">{data.questions.map((item, index) => { const selected = item.options.find((option) => option.id === item.answer?.selected_option_id); const correct = item.options.find((option) => option.id === item.correct_option_id); return <article key={item.question.id} className={`space-y-6 rounded-md border bg-white p-6 shadow-xl sm:p-8 ${item.answer?.is_correct ? "border-emerald-300" : "border-rose-300"}`}><div className="flex items-start gap-3 border-b border-emerald-200 pb-4">{item.answer?.is_correct ? <CheckCircle2 className="size-7 shrink-0 text-emerald-600" /> : <XCircle className="size-7 shrink-0 text-rose-600" />}<div><p className={`text-xs font-bold uppercase ${item.answer?.is_correct ? "text-emerald-700" : "text-rose-700"}`}>Soal #{index + 1} · {item.answer?.is_correct ? "Benar" : "Salah"}</p><h3 className="mt-2 text-base font-medium leading-relaxed text-slate-900 sm:text-lg">{item.question.question_text}</h3></div></div><div className="grid gap-3 text-sm sm:grid-cols-2"><div className="rounded-md border border-emerald-200 bg-emerald-50 p-4"><span className="block text-[10px] font-bold uppercase text-slate-500">Jawaban Anda</span><strong className="mt-1 block text-slate-900">{item.question.question_type === "esai" ? (item.answer?.essay_answer ?? "Tidak dijawab") : (selected?.option_text ?? "Tidak dijawab")}</strong></div>{correct && <div className="rounded-md border border-emerald-400 bg-emerald-100 p-4"><span className="block text-[10px] font-bold uppercase text-emerald-700">Kunci Benar</span><strong className="mt-1 block text-slate-900">{correct.option_text}</strong></div>}</div>{item.question.question_type === "esai" && <p className="text-xs text-amber-700">Jawaban esai belum dinilai otomatis.</p>}</article>; })}</div></section>
    </div>
  );
}
