import type { Metadata } from "next";
import { Suspense } from "react";
import { BotaniLogo } from "@/components/botani-logo";
import { PlantCard } from "@/components/plant-card";
import { EmptyState } from "@/components/ui/empty-state";
import { Pagination } from "@/components/ui/pagination";
import { getPlantCatalog } from "@/server/public-data";

export const metadata: Metadata = { title: "Galeri Tumbuhan" };

type CatalogPageProps = {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
};
function first(value: string | string[] | undefined): string {
  return Array.isArray(value) ? (value[0] ?? "") : (value ?? "");
}

function CatalogContentFallback() {
  return (
    <div role="status" aria-live="polite" aria-label="Memuat katalog tumbuhan">
      <div className="mb-8 grid gap-4 rounded-3xl border border-emerald-200 bg-white p-5 shadow-lg sm:grid-cols-2 lg:grid-cols-5">
        <div className="h-12 animate-pulse rounded-md bg-emerald-100 sm:col-span-2 lg:col-span-5" />
        {Array.from({ length: 5 }, (_, index) => (
          <div key={index} className="h-12 animate-pulse rounded-md bg-emerald-100" />
        ))}
      </div>
      <div className="mb-8 h-8 animate-pulse rounded-md bg-emerald-100" />
      <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        {Array.from({ length: 3 }, (_, index) => (
          <div key={index} className="aspect-[4/3] animate-pulse rounded-xl border border-emerald-200 bg-emerald-100" />
        ))}
      </div>
    </div>
  );
}

export default function CatalogPage({ searchParams }: CatalogPageProps) {
  return (
    <div className="page-container py-12 sm:py-16">
      <header className="mx-auto mb-10 max-w-3xl text-center">
        <p className="eyebrow mb-2 inline-flex items-center gap-1.5"><BotaniLogo decorative className="size-4" />Katalog Herbarium Digital</p>
        <h1 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
          Galeri Tumbuhan Phanerogamae
        </h1>
        <p className="mt-3 text-sm text-slate-700 sm:text-base">
          Jelajahi dan pelajari spesimen tumbuhan berbiji (Gymnospermae &amp;
          Angiospermae) hasil observasi ekosistem Sumatera Utara.
        </p>
      </header>
      <Suspense fallback={<CatalogContentFallback />}>
        <CatalogContent searchParams={searchParams} />
      </Suspense>
    </div>
  );
}

async function CatalogContent({ searchParams }: CatalogPageProps) {
  const params = await searchParams;
  const search = first(params.search);
  const groupType = first(params.group_type);
  const cotyledonType = first(params.cotyledon_type);
  const family = first(params.family);
  const regency = first(params.regency);
  const page = Math.max(1, Number(first(params.page)) || 1);
  const catalog = await getPlantCatalog({
    search,
    groupType:
      groupType === "Gymnospermae" || groupType === "Angiospermae"
        ? groupType
        : "all",
    cotyledonType:
      cotyledonType === "Monokotil" ||
      cotyledonType === "Dikotil" ||
      cotyledonType === "Tidak Berlaku"
        ? cotyledonType
        : "all",
    family: family || "all",
    regency: regency || "all",
    page,
    pageSize: 12,
  });
  const firstItem =
    catalog.total === 0 ? 0 : (catalog.page - 1) * catalog.pageSize + 1;
  const lastItem = Math.min(catalog.page * catalog.pageSize, catalog.total);
  const activeFilters = [
    search ? `Cari: “${search}”` : null,
    groupType && groupType !== "all" ? groupType : null,
    cotyledonType && cotyledonType !== "all" ? cotyledonType : null,
    family && family !== "all" ? `Famili: ${family}` : null,
    regency && regency !== "all" ? `Lokasi: ${regency}` : null,
  ].filter((item): item is string => Boolean(item));

  return (
    <>
      <form
        className="mb-8 grid gap-4 rounded-3xl border border-emerald-200 bg-white p-5 shadow-lg sm:grid-cols-2 lg:grid-cols-5"
        action="/tumbuhan"
      >
        <div className="sm:col-span-2 lg:col-span-5">
          <label className="form-label" htmlFor="search">
            Cari spesimen
          </label>
          <input
            className="form-input"
            id="search"
            name="search"
            defaultValue={search}
            placeholder="Nama lokal, ilmiah, atau kode"
          />
        </div>
        <div>
          <label className="form-label" htmlFor="group_type">
            Kelompok
          </label>
          <select
            className="form-input"
            id="group_type"
            name="group_type"
            defaultValue={groupType || "all"}
          >
            <option value="all">Semua kelompok</option>
            <option>Gymnospermae</option>
            <option>Angiospermae</option>
          </select>
        </div>
        <div>
          <label className="form-label" htmlFor="cotyledon_type">
            Kotiledon
          </label>
          <select
            className="form-input"
            id="cotyledon_type"
            name="cotyledon_type"
            defaultValue={cotyledonType || "all"}
          >
            <option value="all">Semua kotiledon</option>
            <option>Monokotil</option>
            <option>Dikotil</option>
            <option>Tidak Berlaku</option>
          </select>
        </div>
        <div>
          <label className="form-label" htmlFor="family">
            Famili
          </label>
          <select
            className="form-input"
            id="family"
            name="family"
            defaultValue={family || "all"}
          >
            <option value="all">Semua famili</option>
            {catalog.families.map((item) => (
              <option key={item}>{item}</option>
            ))}
          </select>
        </div>
        <div>
          <label className="form-label" htmlFor="regency">
            Kabupaten/Kota
          </label>
          <select
            className="form-input"
            id="regency"
            name="regency"
            defaultValue={regency || "all"}
          >
            <option value="all">Semua wilayah</option>
            {catalog.regencies.map((item) => (
              <option key={item}>{item}</option>
            ))}
          </select>
        </div>
        <div className="flex items-end gap-3">
          <button className="btn-primary min-h-12 flex-1" type="submit">
            Terapkan
          </button>
          <a className="btn-secondary min-h-12" href="/tumbuhan">
            Reset
          </a>
        </div>
      </form>

      <div className="mb-8 flex min-w-0 flex-col items-center justify-between gap-4 break-words border-b border-emerald-200 pb-4 text-sm text-slate-600 sm:flex-row">
        <p>
          Menampilkan <strong className="text-slate-900">{firstItem}</strong> -{" "}
          <strong className="text-slate-900">{lastItem}</strong> dari total{" "}
          <strong className="text-emerald-700">{catalog.total}</strong> spesimen
        </p>
        {activeFilters.length > 0 && (
          <div className="flex flex-wrap items-center gap-2">
            <span className="text-xs text-slate-500">Filter Aktif:</span>
            {activeFilters.map((item) => (
              <span
                key={item}
                className="rounded-md border border-emerald-300 bg-emerald-100 px-2.5 py-1 text-xs text-emerald-700"
              >
                {item}
              </span>
            ))}
          </div>
        )}
      </div>

      {catalog.items.length ? (
        <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          {catalog.items.map((plant) => (
            <PlantCard key={plant.id} plant={plant} />
          ))}
        </div>
      ) : (
        <EmptyState
          title="Spesimen Tidak Ditemukan"
          description="Kami tidak menemukan spesimen yang sesuai dengan kata kunci atau filter pencarian Anda."
        />
      )}
      <div className="mt-14">
        <Pagination
          page={catalog.page}
          pageSize={catalog.pageSize}
          total={catalog.total}
          pathname="/tumbuhan"
          searchParams={{
            search,
            group_type: groupType,
            cotyledon_type: cotyledonType,
            family,
            regency,
          }}
        />
      </div>
    </>
  );
}
