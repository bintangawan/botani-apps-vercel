import type { ReactNode } from "react";

type PageHeaderProps = {
  eyebrow?: string;
  title: string;
  description?: string;
  actions?: ReactNode;
};

export function PageHeader({ eyebrow, title, description, actions }: PageHeaderProps) {
  return (
    <header className="flex flex-col items-center justify-between gap-6 rounded-md border border-emerald-300 bg-emerald-100 p-6 shadow-xl sm:flex-row sm:p-8">
      <div className="max-w-3xl space-y-1.5 text-center sm:text-left">
        {eyebrow && <p className="inline-flex rounded-md border border-emerald-300 bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{eyebrow}</p>}
        <h1 className="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{title}</h1>
        {description && <p className="max-w-2xl text-sm leading-6 text-slate-700">{description}</p>}
      </div>
      {actions && <div className="shrink-0">{actions}</div>}
    </header>
  );
}
