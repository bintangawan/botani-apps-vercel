import Link from "next/link";
import { BotaniLogo } from "@/components/botani-logo";
import { cn } from "@/lib/utils";

type BrandProps = { compact?: boolean };

export function Brand({ compact = false }: BrandProps) {
  return (
    <Link href="/" className="group flex items-center gap-3" aria-label="Botani Phanerogamae — Beranda">
      <span className={cn(
        "block size-14 shrink-0 transition-transform group-hover:scale-105",
        compact && "size-11",
      )}>
        <BotaniLogo decorative priority className="size-full" />
      </span>
      <span className={cn("leading-none", compact && "hidden sm:block")}>
        <span className="block text-xl font-extrabold tracking-tight text-slate-900 transition-colors group-hover:text-emerald-700">
          Botani<span className="text-emerald-700">Phanerogamae</span>
        </span>
        <span className="mt-1 block text-[10px] font-semibold uppercase tracking-widest text-emerald-700">
          Spermatophyta Sumatera
        </span>
      </span>
    </Link>
  );
}
