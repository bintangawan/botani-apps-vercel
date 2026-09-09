import { ArrowRight, ImageOff, MapPin } from "lucide-react";
import Image from "next/image";
import Link from "next/link";
import type { PlantCardData } from "@/server/api/helpers";

export function PlantCard({ plant }: { plant: PlantCardData }) {
  return (
    <article className="group relative flex h-full flex-col overflow-hidden rounded-xl border border-emerald-200 bg-white shadow-xl transition-[border-color,box-shadow,transform] duration-300 ease-out hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-2xl">
      <div className="relative aspect-[4/3] w-full overflow-hidden bg-white">
        {plant.imageUrl ? (
          <Image
            src={plant.imageUrl}
            alt={`${plant.local_name} - ${plant.scientific_name}`}
            fill
            loading="lazy"
            decoding="async"
            sizes="(max-width: 639px) 100vw, (max-width: 1023px) 50vw, 33vw"
            className="object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
          />
        ) : (
          <div className="grid size-full place-items-center bg-emerald-100 text-emerald-600/50">
            <ImageOff className="size-10" />
            <span className="sr-only">Gambar belum tersedia</span>
          </div>
        )}
        <div className="absolute left-3.5 top-3.5 z-10 flex flex-wrap gap-1.5">
          <span
            className={
              plant.group_type === "Gymnospermae"
                ? "rounded-md border border-amber-300 bg-amber-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-amber-700 shadow-sm"
                : "rounded-md border border-emerald-300 bg-emerald-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-emerald-700 shadow-sm"
            }
          >
            {plant.group_type}
          </span>
          {plant.cotyledon_type !== "Tidak Berlaku" && (
            <span className="rounded-md border border-emerald-200 bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-slate-700 shadow-sm">
              {plant.cotyledon_type}
            </span>
          )}
        </div>
      </div>
      <div className="flex flex-grow flex-col justify-between p-6">
        <div>
          {plant.family && (
            <span className="mb-1 block text-xs font-semibold uppercase tracking-wider text-emerald-700">
              Famili: {plant.family}
            </span>
          )}
          <h2 className="line-clamp-1 text-xl font-bold text-slate-900 transition-colors group-hover:text-emerald-700">
            {plant.local_name}
          </h2>
          <p className="mb-3 mt-0.5 line-clamp-1 font-serif text-sm italic text-slate-700">
            {plant.scientific_name}
          </p>
          <p className="line-clamp-3 text-xs leading-relaxed text-slate-600">
            {plant.description ??
              "Deskripsi ilmiah spesimen sedang dilengkapi."}
          </p>
        </div>
        <div className="mt-6 flex items-center justify-between gap-2 border-t border-emerald-200 pt-4">
          <div className="flex max-w-[60%] items-center gap-1.5 truncate text-xs text-slate-600">
            <MapPin className="size-4 shrink-0 text-emerald-700" />
            <span className="truncate">
              {plant.regency ?? "Sumatera Utara"}
            </span>
          </div>
          <Link
            href={`/tumbuhan/${plant.slug}`}
            className="inline-flex shrink-0 items-center gap-1.5 rounded-md border border-emerald-300 bg-emerald-100 px-3.5 py-1.5 text-xs font-semibold text-emerald-700 transition-all group-hover:border-emerald-600 group-hover:bg-emerald-600 group-hover:text-slate-900"
          >
            Pelajari{" "}
            <ArrowRight className="size-3.5 transition-transform group-hover:translate-x-0.5" />
          </Link>
        </div>
      </div>
    </article>
  );
}
