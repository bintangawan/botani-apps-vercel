import Link from "next/link";
import { connection } from "next/server";
import { Suspense } from "react";
import {
  ArrowRight,
  BookOpenCheck,
  GraduationCap,
  MapPin,
  Microscope,
} from "lucide-react";
import { ModuleCard } from "@/components/module-card";
import { PlantCard } from "@/components/plant-card";
import { getHomePageData } from "@/server/public-data";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";

export default function HomePage() {
  return <Suspense fallback={<div className="page-container py-12"><PageContentFallback label="Memuat beranda" /></div>}><HomeContent /></Suspense>;
}

async function HomeContent() {
  await connection();
  const { featured, stats, modules } = await getHomePageData();

  return (
    <>
      <section className="relative overflow-hidden pb-20 pt-12 sm:pb-32 sm:pt-20">
        <div className="page-container relative z-10">
          <div className="mx-auto max-w-4xl space-y-6 text-center sm:space-y-8">
            <div className="inline-flex items-center gap-2 rounded-full border border-emerald-300 bg-emerald-100 px-4 py-1.5 text-xs font-semibold text-emerald-700 shadow-inner sm:text-sm">
              <span className="size-2 animate-ping rounded-md bg-emerald-400" />
              <span>
                Platform Pembelajaran Digital Botani &amp; Observasi Herbarium
              </span>
            </div>
            <h1 className="text-4xl font-extrabold leading-[1.1] tracking-tight text-slate-900 sm:text-6xl lg:text-7xl">
              Eksplorasi Taksonomi <br className="hidden sm:block" />
              <span className="text-emerald-700">Botani Phanerogamae</span>
            </h1>
            <p className="mx-auto max-w-2xl text-base leading-relaxed text-slate-700 sm:text-xl">
              Media pengenalan ilmiah &amp; pembelajaran tumbuhan berbiji (
              <span className="font-serif italic">Spermatophyta</span>) hasil
              observasi ekstensif di Provinsi Sumatera Utara untuk mahasiswa,
              dosen, dan peneliti botani.
            </p>
            <div className="flex flex-col items-center justify-center gap-4 pt-4 sm:flex-row">
              <Link
                href="/tumbuhan"
                className="btn-primary w-full px-8 py-4 text-white sm:w-auto"
              >
                Jelajahi {stats.totalSpecies} Spesimen{" "}
                <ArrowRight className="size-5" />
              </Link>
              <Link
                href="/materi"
                className="btn-secondary w-full px-8 py-4 text-base sm:w-auto"
              >
                Modul Pembelajaran <BookOpenCheck className="size-5" />
              </Link>
            </div>
          </div>
        </div>
      </section>

      <section className="relative z-10 border-y border-emerald-200 bg-emerald-50 py-12">
        <div className="page-container grid grid-cols-2 gap-6 text-center sm:gap-8 md:grid-cols-4">
          {[
            {
              value: `${stats.totalSpecies}+`,
              label: "Spesimen Terkatalog",
              note: "Herbarium Sumatera Utara",
              color: "text-slate-900",
            },
            {
              value: stats.gymnospermae,
              label: "Gymnospermae",
              note: "Tumbuhan Berbiji Terbuka",
              color: "text-amber-700",
            },
            {
              value: stats.angiospermae,
              label: "Angiospermae",
              note: "Monokotil & Dikotil",
              color: "text-emerald-700",
            },
            {
              value: `${modules.length} Modul`,
              label: "Teori Ilmiah & Kuis",
              note: "Berdasarkan Taksonomi",
              color: "text-teal-700",
            },
          ].map((item) => (
            <article
              key={item.label}
              className="rounded-md border border-emerald-200 bg-white p-6 shadow-lg"
            >
              <strong
                className={`mb-2 block text-3xl font-extrabold tracking-tight sm:text-5xl ${item.color}`}
              >
                {item.value}
              </strong>
              <span className="text-xs font-semibold uppercase tracking-wider text-emerald-700 sm:text-sm">
                {item.label}
              </span>
              <span className="mt-1 block text-[11px] text-slate-600">
                {item.note}
              </span>
            </article>
          ))}
        </div>
      </section>

      <section className="page-container py-20 sm:py-28">
        <div className="mb-12 flex flex-col justify-between gap-6 md:flex-row md:items-end">
          <div>
            <p className="eyebrow mb-2">🍃 Koleksi Pilihan</p>
            <h2 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
              Sorotan Spesimen Tumbuhan
            </h2>
            <p className="mt-2 max-w-xl text-sm text-slate-600 sm:text-base">
              Beragam tumbuhan berbiji unggulan yang diamati dari ekosistem
              hutan tropis dan kawasan observasi Sumatera Utara.
            </p>
          </div>
          <Link
            href="/tumbuhan"
            className="btn-secondary shrink-0 self-start shadow-md md:self-auto"
          >
            Lihat Semua Katalog ({stats.totalSpecies}){" "}
            <ArrowRight className="size-4" />
          </Link>
        </div>
        <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          {featured.map((plant) => (
            <PlantCard key={plant.id} plant={plant} />
          ))}
        </div>
      </section>

      <section className="border-y border-emerald-200 bg-emerald-50 py-20">
        <div className="page-container">
          <div className="mx-auto mb-16 max-w-3xl text-center">
            <p className="mb-2 text-xs font-bold uppercase tracking-widest text-amber-700">
              📚 Modul Akademis
            </p>
            <h2 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
              Kurikulum Botani Phanerogamae
            </h2>
            <p className="mt-3 text-sm text-slate-700 sm:text-base">
              Pelajari landasan teori botani dari Gymnospermae hingga
              Angiospermae beserta evaluasi pretest dan posttest interaktif.
            </p>
          </div>
          <div className="grid gap-8 md:grid-cols-3">
            {modules.slice(0, 3).map((module) => (
              <ModuleCard compact key={module.id} module={module} />
            ))}
          </div>
          <div className="mt-12 text-center">
            <Link
              href="/materi"
              className="btn-secondary bg-emerald-100 px-8 py-3.5 shadow-lg"
            >
              Lihat Seluruh Modul &amp; Kuis Pembelajaran
            </Link>
          </div>
        </div>
      </section>

      <section className="page-container py-20 sm:py-28">
        <div className="mx-auto mb-12 max-w-3xl text-center">
          <p className="eyebrow mb-2">📍 Ekosistem Pembelajaran</p>
          <h2 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
            Data lapangan menjadi pengalaman belajar
          </h2>
        </div>
        <div className="grid gap-6 md:grid-cols-3">
          {[
            {
              icon: Microscope,
              title: "Data ilmiah terstruktur",
              body: "Taksonomi, morfologi, fisiologi, media, dan catatan observasi saling terhubung.",
            },
            {
              icon: MapPin,
              title: "Berbasis observasi lokal",
              body: "Koleksi menyoroti keanekaragaman tumbuhan berbiji dari berbagai wilayah Sumatera Utara.",
            },
            {
              icon: GraduationCap,
              title: "Siap untuk evaluasi",
              body: "Materi per subbab terhubung dengan bank soal dan riwayat hasil belajar mahasiswa.",
            },
          ].map((item) => {
            const Icon = item.icon;
            return (
              <article
                key={item.title}
                className="rounded-2xl border border-emerald-200 bg-white p-6 shadow-lg"
              >
                <Icon className="size-8 text-emerald-600" />
                <h3 className="mt-5 text-lg font-bold text-slate-900">
                  {item.title}
                </h3>
                <p className="mt-2 text-sm leading-6 text-slate-600">
                  {item.body}
                </p>
              </article>
            );
          })}
        </div>
      </section>

      <section className="page-container pb-16">
        <div className="flex flex-col items-center justify-between gap-8 overflow-hidden rounded-3xl border border-emerald-300 bg-emerald-100 p-8 text-center shadow-lg sm:flex-row sm:p-14 sm:text-left">
          <div className="max-w-2xl space-y-3">
            <span className="inline-flex rounded-md border border-emerald-300 bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
              🚀 Bergabung Sebagai Mahasiswa / Dosen
            </span>
            <h2 className="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
              Mulai Eksplorasi Botani Phanerogamae
            </h2>
            <p className="text-sm text-slate-700 sm:text-base">
              Daftarkan akun untuk mengikuti evaluasi pembelajaran, atau masuk
              untuk melanjutkan aktivitas Anda.
            </p>
          </div>
          <div className="flex w-full shrink-0 flex-col gap-3 sm:w-auto sm:flex-row">
            <Link
              href="/register"
              className="btn-primary px-7 text-white py-3.5"
            >
              Daftar Sekarang
            </Link>
            <Link href="/login" className="btn-secondary px-7 py-3.5">
              Masuk Akun
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
