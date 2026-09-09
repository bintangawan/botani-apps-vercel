import Link from "next/link";
import { Suspense } from "react";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { formatDate } from "@/lib/utils";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

export default function LecturerDashboardPage() {
  return (
    <Suspense fallback={<PageContentFallback label="Memuat dashboard dosen" />}>
      <LecturerDashboardContent />
    </Suspense>
  );
}

async function LecturerDashboardContent() {
  await requireRoles(["dosen"]);
  const data = await (await api()).dashboard.lecturer();
  return (
    <div className="mx-auto max-w-7xl py-6 sm:py-8">
      <header className="mb-8 flex flex-col items-center justify-between gap-6 rounded-md border border-emerald-300 bg-emerald-100 p-8 shadow-xl sm:flex-row sm:p-10"><div className="space-y-2 text-center sm:text-left"><span className="inline-flex rounded-md border border-teal-300 bg-teal-100 px-3 py-1 text-xs font-bold text-teal-700">👨‍🏫 Panel Dosen Pengampu</span><h1 className="text-3xl font-extrabold text-slate-900">Dashboard Monitoring Pembelajaran</h1><p className="max-w-2xl text-sm text-slate-700">Pantau analitik evaluasi mahasiswa dan kustomisasi materi deskripsi ilmiah spesimen tumbuhan.</p></div><div className="flex shrink-0 flex-wrap items-center justify-center gap-3"><Link href="/manage/plants/new" className="btn-primary">+ Tambah Tumbuhan</Link><Link href="/manage/modules" className="btn-secondary">📚 Modul Pembelajaran</Link><Link href="/manage/quizzes" className="btn-secondary border-amber-300 text-amber-700 hover:bg-amber-100">🎯 Bank Soal &amp; Kuis</Link></div></header>
      <div className="mb-10 grid gap-6 sm:grid-cols-3"><article className="rounded-md border border-emerald-200 bg-white p-6 shadow-lg"><strong className="mb-1 block text-4xl font-extrabold text-slate-900">{data.totalStudents}</strong><span className="text-xs font-bold uppercase tracking-wider text-emerald-700">Total Mahasiswa Terdaftar</span></article><article className="rounded-md border border-emerald-200 bg-white p-6 shadow-lg"><strong className="mb-1 block text-4xl font-extrabold text-amber-700">{data.totalAttempts}</strong><span className="text-xs font-bold uppercase tracking-wider text-slate-700">Sesi Kuis Dikerjakan</span></article><article className="rounded-md border border-emerald-200 bg-white p-6 shadow-lg"><strong className="mb-1 block text-4xl font-extrabold text-teal-700">{data.averageScore.toFixed(1)} / 100</strong><span className="text-xs font-bold uppercase tracking-wider text-slate-700">Rata-rata Nilai Mahasiswa</span></article></div>
      <section className="space-y-6 rounded-md border border-emerald-200 bg-white p-6 sm:p-8"><div className="flex items-center justify-between border-b border-emerald-200 pb-4"><h2 className="text-xl font-bold text-slate-900">📈 Aktivitas Evaluasi Terakhir Mahasiswa</h2><span className="hidden text-xs text-slate-600 sm:block">Menampilkan percobaan terakhir</span></div><div className="overflow-x-auto"><table className="w-full border-collapse text-left text-sm"><thead><tr className="border-b border-emerald-200 text-xs uppercase tracking-wider text-emerald-700"><th className="px-4 py-3 font-semibold">Mahasiswa</th><th className="px-4 py-3 font-semibold">Modul / Kuis</th><th className="px-4 py-3 font-semibold">Skor Peroleh</th><th className="px-4 py-3 font-semibold">Waktu Selesai</th></tr></thead><tbody className="divide-y divide-emerald-100 text-slate-700">{data.recentAttempts.map((attempt) => <tr key={attempt.id} className="hover:bg-emerald-100"><td className="px-4 py-3.5 font-medium text-slate-900">{attempt.student?.name ?? "Mahasiswa"}</td><td className="px-4 py-3.5">{attempt.quiz?.title ?? "-"}</td><td className="px-4 py-3.5"><span className={`rounded-md px-2.5 py-1 font-bold ${(attempt.score ?? 0) >= 60 ? "bg-emerald-100 text-emerald-700" : "bg-rose-100 text-rose-700"}`}>{attempt.score ?? "—"}</span></td><td className="px-4 py-3.5 text-xs text-slate-600">{formatDate(attempt.completed_at, true)}</td></tr>)}</tbody></table>{!data.recentAttempts.length && <div className="space-y-2 py-12 text-center text-slate-600"><span className="text-4xl">📊</span><p className="text-sm">Belum ada sesi kuis yang diselesaikan mahasiswa.</p></div>}</div></section>
    </div>
  );
}
