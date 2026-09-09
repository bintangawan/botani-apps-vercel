import type { Metadata } from "next";
import Link from "next/link";
import { Suspense } from "react";
import { RegisterForm } from "@/components/auth/register-form";
import { BotaniLogo } from "@/components/botani-logo";
import { AuthPageFallback } from "@/components/ui/runtime-fallbacks";
import { redirectAuthenticatedUser } from "@/server/auth";

export const metadata: Metadata = { title: "Registrasi Akun" };

export default function RegisterPage() {
  return (
    <Suspense fallback={<AuthPageFallback />}>
      <RegisterContent />
    </Suspense>
  );
}

async function RegisterContent() {
  await redirectAuthenticatedUser();
  return (
    <div className="flex min-h-[85vh] items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
      <div className="relative z-10 w-full max-w-md space-y-8 rounded-md border border-emerald-300 bg-white p-8 shadow-lg sm:p-10">
        <header className="text-center">
          <BotaniLogo className="mx-auto mb-4 size-24" />
          <h1 className="text-3xl font-extrabold tracking-tight text-slate-900">
            Registrasi <span className="text-emerald-700">Akun</span>
          </h1>
          <p className="mt-2 text-sm text-slate-600">
            Buat akun untuk mengikuti pretest &amp; posttest pembelajaran Botani
            Phanerogamae
          </p>
        </header>
        <RegisterForm />
        <p className="border-t border-emerald-200 pt-3 text-center text-xs text-slate-600">
          Sudah punya akun?{" "}
          <Link
            href="/login"
            className="font-bold text-emerald-700 hover:text-emerald-900"
          >
            Masuk ke akun Anda
          </Link>
        </p>
      </div>
    </div>
  );
}
