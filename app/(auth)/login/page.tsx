import type { Metadata } from "next";
import Link from "next/link";
import { Suspense } from "react";
import { BotaniLogo } from "@/components/botani-logo";
import { LoginForm } from "@/components/auth/login-form";
import { AuthPageFallback } from "@/components/ui/runtime-fallbacks";
import { redirectAuthenticatedUser } from "@/server/auth";

export const metadata: Metadata = { title: "Masuk" };
type LoginPageProps = { searchParams: Promise<Record<string, string | string[] | undefined>> };

export default function LoginPage({ searchParams }: LoginPageProps) {
  return (
    <Suspense fallback={<AuthPageFallback />}>
      <LoginContent searchParams={searchParams} />
    </Suspense>
  );
}

async function LoginContent({ searchParams }: LoginPageProps) {
  await redirectAuthenticatedUser();
  const params = await searchParams;
  const registered = params.registered === "1";
  const confirmed = typeof params.confirmed === "string" && /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(params.confirmed);
  const inactive = params.error === "inactive";
  const confirmationError = params.error === "confirmation";
  return <div className="flex min-h-[85vh] items-center justify-center px-4 py-12 sm:px-6 lg:px-8"><div className="relative z-10 w-full max-w-md space-y-8 rounded-md border border-emerald-300 bg-white p-8 shadow-lg sm:p-10"><header className="text-center"><BotaniLogo className="mx-auto mb-4 size-24" /><h1 className="text-3xl font-extrabold tracking-tight text-slate-900">Masuk ke <span className="text-emerald-700">Platform</span></h1><p className="mt-2 text-sm text-slate-600">Akses herbarium digital &amp; evaluasi pembelajaran Phanerogamae</p></header>{registered && <p className="rounded-md bg-emerald-50 p-3 text-sm text-emerald-800">Pendaftaran diterima. Periksa email dan klik tombol konfirmasi sebelum masuk.</p>}{confirmed && <p className="rounded-md bg-emerald-50 p-3 text-sm text-emerald-800">Email berhasil dikonfirmasi. Akunmu sudah aktif, silakan masuk.</p>}{inactive && <p className="rounded-md bg-rose-50 p-3 text-sm text-rose-700">Akun sedang dinonaktifkan. Hubungi administrator.</p>}{confirmationError && <p className="rounded-md bg-rose-50 p-3 text-sm text-rose-700">Tautan konfirmasi tidak valid atau kedaluwarsa.</p>}<LoginForm /><p className="border-t border-emerald-200 pt-3 text-center text-xs text-slate-600">Belum memiliki akun mahasiswa? <Link href="/register" className="font-bold text-emerald-700 hover:text-emerald-900">Daftar di sini</Link></p></div></div>;
}
