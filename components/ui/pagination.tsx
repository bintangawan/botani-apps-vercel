import Link from "next/link";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { cn } from "@/lib/utils";

type PaginationProps = { page: number; pageSize: number; total: number; pathname: string; searchParams: Record<string, string | undefined> };

function hrefFor(pathname: string, searchParams: Record<string, string | undefined>, page: number): string {
  const params = new URLSearchParams();
  for (const [key, value] of Object.entries(searchParams)) {
    if (value && key !== "page") params.set(key, value);
  }
  params.set("page", String(page));
  return `${pathname}?${params.toString()}`;
}

export function Pagination({ page, pageSize, total, pathname, searchParams }: PaginationProps) {
  const lastPage = Math.max(1, Math.ceil(total / pageSize));
  if (lastPage <= 1) return null;
  return (
    <nav className="mt-8 flex items-center justify-center gap-3" aria-label="Paginasi">
      <Link aria-disabled={page <= 1} tabIndex={page <= 1 ? -1 : undefined} href={hrefFor(pathname, searchParams, Math.max(1, page - 1))} className={cn("btn-secondary px-4", page <= 1 && "pointer-events-none opacity-40")}><ChevronLeft className="size-4" /> Sebelumnya</Link>
      <span className="rounded-md bg-emerald-100 px-4 py-3 text-sm font-bold text-emerald-900">{page} / {lastPage}</span>
      <Link aria-disabled={page >= lastPage} tabIndex={page >= lastPage ? -1 : undefined} href={hrefFor(pathname, searchParams, Math.min(lastPage, page + 1))} className={cn("btn-secondary px-4", page >= lastPage && "pointer-events-none opacity-40")}>Berikutnya <ChevronRight className="size-4" /></Link>
    </nav>
  );
}
