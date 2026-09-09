import { ArrowRight, BookOpen, Clock3, Leaf } from "lucide-react";
import Link from "next/link";
import type { Tables } from "@/types/database";

type ModuleData = Tables<"learning_modules"> & {
  lessonCount: number;
  speciesCount: number;
  quizCount: number;
};
type ModuleCardProps = { module: ModuleData; compact?: boolean };

export function ModuleCard({ module, compact = false }: ModuleCardProps) {
  if (compact) {
    return (
      <article className="group flex h-full flex-col justify-between overflow-hidden rounded-2xl! border border-emerald-300 bg-white p-8 shadow-xl transition-[border-color,box-shadow,transform] duration-300 ease-out hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-2xl">
        <div>
          <span className="mb-6 grid size-12 place-items-center rounded-md border border-emerald-300 bg-emerald-100 text-lg font-bold text-emerald-700 transition-transform duration-300 group-hover:-translate-y-0.5">
            #{module.module_order}
          </span>
          <h2 className="mb-3 text-xl font-bold text-slate-900 transition-colors group-hover:text-emerald-700">
            {module.title}
          </h2>
          <p className="mb-6 line-clamp-3 text-sm leading-relaxed text-slate-600">
            {module.description}
          </p>
        </div>
        <Link
          href={`/materi/${module.slug}`}
          className="inline-flex items-center gap-2 border-t border-emerald-200 pt-4 text-sm font-semibold text-emerald-700"
        >
          Mulai Belajar Modul Ini{" "}
          <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" />
        </Link>
      </article>
    );
  }

  return (
    <article className="group flex h-full flex-col overflow-hidden rounded-2xl! border border-emerald-200 bg-white shadow-lg transition-[border-color,box-shadow,transform] duration-300 ease-out hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-xl">
      <div className="border-b border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-6">
        <div className="flex items-center justify-between gap-4">
          <span className="grid size-11 place-items-center rounded-md bg-emerald-600 text-sm font-extrabold text-white shadow-md">
            {String(module.module_order).padStart(2, "0")}
          </span>
          <span className="rounded-md border border-emerald-200 bg-white px-3 py-1 text-xs font-bold text-emerald-700">
            {module.lessonCount} lesson
          </span>
        </div>
        <h2 className="mt-5 text-xl font-extrabold leading-snug text-slate-900 transition group-hover:text-emerald-700">
          {module.title}
        </h2>
        <p className="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">
          {module.description}
        </p>
      </div>
      <div className="flex flex-1 flex-col p-6">
        <p className="text-[11px] font-bold uppercase tracking-wider text-slate-400">
          Ringkasan bab
        </p>
        <div className="mt-4 grid grid-cols-2 gap-3 text-xs font-semibold text-slate-600">
          <span className="flex items-center gap-2 rounded-md border border-emerald-200 bg-white p-3">
            <BookOpen className="size-4 text-emerald-600" />
            {module.lessonCount} subbab
          </span>
          <span className="flex items-center gap-2 rounded-md border border-emerald-200 bg-white p-3">
            <Leaf className="size-4 text-emerald-600" />
            {module.speciesCount} spesies
          </span>
        </div>
        <div className="mt-auto pt-6">
          <div className="flex items-center justify-between border-t border-emerald-100 pt-5">
            <span className="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
              <Clock3 className="size-4" />± {module.estimated_minutes} menit
            </span>
            <Link
              href={`/materi/${module.slug}`}
              className="inline-flex items-center gap-2 rounded-md bg-emerald-600 px-4 py-2.5 text-xs font-extrabold text-white transition hover:bg-emerald-700"
            >
              Mulai Belajar <ArrowRight className="size-4" />
            </Link>
          </div>
        </div>
      </div>
    </article>
  );
}
