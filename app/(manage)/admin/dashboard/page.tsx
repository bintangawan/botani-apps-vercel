import Link from "next/link";
import { BookOpen, Database, MapPin, Users } from "lucide-react";
import { Suspense } from "react";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

export default function AdminDashboardPage() {
  return (
    <Suspense fallback={<PageContentFallback label="Memuat dashboard administrator" />}>
      <AdminDashboardContent />
    </Suspense>
  );
}

async function AdminDashboardContent() {
  await requireRoles(["admin"]);
  const data = await (await api()).dashboard.admin();
  const metrics = [
    { label: "Spesimen Tumbuhan", value: data.stats.totalSpecies, icon: Database, color: "text-slate-900" },
    { label: "Titik Observasi", value: data.stats.totalObservations, icon: MapPin, color: "text-amber-700" },
    { label: "Modul Pembelajaran", value: data.stats.totalModules, icon: BookOpen, color: "text-teal-700" },
    { label: "Akun Pengguna", value: data.stats.totalUsers, icon: Users, color: "text-emerald-700" },
  ];
  return (
    <div className="mx-auto max-w-7xl py-6 sm:py-8">
      <header className="mb-8 flex flex-col items-center justify-between gap-6 rounded-md border border-emerald-300 bg-emerald-100 p-8 shadow-xl sm:flex-row sm:p-10"><div className="space-y-2 text-center sm:text-left"><span className="inline-flex rounded-md border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">⚙️ Panel Administrator Sistem</span><h1 className="text-3xl font-extrabold text-slate-900">Kendali Sistem &amp; Database Botani</h1><p className="max-w-2xl text-sm text-slate-700">Kelola data spesimen herbarium, modul pembelajaran, observasi lapangan, serta hak akses pengguna.</p></div><div className="flex shrink-0 flex-wrap items-center justify-center gap-3"><Link href="/manage/plants/new" className="btn-primary">+ Tambah Tumbuhan</Link><Link href="/manage/quizzes" className="btn-secondary border-amber-300 text-amber-700 hover:bg-amber-100">🎯 Bank Soal &amp; Kuis</Link><Link href="/admin/users" className="btn-secondary">👥 Kelola User &amp; Dosen</Link></div></header>
      <div className="mb-10 grid grid-cols-2 gap-6 md:grid-cols-4">{metrics.map((metric) => { const Icon = metric.icon; return <article key={metric.label} className="rounded-md border border-emerald-200 bg-white p-6 shadow-lg"><Icon className="mb-4 size-6 text-emerald-600" /><strong className={`mb-1 block text-3xl font-extrabold sm:text-4xl ${metric.color}`}>{metric.value}</strong><span className="text-xs font-bold uppercase tracking-wider text-slate-700">{metric.label}</span></article>; })}</div>
      <div className="grid gap-8 lg:grid-cols-2"><section className="space-y-4 rounded-md border border-emerald-200 bg-white p-6 sm:p-8"><h2 className="border-b border-emerald-200 pb-3 text-lg font-bold text-slate-900">🌱 Spesimen Tumbuhan Terkatalog Terakhir</h2><div className="space-y-3">{data.recentSpecies.map((plant) => <Link key={plant.id} href={`/manage/plants/${plant.id}/edit`} className="flex items-center justify-between rounded-md border border-emerald-200 bg-white p-3.5 hover:bg-emerald-50"><div><strong className="block text-sm text-slate-900">{plant.local_name}</strong><span className="font-serif text-xs italic text-emerald-700">{plant.scientific_name}</span></div><span className="rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 font-mono text-[10px] text-slate-600">{plant.code}</span></Link>)}</div></section><section className="space-y-4 rounded-md border border-emerald-200 bg-white p-6 sm:p-8"><h2 className="border-b border-emerald-200 pb-3 text-lg font-bold text-slate-900">👥 Akun Pengguna Terakhir</h2><div className="space-y-3">{data.recentUsers.map((user) => <div key={user.id} className="flex items-center justify-between rounded-md border border-emerald-200 bg-white p-3.5"><div className="min-w-0"><strong className="block truncate text-sm text-slate-900">{user.name}</strong><span className="block truncate text-xs text-slate-600">{user.email}</span></div><span className={`rounded-md border px-2.5 py-1 text-xs font-bold uppercase tracking-wider ${user.role === "admin" ? "border-amber-300 bg-amber-100 text-amber-700" : user.role === "dosen" ? "border-teal-300 bg-teal-100 text-teal-700" : "border-emerald-300 bg-emerald-100 text-emerald-700"}`}>{user.role}</span></div>)}</div></section></div>
    </div>
  );
}
