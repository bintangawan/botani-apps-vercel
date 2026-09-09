import Link from "next/link";
import { cacheLife } from "next/cache";
import { BotaniLogo } from "@/components/botani-logo";

async function getCurrentYear(): Promise<number> {
  "use cache";
  cacheLife({ stale: 86400, revalidate: 86400, expire: 604800 });
  return new Date().getFullYear();
}

export async function PublicFooter() {
  const currentYear = await getCurrentYear();
  return (
    <footer className="relative z-10 mt-24 border-t border-emerald-200 bg-emerald-50 pb-12 pt-16">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mb-12 grid grid-cols-1 gap-12 md:grid-cols-4">
          <div className="space-y-4 md:col-span-2">
            <div className="flex items-center gap-3">
              <span className="block size-14 shrink-0">
                <BotaniLogo decorative className="size-full" />
              </span>
              <div>
                <span className="text-xl font-bold tracking-tight text-slate-900">
                  Botani<span className="text-emerald-700">Phanerogamae</span>
                </span>
                <span className="block text-xs font-medium text-emerald-700">
                  Herbarium Digital & Pembelajaran
                </span>
              </div>
            </div>
            <p className="max-w-sm text-sm leading-relaxed text-slate-600">
              Platform dokumentasi ilmiah tumbuhan berbiji hasil eksplorasi
              lapangan di Provinsi Sumatera Utara.
            </p>
            <div className="flex flex-wrap gap-2.5 pt-2">
              {[
                "Eksplorasi Taksonomi",
                "Spermatophyta Sumatera",
                "Kurikulum Biologi",
              ].map((label) => (
                <span
                  key={label}
                  className="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700"
                >
                  {label}
                </span>
              ))}
            </div>
          </div>
          <div>
            <h2 className="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-900">
              Navigasi Utama
            </h2>
            <ul className="space-y-2.5 text-sm text-slate-600">
              <li>
                <Link
                  className="transition-colors hover:text-emerald-700"
                  href="/"
                >
                  Beranda
                </Link>
              </li>
              <li>
                <Link
                  className="transition-colors hover:text-emerald-700"
                  href="/tumbuhan"
                >
                  Jelajahi Galeri Tumbuhan
                </Link>
              </li>
              <li>
                <Link
                  className="transition-colors hover:text-emerald-700"
                  href="/materi"
                >
                  Modul Pembelajaran
                </Link>
              </li>
              <li>
                <Link
                  className="transition-colors hover:text-emerald-700"
                  href="/tentang"
                >
                  Tentang Penelitian
                </Link>
              </li>
            </ul>
          </div>
          <div>
            <h2 className="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-900">
              Akses Pengguna
            </h2>
            <ul className="space-y-2.5 text-sm text-slate-600">
              <li>
                <Link
                  className="transition-colors hover:text-emerald-700"
                  href="/login"
                >
                  Masuk Akun
                </Link>
              </li>
              <li>
                <Link
                  className="transition-colors hover:text-emerald-700"
                  href="/register"
                >
                  Daftar Akun
                </Link>
              </li>
            </ul>
          </div>
        </div>
        <div className="flex flex-col items-center justify-between gap-4 border-t border-emerald-200 pt-8 text-xs text-slate-500 sm:flex-row">
          <p>
            © {currentYear} Botani Phanerogamae Apps. Seluruh Hak
            Cipta Dilindungi.
          </p>
          <p>
            Dikembangkan untuk observasi taksonomi & evaluasi pembelajaran
            Biologi.
          </p>
        </div>
      </div>
    </footer>
  );
}
