import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { ArrowLeft, ImageOff } from "lucide-react";
import { handlePageError } from "@/server/page-error";
import { Suspense } from "react";
import { BotaniLogo } from "@/components/botani-logo";
import { PlantDetailFallback } from "@/components/ui/runtime-fallbacks";
import { getPlantBySlug } from "@/server/public-data";

type PlantDetailProps = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: PlantDetailProps): Promise<Metadata> {
  const { slug } = await params;
  try {
    const plant = await getPlantBySlug(slug);
    return { title: plant.local_name, description: plant.description ?? `Detail ilmiah ${plant.scientific_name}` };
  } catch { return { title: "Detail spesimen" }; }
}

const morphologyLabels = {
  root: "🌱 Akar (Radix)", stem: "🪵 Batang (Caulis)", leaf: "🍃 Daun (Folium)", flower: "🌸 Bunga (Flos / Strobilus)", fruit: "🍎 Buah (Fructus)", seed: "🌰 Biji (Semen)", special_characteristics: "✨ Ciri Khusus",
} as const;

export default function PlantDetailPage({ params }: PlantDetailProps) {
  return (
    <Suspense fallback={<PlantDetailFallback />}>
      <PlantDetailContent params={params} />
    </Suspense>
  );
}

async function PlantDetailContent({ params }: PlantDetailProps) {
  const { slug } = await params;
  const plant = await getPlantBySlug(slug).catch(handlePageError);
  const primaryImage = plant.imageUrl.find((item) => item.is_primary) ?? plant.imageUrl[0];
  const family = plant.taxonomy.find((taxon) => taxon.rank === "famili")?.name ?? "-";
  const order = plant.taxonomy.find((taxon) => taxon.rank === "ordo")?.name ?? "-";

  return (
    <div className="page-container min-w-0 overflow-x-hidden py-12 sm:py-20">
      <div className="mb-8"><Link href="/tumbuhan" className="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-900"><ArrowLeft className="size-4" />Kembali ke Galeri Tumbuhan</Link></div>
      <div className="grid min-w-0 items-start gap-8 sm:gap-10 lg:grid-cols-12">
        <aside className="min-w-0 space-y-6 lg:sticky lg:top-28 lg:col-span-5">
          <div className="group relative aspect-[4/3] overflow-hidden rounded-md border border-emerald-300 bg-white shadow-lg sm:aspect-square">
            {primaryImage?.url ? <Image src={primaryImage.url} alt={primaryImage.caption ?? plant.local_name} fill loading="lazy" decoding="async" sizes="(max-width: 1023px) 100vw, 42vw" className="object-cover transition-transform duration-700 group-hover:scale-[1.02]" /> : <div className="flex size-full flex-col items-center justify-center gap-2 bg-emerald-100 text-slate-600"><ImageOff className="size-14 text-emerald-500" /><span className="text-xs">Gambar Herbarium Belum Tersedia</span></div>}
            <div className="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3 rounded-md border border-white bg-white/90 px-3 py-2 text-xs text-slate-700 shadow-sm"><span>📷 Dokumentasi Herbarium Digital</span><span className="font-semibold text-emerald-700">{plant.author_name || "L."}</span></div>
          </div>
          <section className="min-w-0 space-y-4 rounded-md border border-emerald-200 bg-white p-6">
            <h2 className="border-b border-emerald-200 pb-2 text-xs font-bold uppercase tracking-widest text-emerald-700">🏷️ Klasifikasi Cepat</h2>
            <div className="grid min-w-0 gap-3 text-xs sm:grid-cols-2">
              {[
                ["Divisi / Kelompok", plant.group_type, "text-emerald-700"],
                ["Kelas Kotiledon", plant.cotyledon_type, "text-slate-900"],
                ["Ordo", order, "text-slate-700"],
                ["Famili", family, "text-amber-700"],
              ].map(([label, value, color]) => <div key={label} className="break-words rounded-md border border-emerald-200 bg-white p-3"><span className="block text-[10px] font-semibold uppercase text-slate-500">{label}</span><strong className={`mt-0.5 block text-sm ${color}`}>{value}</strong></div>)}
            </div>
          </section>
        </aside>

        <main className="min-w-0 space-y-8 break-words lg:col-span-7">
          <header>
            <div className="mb-3 flex flex-wrap items-center gap-2"><span className={`rounded-md border px-3 py-1 text-xs font-bold uppercase tracking-wider ${plant.group_type === "Gymnospermae" ? "border-amber-300 bg-amber-100 text-amber-700" : "border-emerald-300 bg-emerald-100 text-emerald-700"}`}>{plant.group_type}</span>{plant.cotyledon_type !== "Tidak Berlaku" && <span className="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-slate-700">{plant.cotyledon_type}</span>}<span className="rounded-md border border-emerald-200 bg-white px-3 py-1 text-xs font-bold text-slate-600">{plant.code}</span></div>
            <h1 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">{plant.local_name}</h1>
            <p className="mt-1 font-serif text-xl italic text-emerald-700 sm:text-2xl">{plant.scientific_name} <span className="text-base not-italic text-slate-600">{plant.author_name}</span></p>
          </header>

          <nav className="flex max-w-full gap-4 overflow-x-auto border-b border-emerald-200 pb-1 text-xs font-semibold sm:gap-6 sm:text-sm"><a href="#teori" className="shrink-0 border-b-2 border-emerald-400 pb-3 text-emerald-700">📖 Deskripsi &amp; Teori Botani</a><a href="#taksonomi" className="inline-flex shrink-0 items-start gap-1.5 pb-3 text-slate-600 hover:text-emerald-800"><BotaniLogo decorative className="mt-0.5 size-4" />Hierarki Taksonomi</a><a href="#morfologi" className="shrink-0 pb-3 text-slate-600 hover:text-emerald-800">🔬 Morfologi Organ</a><a href="#observasi" className="shrink-0 pb-3 text-slate-600 hover:text-emerald-800">📍 Observasi Lapangan</a></nav>

          <section id="teori" className="space-y-6 rounded-md border border-emerald-200 bg-white p-6 leading-relaxed text-slate-700 sm:p-8">
            <div><h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-emerald-700">Deskripsi Ilmiah &amp; Landasan Teori</h2><p className="text-base leading-relaxed">{plant.description || "Deskripsi spesimen belum tersedia."}</p></div>
            {plant.habitat && <div className="border-t border-emerald-200 pt-4"><h3 className="mb-2 text-xs font-bold uppercase tracking-widest text-amber-700">🌱 Habitat &amp; Ekologi Tropis</h3><p className="text-sm">{plant.habitat}</p></div>}
            {plant.benefits && <div className="border-t border-emerald-200 pt-4"><h3 className="mb-2 text-xs font-bold uppercase tracking-widest text-teal-700">💎 Nilai Manfaat &amp; Kegunaan</h3><p className="text-sm">{plant.benefits}</p></div>}
          </section>

          <section id="taksonomi" className="space-y-4 rounded-md border border-emerald-200 bg-white p-6 sm:p-8">
            <h2 className="mb-4 text-sm font-bold uppercase tracking-wider text-emerald-700">Hierarki Taksonomi Lengkap</h2>
            <div className="relative space-y-3 before:absolute before:bottom-2 before:left-4 before:top-2 before:w-0.5 before:bg-emerald-300">{plant.taxonomy.map((taxon) => <div key={taxon.id} className="relative flex items-center gap-4 pl-8"><span className="absolute left-2.5 top-5 size-3.5 rounded-md border-2 border-white bg-emerald-500" /><div className="flex flex-grow items-center justify-between rounded-md border border-emerald-200 bg-white p-3.5"><div><span className="block text-[10px] font-bold uppercase tracking-wider text-emerald-700">{taxon.rank}</span><strong className={`text-sm text-slate-900 ${taxon.rank === "genus" || taxon.rank === "spesies" ? "font-serif italic" : ""}`}>{taxon.name}</strong></div><span className="hidden text-xs text-slate-500 sm:block">Phanerogamae</span></div></div>)}</div>
          </section>

          <section id="morfologi" className="space-y-6 rounded-md border border-emerald-200 bg-white p-6 sm:p-8">
            <h2 className="text-sm font-bold uppercase tracking-wider text-emerald-700">Karakteristik Anatomi &amp; Morfologi Organ</h2>
            {plant.morphology ? <div className="grid gap-6 text-sm sm:grid-cols-2">{Object.entries(morphologyLabels).map(([key, label]) => { const value = plant.morphology?.[key as keyof typeof morphologyLabels]; return value ? <article key={key} className="rounded-md border border-emerald-200 bg-white p-4"><h3 className="mb-1 block text-xs font-bold uppercase text-emerald-700">{label}</h3><p className="leading-relaxed text-slate-700">{value}</p></article> : null; })}</div> : <p className="text-sm italic text-slate-600">Data morfologi mendetail belum tersedia.</p>}
          </section>

          <section id="observasi" className="space-y-6 rounded-md border border-emerald-200 bg-white p-6 sm:p-8">
            <h2 className="text-sm font-bold uppercase tracking-wider text-emerald-700">Catatan Sampel Lapangan Sumatera Utara</h2>
            {plant.observations.length > 0 ? <div className="space-y-4">{plant.observations.map((observation) => <article key={observation.id} className="flex flex-col justify-between gap-4 rounded-md border border-emerald-200 bg-white p-5 sm:flex-row"><div><p className="text-xs font-semibold text-emerald-700">📍 Lokasi: {observation.location ? `${observation.location.location_name}, ${observation.location.province}` : "Sumatera Utara"}</p><p className="mt-1 text-sm leading-relaxed text-slate-700">{observation.notes || "Tidak ada catatan tambahan."}</p></div><time className="shrink-0 text-xs text-slate-500">Tanggal: {observation.observation_date ?? "-"}</time></article>)}</div> : <p className="text-sm italic text-slate-600">Belum ada catatan observasi lapangan yang ditautkan.</p>}
          </section>

          {plant.modules.length > 0 && <section className="flex flex-col items-start justify-between gap-4 rounded-md border border-emerald-300 bg-emerald-100 p-6 sm:flex-row sm:items-center"><div><p className="text-xs font-bold uppercase text-emerald-700">📚 Terintegrasi Kurikulum</p><h2 className="text-base font-bold text-slate-900">Spesimen Ini Dipelajari dalam Modul Botani</h2></div><Link href={`/materi/${plant.modules[0]?.slug}`} className="btn-primary shrink-0 text-xs">Buka Modul Teori →</Link></section>}
        </main>
      </div>
    </div>
  );
}
