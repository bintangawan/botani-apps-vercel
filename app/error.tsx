"use client";

import { AlertTriangle } from "lucide-react";
import Link from "next/link";

export default function GlobalError({ reset }: { error: Error & { digest?: string }; reset: () => void }) {
  return (
    <main className="grid min-h-[70vh] place-items-center px-4">
      <div className="max-w-lg text-center">
        <AlertTriangle className="mx-auto size-14 text-amber-600" />
        <h1 className="mt-5 text-3xl font-black text-slate-950">Ada sesuatu yang belum beres.</h1>
        <p className="mt-3 text-slate-600">Data belum berhasil dimuat. Silakan coba lagi dalam beberapa saat. Jika masih bermasalah, hubungi pengelola aplikasi.</p>
        <button type="button" onClick={reset} className="btn-primary mt-7">Coba lagi</button>
        <Link href="/" className="mt-4 block text-sm font-semibold text-emerald-700">Kembali ke beranda</Link>
      </div>
    </main>
  );
}
