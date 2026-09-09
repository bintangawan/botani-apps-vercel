import Link from "next/link";
import { Leaf } from "lucide-react";

export default function NotFound() {
  return (
    <main className="grid min-h-screen place-items-center bg-emerald-50 px-4">
      <div className="max-w-lg text-center">
        <Leaf className="mx-auto size-14 text-emerald-600" />
        <p className="mt-6 eyebrow">404 · Tidak ditemukan</p>
        <h1 className="mt-3 text-4xl font-black text-slate-950">Halaman ini belum tumbuh.</h1>
        <p className="mt-4 leading-7 text-slate-600">Data atau halaman yang kamu cari tidak tersedia di herbarium digital.</p>
        <Link href="/" className="btn-primary mt-8">Kembali ke beranda</Link>
      </div>
    </main>
  );
}
