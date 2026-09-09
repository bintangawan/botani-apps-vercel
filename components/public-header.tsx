"use client";

import { BookOpen, Home, Info, Leaf, Menu, X } from "lucide-react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { BotaniLogo } from "@/components/botani-logo";
import { Brand } from "@/components/brand";
import { SignOutButton } from "@/components/sign-out-button";
import { cn } from "@/lib/utils";
import type { Tables } from "@/types/database";

const navItems = [
  { href: "/", label: "Beranda", icon: Home },
  { href: "/tumbuhan", label: "Galeri Tumbuhan", icon: Leaf },
  { href: "/materi", label: "Modul Pembelajaran", icon: BookOpen },
  { href: "/tentang", label: "Tentang Platform", icon: Info },
] as const;

function dashboardPath(profile: Tables<"profiles">): string {
  if (profile.role === "admin") return "/admin/dashboard";
  if (profile.role === "dosen") return "/dosen/dashboard";
  return "/mahasiswa/dashboard";
}

type PublicHeaderProps = { profile: Tables<"profiles"> | null };

export function PublicHeader({ profile }: PublicHeaderProps) {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);
  const active = (href: string): boolean =>
    href === "/" ? pathname === "/" : pathname.startsWith(href);

  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    return () => {
      document.body.style.overflow = "";
    };
  }, [open]);

  return (
    <>
      <header className="sticky top-0 z-40 border-b border-emerald-200 bg-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex h-20 items-center justify-between">
            <Brand />
            <nav
              className="hidden items-center gap-1 md:flex lg:gap-2"
              aria-label="Navigasi utama"
            >
              {navItems.map((item) => (
                <Link
                  key={item.href}
                  href={item.href}
                  className={cn(
                    "rounded-md px-4 py-2 text-sm font-medium text-slate-700 transition-all hover:bg-emerald-50 hover:text-emerald-800",
                    active(item.href) &&
                      "border border-emerald-200 bg-emerald-100 text-emerald-700",
                  )}
                >
                  {item.label === "Tentang Platform" ? "Tentang" : item.label}
                </Link>
              ))}
            </nav>
            <div className="hidden items-center gap-3 md:flex">
              {profile ? (
                <>
                  <Link
                    href={dashboardPath(profile)}
                    className="flex items-center gap-2 rounded-md border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 shadow-md transition-all hover:bg-emerald-100 hover:text-emerald-800"
                  >
                    <span className="size-2 animate-pulse rounded-md bg-emerald-400" />
                    Dashboard (
                    {profile.role.charAt(0).toUpperCase() +
                      profile.role.slice(1)}
                    )
                  </Link>
                  <SignOutButton compact />
                </>
              ) : (
                <>
                  <Link
                    href="/login"
                    className="rounded-md px-4 py-2 text-sm font-medium text-slate-700 transition-all hover:bg-emerald-50 hover:text-emerald-800"
                  >
                    Masuk
                  </Link>
                  <Link
                    href="/register"
                    className="rounded-md bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/10 transition-all hover:bg-emerald-500"
                  >
                    Daftar
                  </Link>
                </>
              )}
            </div>
            <button
              type="button"
              onClick={() => setOpen(true)}
              className="grid size-11 place-items-center rounded-md border border-emerald-300 bg-emerald-50 text-emerald-700 transition-all hover:bg-emerald-100 hover:text-emerald-800 md:hidden"
              aria-label="Buka menu"
            >
              <Menu className="size-6" />
            </button>
          </div>
        </div>
      </header>

      <div
        className={cn(
          "fixed inset-0 z-50 overflow-hidden md:hidden",
          open ? "pointer-events-auto" : "pointer-events-none",
        )}
        aria-hidden={!open}
      >
        <button
          type="button"
          aria-label="Tutup menu"
          onClick={() => setOpen(false)}
          className={cn(
            "fixed inset-0 bg-emerald-950/30 transition-opacity duration-300",
            open ? "opacity-100" : "opacity-0",
          )}
        />
        <div className="fixed inset-y-0 right-0 flex max-w-full pl-10">
          <aside
            className={cn(
              "flex w-screen max-w-xs flex-col justify-between border-l border-emerald-200 bg-emerald-50 shadow-lg transition-transform duration-300",
              open ? "translate-x-0" : "translate-x-full",
            )}
          >
            <div className="overflow-y-auto px-6 pb-4 pt-6">
              <div className="flex items-center justify-between border-b border-emerald-200 pb-6">
                <div className="flex items-center gap-2.5">
                  <span className="block size-12 shrink-0">
                    <BotaniLogo decorative className="size-full" />
                  </span>
                  <div>
                    <span className="text-base font-bold text-slate-900">
                      Botani
                      <span className="text-emerald-700">Phanerogamae</span>
                    </span>
                    <span className="block text-[9px] font-semibold uppercase tracking-wider text-emerald-700">
                      Navigasi Utama
                    </span>
                  </div>
                </div>
                <button
                  type="button"
                  onClick={() => setOpen(false)}
                  className="rounded-md p-2 text-slate-600 transition-all hover:bg-emerald-100 hover:text-emerald-800"
                  aria-label="Tutup menu"
                >
                  <X className="size-6" />
                </button>
              </div>
              <nav className="mt-8 space-y-2" aria-label="Navigasi seluler">
                {navItems.map((item) => {
                  const Icon = item.icon;
                  return (
                    <Link
                      key={item.href}
                      href={item.href}
                      onClick={() => setOpen(false)}
                      className={cn(
                        "flex items-center gap-3 rounded-md px-4 py-3 text-sm font-medium text-slate-700 transition-all hover:bg-emerald-100 hover:text-emerald-800",
                        active(item.href) &&
                          "border border-emerald-300 bg-emerald-100 text-emerald-700 shadow-md",
                      )}
                    >
                      <Icon className="size-5" />
                      {item.label}
                    </Link>
                  );
                })}
              </nav>
            </div>
            <div className="border-t border-emerald-200 bg-emerald-50 p-6">
              {profile ? (
                <>
                  <div className="mb-4 rounded-md border border-emerald-200 bg-white p-3">
                    <span className="block text-xs text-slate-600">
                      Masuk sebagai:
                    </span>
                    <span className="block truncate text-sm font-bold text-slate-900">
                      {profile.name}
                    </span>
                    <span className="mt-1 inline-block rounded-md border border-emerald-300 bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                      Role: {profile.role}
                    </span>
                  </div>
                  <Link
                    href={dashboardPath(profile)}
                    onClick={() => setOpen(false)}
                    className="mb-2 flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-3 text-sm font-semibold text-slate-900 shadow-lg shadow-emerald-900/10 transition-all hover:bg-emerald-500"
                  >
                    Buka Dashboard
                  </Link>
                  <SignOutButton />
                </>
              ) : (
                <div className="space-y-2.5">
                  <Link
                    href="/login"
                    onClick={() => setOpen(false)}
                    className="flex w-full items-center justify-center rounded-md border border-emerald-300 bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-700 transition-all hover:bg-emerald-200"
                  >
                    Masuk Akun
                  </Link>
                  <Link
                    href="/register"
                    onClick={() => setOpen(false)}
                    className="flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-3 text-sm font-semibold text-slate-900 shadow-lg shadow-emerald-900/10 transition-all hover:bg-emerald-500"
                  >
                    Daftar Akun
                  </Link>
                </div>
              )}
            </div>
          </aside>
        </div>
      </div>
    </>
  );
}
