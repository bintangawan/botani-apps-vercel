import { Menu } from "lucide-react";
import Link from "next/link";
import { Brand } from "@/components/brand";

const links = [
  { href: "/", label: "Beranda" },
  { href: "/tumbuhan", label: "Galeri Tumbuhan" },
  { href: "/materi", label: "Modul Pembelajaran" },
  { href: "/tentang", label: "Tentang" },
] as const;

export function PublicHeaderFallback() {
  return (
    <header className="sticky top-0 z-40 border-b border-emerald-200 bg-white">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="flex h-20 items-center justify-between">
          <Brand />
          <nav className="hidden items-center gap-1 md:flex lg:gap-2" aria-label="Navigasi utama">
            {links.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                className="rounded-md px-4 py-2 text-sm font-medium text-slate-700 transition-all hover:bg-emerald-50 hover:text-emerald-800"
              >
                {item.label}
              </Link>
            ))}
          </nav>
          <div className="hidden items-center gap-3 md:flex">
            <Link href="/login" className="rounded-md px-4 py-2 text-sm font-medium text-slate-700">
              Masuk
            </Link>
            <Link href="/register" className="rounded-md bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/10">
              Daftar
            </Link>
          </div>
          <span className="grid size-11 place-items-center rounded-md border border-emerald-300 bg-emerald-50 text-emerald-700 md:hidden">
            <Menu className="size-6" />
          </span>
        </div>
      </div>
    </header>
  );
}
