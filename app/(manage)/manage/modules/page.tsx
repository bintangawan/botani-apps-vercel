import Link from "next/link";
import { Pencil, Plus } from "lucide-react";
import { Suspense } from "react";
import { DeleteButton } from "@/components/manage/delete-button";
import { ModuleManageSearch } from "@/components/manage/manage-search-box";
import { PageHeader } from "@/components/ui/page-header";
import { Pagination } from "@/components/ui/pagination";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";

type Props = { searchParams: Promise<Record<string, string | string[] | undefined>> };
function value(input: string | string[] | undefined): string { return Array.isArray(input) ? (input[0] ?? "") : (input ?? ""); }

export default function ManageModulesPage({ searchParams }: Props) {
  return (
    <Suspense fallback={<PageContentFallback label="Memuat modul pembelajaran" />}>
      <ManageModulesContent searchParams={searchParams} />
    </Suspense>
  );
}

async function ManageModulesContent({ searchParams }: Props) {
  const params = await searchParams;
  const search = value(params.search);
  const page = Math.max(1, Number(value(params.page)) || 1);
  const data = await (await api()).modules.manageList({ search, page, pageSize: 10 });
  return <div className="mx-auto max-w-7xl"><PageHeader eyebrow="📚 Materi & Kurikulum" title="Manajemen Modul Pembelajaran" description="Susun bab, konten Markdown, ringkasan, dan spesies yang terkait." actions={<Link href="/manage/modules/new" className="btn-primary"><Plus className="size-4" />Tambah Bab Baru</Link>} /><form className="panel mt-7 flex gap-3 p-5"><ModuleManageSearch defaultValue={search} placeholder="Cari judul atau deskripsi" /><button className="btn-primary">Cari</button></form><div className="panel mt-6 overflow-x-auto p-6"><table className="w-full min-w-[760px] text-left text-sm"><thead className="border-b border-emerald-200 text-xs uppercase tracking-wider text-emerald-700"><tr><th className="px-4 py-4">Bab</th><th className="px-4 py-4">Subbab</th><th className="px-4 py-4">Spesies</th><th className="px-4 py-4">Status</th><th className="px-4 py-4 text-right">Aksi</th></tr></thead><tbody className="divide-y divide-emerald-100 text-slate-700">{data.items.map((module) => <tr key={module.id} className="hover:bg-emerald-50"><td className="px-4 py-4"><p className="eyebrow">Bab {module.module_order}</p><p className="mt-1 font-bold text-slate-950">{module.title}</p></td><td className="px-4 py-4">{module.lessonCount}</td><td className="px-4 py-4">{module.speciesCount}</td><td className="px-4 py-4">{module.status}</td><td className="px-4 py-4"><div className="flex justify-end gap-2"><Link href={`/manage/modules/${module.id}/edit`} className="inline-flex size-9 items-center justify-center rounded-md border border-amber-300 bg-amber-50 text-amber-700"><Pencil className="size-4" /></Link><DeleteButton type="module" id={module.id} label={module.title} /></div></td></tr>)}</tbody></table></div><Pagination page={data.page} pageSize={data.pageSize} total={data.total} pathname="/manage/modules" searchParams={{ search }} /></div>;
}
