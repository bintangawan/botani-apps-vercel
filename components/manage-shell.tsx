"use client";

import { BookOpen, ClipboardList, Globe2, LayoutDashboard, Leaf, Menu, Users, X } from "lucide-react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState, type ReactNode } from "react";
import { BotaniLogo } from "@/components/botani-logo";
import { SignOutButton } from "@/components/sign-out-button";
import { cn } from "@/lib/utils";
import type { Tables } from "@/types/database";

type ManageShellProps = { profile: Tables<"profiles">; children: ReactNode };

export function ManageShell({ profile, children }: ManageShellProps) {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);
  const dashboardHref = profile.role === "admin" ? "/admin/dashboard" : "/dosen/dashboard";
  const items = [
    { href: dashboardHref, label: "Dashboard Utama", icon: LayoutDashboard },
    { href: "/manage/plants", label: "Katalog Tumbuhan", icon: Leaf },
    { href: "/manage/modules", label: "Modul Pembelajaran", icon: BookOpen },
    { href: "/manage/quizzes", label: "Kuis & Bank Soal", icon: ClipboardList },
    ...(profile.role === "admin" ? [{ href: "/admin/users", label: "Kelola Pengguna & Dosen", icon: Users }] : []),
  ];

  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    return () => { document.body.style.overflow = ""; };
  }, [open]);

  const navigation = (mobile: boolean) => (
    <nav className={mobile ? "space-y-2" : "flex-grow space-y-1.5 overflow-y-auto p-4"} aria-label="Navigasi pengelolaan">
      {!mobile && <p className="px-3 py-2 text-[11px] font-bold uppercase tracking-widest text-slate-500">Menu Utama</p>}
      {items.map((item, index) => {
        const Icon = item.icon;
        const active = pathname === item.href || pathname.startsWith(`${item.href}/`);
        return (
          <div key={item.href}>
            {!mobile && index === 1 && <p className="px-3 pb-2 pt-6 text-[11px] font-bold uppercase tracking-widest text-slate-500">Manajemen Konten</p>}
            {!mobile && profile.role === "admin" && item.href === "/admin/users" && <p className="px-3 pb-2 pt-6 text-[11px] font-bold uppercase tracking-widest text-slate-500">Hak Akses Admin</p>}
            <Link href={item.href} onClick={() => setOpen(false)} className={cn("flex items-center gap-3 rounded-md px-4 py-3 text-sm font-semibold text-slate-700 transition-all hover:bg-emerald-50 hover:text-emerald-700", active && "bg-emerald-600 text-slate-900 shadow-lg shadow-emerald-900/10")}>
              <Icon className="size-5" />{item.label}
            </Link>
          </div>
        );
      })}
      {!mobile && <><p className="px-3 pb-2 pt-6 text-[11px] font-bold uppercase tracking-widest text-slate-500">Publik & Akun</p><Link href="/" className="flex items-center gap-3 rounded-md px-4 py-3 text-sm font-semibold text-slate-600 transition-all hover:bg-emerald-50 hover:text-emerald-800"><Globe2 className="size-5" />Kembali ke Situs Utama</Link></>}
    </nav>
  );

  return (
    <div className="min-h-screen bg-botanical-50 text-slate-800">
      <aside className="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-emerald-200 bg-white lg:flex">
        <div className="flex items-center gap-3 border-b border-emerald-200 p-6">
          <span className="block size-14 shrink-0"><BotaniLogo decorative className="size-full" /></span>
          <div>
            <span className="block text-lg font-extrabold tracking-tight text-slate-900">Botani<span className="text-emerald-700">Panel</span></span>
            <span className="block text-[10px] font-bold uppercase tracking-widest text-emerald-700">{profile.role === "admin" ? "Administrator" : "Dosen Pengampu"}</span>
          </div>
        </div>
        {navigation(false)}
        <div className="border-t border-emerald-200 bg-white p-4">
          <div className="mb-2 rounded-md border border-emerald-200 bg-white p-3">
            <span className="block truncate text-xs font-bold text-slate-900">{profile.name}</span>
            <span className="block truncate text-[10px] text-emerald-700">{profile.email}</span>
          </div>
          <SignOutButton />
        </div>
      </aside>

      <header className="fixed inset-x-0 top-0 z-30 flex h-16 items-center justify-between border-b border-emerald-200 bg-white px-4 lg:hidden">
        <div className="flex items-center gap-2.5"><span className="block size-11 shrink-0"><BotaniLogo decorative className="size-full" /></span><span className="text-base font-bold text-slate-900">Botani<span className="text-emerald-700">Panel</span></span></div>
        <button type="button" onClick={() => setOpen(true)} className="grid size-10 place-items-center rounded-md border border-emerald-300 bg-white text-emerald-700" aria-label="Buka navigasi"><Menu className="size-6" /></button>
      </header>

      <main className="min-h-screen flex-grow pt-16 lg:ml-64 lg:pt-0">
        <div className="p-4 sm:p-8">{children}</div>
      </main>

      {open && <button type="button" aria-label="Tutup navigasi" onClick={() => setOpen(false)} className="fixed inset-0 z-40 bg-emerald-950/30 lg:hidden" />}
      <aside className={cn("fixed inset-y-0 right-0 z-50 flex w-80 flex-col justify-between overflow-y-auto border-l border-emerald-200 bg-emerald-50 p-6 shadow-lg transition-transform lg:hidden", open ? "translate-x-0" : "translate-x-full")}>
        <div className="space-y-6">
          <div className="flex items-center justify-between border-b border-emerald-200 pb-4"><div className="flex items-center gap-2.5"><BotaniLogo decorative className="size-8" /><span className="text-lg font-extrabold text-slate-900">Botani<span className="text-emerald-700">Panel</span></span></div><button type="button" onClick={() => setOpen(false)} className="text-slate-600 hover:text-emerald-800" aria-label="Tutup navigasi"><X className="size-6" /></button></div>
          <div className="rounded-md border border-emerald-200 bg-white p-3"><span className="block text-sm font-bold text-slate-900">{profile.name}</span><span className="text-xs font-semibold uppercase text-emerald-700">{profile.role}</span></div>
          {navigation(true)}
          <Link href="/" onClick={() => setOpen(false)} className="flex items-center gap-3 rounded-md px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-emerald-100 hover:text-emerald-800"><Globe2 className="size-5" />Kembali ke Situs Utama</Link>
        </div>
        <div className="border-t border-emerald-200 pt-6"><SignOutButton /></div>
      </aside>
    </div>
  );
}
