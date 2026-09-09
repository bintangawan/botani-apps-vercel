import { BotaniLogo } from "@/components/botani-logo";

type PageContentFallbackProps = {
  label?: string;
  maxWidth?: "2xl" | "4xl" | "5xl" | "7xl";
};

const maxWidthClasses = {
  "2xl": "max-w-2xl",
  "4xl": "max-w-4xl",
  "5xl": "max-w-5xl",
  "7xl": "max-w-7xl",
} as const;

export function PageContentFallback({
  label = "Memuat halaman",
  maxWidth = "7xl",
}: PageContentFallbackProps) {
  return (
    <div
      className={`mx-auto w-full ${maxWidthClasses[maxWidth]}`}
      role="status"
      aria-live="polite"
      aria-label={label}
    >
      <div className="animate-pulse space-y-7">
        <div className="space-y-3">
          <div className="h-5 w-32 rounded-md bg-emerald-100" />
          <div className="h-10 w-full max-w-xl rounded-md bg-emerald-100" />
          <div className="h-5 w-full max-w-2xl rounded-md bg-slate-200" />
        </div>
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: 3 }, (_, index) => (
            <div
              key={index}
              className="h-40 rounded-md border border-emerald-200 bg-white"
            />
          ))}
        </div>
      </div>
    </div>
  );
}

export function AuthPageFallback() {
  return (
    <div
      className="flex min-h-[85vh] items-center justify-center px-4 py-12 sm:px-6 lg:px-8"
      role="status"
      aria-live="polite"
      aria-label="Memuat formulir akun"
    >
      <div className="w-full max-w-md animate-pulse space-y-7 rounded-md border border-emerald-300 bg-white p-8 shadow-lg sm:p-10">
        <BotaniLogo decorative className="mx-auto size-24 opacity-60" />
        <div className="mx-auto h-9 w-64 rounded-md bg-emerald-100" />
        <div className="mx-auto h-5 w-full rounded-md bg-slate-200" />
        <div className="space-y-4">
          <div className="h-12 rounded-md bg-emerald-100" />
          <div className="h-12 rounded-md bg-emerald-100" />
          <div className="h-12 rounded-md bg-emerald-200" />
        </div>
      </div>
    </div>
  );
}

export function ModuleDetailFallback() {
  return (
    <div
      className="relative mx-auto max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8"
      role="status"
      aria-live="polite"
      aria-label="Memuat materi pembelajaran"
    >
      <div className="grid animate-pulse items-start gap-8 lg:grid-cols-[320px_minmax(0,1fr)]">
        <aside className="h-[520px] rounded-md border border-slate-200 bg-white p-4 shadow-lg">
          <div className="h-7 w-32 rounded-md bg-emerald-100" />
          <div className="mt-6 space-y-4">
            {Array.from({ length: 6 }, (_, index) => (
              <div key={index} className="h-10 rounded-md bg-slate-100" />
            ))}
          </div>
        </aside>
        <article className="min-w-0 overflow-hidden rounded-md border border-emerald-200 bg-white">
          <div className="space-y-4 border-b border-emerald-300 bg-emerald-100 p-6 sm:p-9">
            <div className="h-4 w-48 rounded-md bg-emerald-200" />
            <div className="h-10 w-full max-w-2xl rounded-md bg-emerald-200" />
            <div className="h-5 w-44 rounded-md bg-emerald-200" />
          </div>
          <div className="space-y-5 p-6 sm:p-10">
            {Array.from({ length: 5 }, (_, index) => (
              <div
                key={index}
                className={`h-5 rounded-md bg-slate-100 ${index === 4 ? "w-2/3" : "w-full"}`}
              />
            ))}
          </div>
        </article>
      </div>
    </div>
  );
}

export function PlantDetailFallback() {
  return (
    <div
      className="page-container py-12 sm:py-20"
      role="status"
      aria-live="polite"
      aria-label="Memuat detail tumbuhan"
    >
      <div className="grid animate-pulse items-start gap-8 lg:grid-cols-12">
        <div className="aspect-square rounded-md border border-emerald-200 bg-emerald-100 lg:col-span-5" />
        <div className="space-y-6 lg:col-span-7">
          <div className="h-6 w-40 rounded-md bg-emerald-100" />
          <div className="h-12 w-3/4 rounded-md bg-emerald-100" />
          <div className="h-7 w-1/2 rounded-md bg-slate-200" />
          {Array.from({ length: 3 }, (_, index) => (
            <div
              key={index}
              className="h-44 rounded-md border border-emerald-200 bg-white"
            />
          ))}
        </div>
      </div>
    </div>
  );
}

export function ManagementShellFallback() {
  return (
    <div
      className="min-h-screen bg-botanical-50"
      role="status"
      aria-live="polite"
      aria-label="Memuat panel pengelolaan"
    >
      <aside className="fixed inset-y-0 left-0 hidden w-64 border-r border-emerald-200 bg-white p-6 lg:block">
        <BotaniLogo decorative className="size-14 opacity-60" />
        <div className="mt-8 animate-pulse space-y-3">
          {Array.from({ length: 6 }, (_, index) => (
            <div key={index} className="h-11 rounded-md bg-emerald-50" />
          ))}
        </div>
      </aside>
      <main className="min-h-screen p-4 pt-20 sm:p-8 lg:ml-64 lg:pt-8">
        <PageContentFallback />
      </main>
    </div>
  );
}
