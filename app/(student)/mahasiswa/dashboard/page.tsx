import Link from "next/link";
import { Suspense } from "react";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { formatDate } from "@/lib/utils";
import { api } from "@/server/api/server";

export default function StudentDashboardPage() {
  return (
    <Suspense
      fallback={
        <div className="page-container py-12 sm:py-16">
          <PageContentFallback label="Memuat dashboard mahasiswa" />
        </div>
      }
    >
      <StudentDashboardContent />
    </Suspense>
  );
}

async function StudentDashboardContent() {
  const data = await (await api()).dashboard.student();
  return (
    <div className="page-container py-12 sm:py-16">
      <header className="mb-10 flex flex-col items-center justify-between gap-6 rounded-md border border-emerald-300 bg-emerald-100 p-8 shadow-xl sm:flex-row sm:p-10">
        <div className="space-y-2 text-center sm:text-left"><span className="inline-flex rounded-md border border-emerald-300 bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">🎓 Panel Mahasiswa</span><h1 className="text-3xl font-extrabold text-slate-900">Selamat Datang, {data.profile.name}!</h1><p className="text-sm text-slate-700">{data.profile.institution ?? "Institusi belum diisi"} — Ikuti pembelajaran dan evaluasi Botani Phanerogamae.</p></div>
        <div className="flex shrink-0 flex-wrap justify-center gap-3"><Link href="/materi" className="btn-primary px-6 py-3">Buka Modul Teori</Link><Link href="/tumbuhan" className="btn-secondary px-6 py-3">Jelajahi Galeri</Link></div>
      </header>
      <div className="grid gap-8 lg:grid-cols-3">
        <section className="space-y-6 lg:col-span-2">
          <h2 className="border-b border-emerald-200 pb-3 text-xl font-bold text-slate-900">📚 Modul Pembelajaran &amp; Kuis Evaluasi</h2>
          <div className="space-y-4">{data.modules.map((module) => <article key={module.id} className="flex flex-col items-start justify-between gap-4 rounded-md border border-emerald-200 bg-white p-6 sm:flex-row sm:items-center"><div className="space-y-1"><p className="text-xs font-bold text-emerald-700">Modul #{module.module_order}</p><h3 className="text-lg font-bold text-slate-900">{module.title}</h3><p className="line-clamp-1 text-xs text-slate-600">{module.description}</p></div><div className="flex w-full shrink-0 gap-2 sm:w-auto"><Link href={`/materi/${module.slug}`} className="flex-1 rounded-md border border-emerald-300 bg-white px-4 py-2 text-center text-xs font-semibold text-emerald-700 hover:bg-emerald-100 sm:flex-initial">Baca Teori</Link><Link href={`/mahasiswa/quizzes?module=${module.module_order}`} className="flex-1 rounded-md border border-emerald-300 bg-emerald-100 px-4 py-2 text-center text-xs font-bold text-emerald-700 hover:bg-emerald-200 sm:flex-initial">Mulai Kuis Latihan</Link></div></article>)}</div>
        </section>
        <aside className="space-y-6"><h2 className="border-b border-emerald-200 pb-3 text-xl font-bold text-slate-900">📊 Riwayat Kuis Saya</h2><div className="space-y-4 rounded-md border border-emerald-200 bg-white p-6">{data.attempts.map((attempt) => <Link key={attempt.id} href={attempt.completed_at ? `/mahasiswa/quizzes/result/${attempt.id}` : `/mahasiswa/quizzes/attempt/${attempt.id}`} className="flex items-center justify-between rounded-md border border-emerald-200 bg-white p-3.5 hover:bg-emerald-100"><div><strong className="block text-sm text-slate-900">{attempt.quiz?.title ?? "Kuis Evaluasi"}</strong><span className="text-[10px] text-slate-600">{formatDate(attempt.started_at, true)}</span></div><span className="rounded-md bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">{attempt.completed_at ? `Skor: ${attempt.score ?? 0}` : "Berlangsung"}</span></Link>)}{!data.attempts.length && <div className="space-y-2 py-8 text-center"><span className="text-3xl">📝</span><p className="text-sm font-semibold text-slate-700">Belum Ada Riwayat Kuis</p><p className="text-xs text-slate-500">Anda belum mengikuti evaluasi apa pun.</p></div>}</div><div className="space-y-2 rounded-md border border-emerald-300 bg-emerald-100 p-6"><p className="text-xs font-bold uppercase text-amber-700">💡 Panduan Belajar</p><p className="text-xs leading-relaxed text-slate-700">Pelajari karakteristik taksonomi pada setiap kartu di <Link href="/tumbuhan" className="text-emerald-700 underline">Galeri Tumbuhan</Link> sebelum mengerjakan posttest akhir.</p></div></aside>
      </div>
    </div>
  );
}
