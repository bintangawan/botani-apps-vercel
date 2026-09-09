import Link from "next/link";
import { ListChecks, Pencil, Plus } from "lucide-react";
import { Suspense } from "react";
import { DeleteButton } from "@/components/manage/delete-button";
import { PageHeader } from "@/components/ui/page-header";
import { Pagination } from "@/components/ui/pagination";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";

type Props = { searchParams: Promise<Record<string, string | string[] | undefined>> };
function value(input: string | string[] | undefined): string { return Array.isArray(input) ? (input[0] ?? "") : (input ?? ""); }

export default function ManageQuizzesPage({ searchParams }: Props) {
  return (
    <Suspense fallback={<PageContentFallback label="Memuat daftar kuis" />}>
      <ManageQuizzesContent searchParams={searchParams} />
    </Suspense>
  );
}

async function ManageQuizzesContent({ searchParams }: Props) {
  const params = await searchParams;
  const search = value(params.search);
  const moduleId = Number(value(params.module_id)) || undefined;
  const page = Math.max(1, Number(value(params.page)) || 1);
  const caller = await api();
  const [data, modules] = await Promise.all([caller.quizzes.manageList({ search, moduleId, page, pageSize: 10 }), caller.modules.selectOptions()]);
  return <div className="mx-auto max-w-7xl"><PageHeader eyebrow="🎯 Bank Soal & Kuis" title="Manajemen Kuis & Evaluasi" description="Konfigurasikan evaluasi dan susun kunci jawaban yang aman dari akses mahasiswa." actions={<Link href="/manage/quizzes/new" className="btn-primary"><Plus className="size-4" />Buat kuis</Link>} /><form className="panel mt-7 grid gap-3 p-5 sm:grid-cols-[1fr_280px_auto]"><input className="form-input" name="search" defaultValue={search} placeholder="Cari judul kuis" /><select className="form-input" name="module_id" defaultValue={moduleId ?? ""}><option value="">Semua modul</option>{modules.map((module) => <option value={module.id} key={module.id}>{module.title}</option>)}</select><button className="btn-primary">Filter</button></form><div className="panel mt-6 overflow-x-auto p-6"><table className="w-full min-w-[820px] text-left text-sm"><thead className="border-b border-emerald-200 text-xs uppercase tracking-wider text-emerald-700"><tr><th className="px-4 py-4">Kuis</th><th className="px-4 py-4">Modul</th><th className="px-4 py-4">Soal</th><th className="px-4 py-4">Attempt</th><th className="px-4 py-4">Status</th><th className="px-4 py-4 text-right">Aksi</th></tr></thead><tbody className="divide-y divide-emerald-100 text-slate-700">{data.items.map((quiz) => <tr key={quiz.id} className="hover:bg-emerald-100"><td className="px-4 py-4 font-bold text-slate-950">{quiz.title}</td><td className="px-4 py-4 text-slate-600">{quiz.moduleTitle}</td><td className="px-4 py-4">{quiz.questionCount}</td><td className="px-4 py-4">{quiz.attemptCount}</td><td className="px-4 py-4">{quiz.status}</td><td className="px-4 py-4"><div className="flex justify-end gap-2"><Link title="Bank soal" href={`/manage/quizzes/${quiz.id}/questions`} className="inline-flex size-9 items-center justify-center rounded-md border border-amber-300 bg-amber-100 text-amber-700"><ListChecks className="size-4" /></Link><Link title="Edit" href={`/manage/quizzes/${quiz.id}/edit`} className="inline-flex size-9 items-center justify-center rounded-md border border-emerald-300 bg-emerald-50 text-emerald-700"><Pencil className="size-4" /></Link><DeleteButton type="quiz" id={quiz.id} label={quiz.title} /></div></td></tr>)}</tbody></table></div><Pagination page={data.page} pageSize={data.pageSize} total={data.total} pathname="/manage/quizzes" searchParams={{ search, module_id: moduleId ? String(moduleId) : undefined }} /></div>;
}
