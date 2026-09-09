import type { Metadata } from "next";
import { connection } from "next/server";
import { Suspense } from "react";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { ModuleCard } from "@/components/module-card";
import { EmptyState } from "@/components/ui/empty-state";
import { getPublishedModules } from "@/server/public-data";

export const metadata: Metadata = { title: "Modul Pembelajaran" };

export default function ModulesPage() {
  return <Suspense fallback={<div className="page-container py-12"><PageContentFallback label="Memuat modul pembelajaran" /></div>}><ModulesContent /></Suspense>;
}

async function ModulesContent() {
  await connection();
  const modules = await getPublishedModules();
  const lessonCount = modules.reduce(
    (total, module) => total + module.lessonCount,
    0,
  );
  const estimatedMinutes = modules.reduce(
    (total, module) => total + module.estimated_minutes,
    0,
  );

  return (
    <div className="page-container py-12 sm:py-16">
      <section className="relative overflow-hidden rounded-[2rem] border border-emerald-300 bg-emerald-100 px-6 py-10 shadow-xl sm:px-10 lg:px-14 lg:py-14">
        <div className="relative z-10 max-w-3xl">
          <span className="inline-flex rounded-md border border-emerald-300 bg-white/70 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-700">
            Kurikulum Botani Phanerogamae
          </span>
          <h1 className="mt-5 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">
            Belajar terstruktur dari bab ke subbab
          </h1>
          <p className="mt-4 max-w-2xl text-sm leading-7 text-slate-700 sm:text-base">
            Materi disusun menjadi {modules.length} bab dan {lessonCount}{" "}
            subbab. Pilih bab, lalu ikuti setiap materi melalui navigasi
            pembelajaran yang berurutan.
          </p>
          <div className="mt-7 flex flex-wrap gap-3 text-xs font-bold text-emerald-800">
            <span className="rounded-md border border-emerald-300 bg-white/70 px-3 py-2">
              {modules.length} Bab
            </span>
            <span className="rounded-md border border-emerald-300 bg-white/70 px-3 py-2">
              {lessonCount} Subbab
            </span>
            <span className="rounded-md border border-emerald-300 bg-white/70 px-3 py-2">
              {estimatedMinutes} Menit
            </span>
          </div>
        </div>
        <div className="absolute -bottom-20 -right-16 size-64 rounded-md bg-emerald-300/40" />
        <div className="absolute -right-4 top-8 size-28 rounded-md border-[20px] border-white/30" />
      </section>

      <section className="mt-12">
        <div className="mb-6 flex items-end justify-between gap-4">
          <div>
            <p className="eyebrow">Daftar Materi</p>
            <h2 className="mt-1 text-2xl font-extrabold text-slate-900">
              {modules.length} Bab Pembelajaran
            </h2>
          </div>
          <p className="hidden text-sm text-slate-500 sm:block">
            Mulai dari bab mana saja
          </p>
        </div>
        {modules.length ? (
          <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            {modules.map((module) => (
              <ModuleCard key={module.id} module={module} />
            ))}
          </div>
        ) : (
          <EmptyState
            title="Modul belum tersedia"
            description="Admin atau dosen dapat menerbitkan modul dari panel pengelolaan."
          />
        )}
      </section>
    </div>
  );
}
