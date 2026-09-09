import Link from "next/link";
import { Pencil, Plus } from "lucide-react";
import { Suspense } from "react";
import { DeleteButton } from "@/components/manage/delete-button";
import { PlantManageSearch } from "@/components/manage/manage-search-box";
import { PageHeader } from "@/components/ui/page-header";
import { Pagination } from "@/components/ui/pagination";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";

type Props = {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
};

function value(input: string | string[] | undefined): string {
  return Array.isArray(input) ? (input[0] ?? "") : (input ?? "");
}

export default function ManagePlantsPage({ searchParams }: Props) {
  return (
    <Suspense fallback={<PageContentFallback label="Memuat katalog pengelolaan" />}>
      <ManagePlantsContent searchParams={searchParams} />
    </Suspense>
  );
}

async function ManagePlantsContent({ searchParams }: Props) {
  const params = await searchParams;
  const search = value(params.search);
  const group = value(params.group_type);
  const page = Math.max(1, Number(value(params.page)) || 1);
  const data = await (await api()).plants.manageList({
    search,
    groupType:
      group === "Gymnospermae" || group === "Angiospermae" ? group : "all",
    page,
    pageSize: 10,
  });

  return (
    <div className="mx-auto max-w-7xl">
      <PageHeader
        eyebrow="🌱 Katalog & Taksonomi"
        title="Manajemen Spesimen Tumbuhan"
        description="Tambah, perbarui, dan terbitkan spesimen beserta morfologi dan taksonominya."
        actions={
          <Link href="/manage/plants/new" className="btn-primary">
            <Plus className="size-4" />
            Tambah tumbuhan
          </Link>
        }
      />
      <form className="panel mt-7 grid gap-4 p-5 sm:grid-cols-[1fr_220px_auto]">
        <PlantManageSearch
          defaultValue={search}
          placeholder="Cari nama atau kode"
        />
        <select
          className="form-input"
          name="group_type"
          defaultValue={group || "all"}
        >
          <option value="all">Semua kelompok</option>
          <option>Gymnospermae</option>
          <option>Angiospermae</option>
        </select>
        <button className="btn-primary" type="submit">
          Cari
        </button>
      </form>
      <div className="panel mt-6 overflow-x-auto p-6">
        <table className="w-full min-w-[760px] text-left text-sm">
          <thead className="border-b border-emerald-200 text-xs uppercase tracking-wider text-emerald-700">
            <tr>
              <th className="px-4 py-4">Spesimen</th>
              <th className="px-4 py-4">Kelompok</th>
              <th className="px-4 py-4">Famili</th>
              <th className="px-4 py-4">Status</th>
              <th className="px-4 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-emerald-100 text-slate-700">
            {data.items.map((plant) => (
              <tr key={plant.id} className="hover:bg-emerald-100">
                <td className="px-4 py-4">
                  <p className="font-bold text-slate-950">{plant.local_name}</p>
                  <p className="mt-1 text-xs font-serif italic text-emerald-700">
                    {plant.scientific_name} · {plant.code}
                  </p>
                </td>
                <td className="px-4 py-4">{plant.group_type}</td>
                <td className="px-4 py-4">{plant.family ?? "—"}</td>
                <td className="px-4 py-4">
                  <span className="rounded-md bg-emerald-100 px-2.5 py-1 text-[10px] font-extrabold uppercase text-emerald-800">
                    {plant.status}
                  </span>
                </td>
                <td className="px-4 py-4">
                  <div className="flex justify-end gap-2">
                    <Link
                      href={`/manage/plants/${plant.id}/edit`}
                      className="inline-flex size-9 items-center justify-center rounded-md border border-amber-300 bg-amber-100 text-amber-700"
                    >
                      <Pencil className="size-4" />
                    </Link>
                    <DeleteButton type="plant" id={plant.id} label={plant.local_name} />
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {!data.items.length && (
          <p className="p-10 text-center text-sm text-slate-500">
            Belum ada data yang cocok.
          </p>
        )}
      </div>
      <Pagination
        page={data.page}
        pageSize={data.pageSize}
        total={data.total}
        pathname="/manage/plants"
        searchParams={{ search, group_type: group }}
      />
    </div>
  );
}
