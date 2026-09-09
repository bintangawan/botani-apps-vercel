import { Sprout } from "lucide-react";

export function EmptyState({ title, description }: { title: string; description: string }) {
  return (
    <div className="panel grid min-h-64 place-items-center p-8 text-center">
      <div><Sprout className="mx-auto size-10 text-emerald-500" /><h2 className="mt-4 text-lg font-extrabold text-slate-900">{title}</h2><p className="mt-2 text-sm text-slate-500">{description}</p></div>
    </div>
  );
}
