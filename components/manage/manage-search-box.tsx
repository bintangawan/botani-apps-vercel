"use client";

import { LoaderCircle, Search } from "lucide-react";
import Link from "next/link";
import { useEffect, useState } from "react";
import { trpc } from "@/lib/trpc/client";

type SearchInputProps = {
  defaultValue: string;
  placeholder: string;
};

function useDebouncedSearch(defaultValue: string) {
  const [value, setValue] = useState(defaultValue);
  const [debouncedValue, setDebouncedValue] = useState(defaultValue.trim());
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const timer = window.setTimeout(() => setDebouncedValue(value.trim()), 250);
    return () => window.clearTimeout(timer);
  }, [value]);

  return { value, setValue, debouncedValue, open, setOpen };
}

function SearchInput({
  value,
  setValue,
  placeholder,
  loading,
}: {
  value: string;
  setValue: (value: string) => void;
  placeholder: string;
  loading: boolean;
}) {
  return (
    <div className="relative">
      <Search className="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
      <input
        className="form-input pl-10 pr-10"
        name="search"
        value={value}
        placeholder={placeholder}
        autoComplete="off"
        onChange={(event) => setValue(event.target.value)}
      />
      {loading && (
        <LoaderCircle className="pointer-events-none absolute right-4 top-1/2 size-4 -translate-y-1/2 animate-spin text-emerald-600" />
      )}
    </div>
  );
}

function EmptySuggestions() {
  return (
    <p className="px-4 py-3 text-sm text-slate-500">
      Tidak ada rekomendasi yang cocok.
    </p>
  );
}

export function PlantManageSearch({ defaultValue, placeholder }: SearchInputProps) {
  const { value, setValue, debouncedValue, open, setOpen } = useDebouncedSearch(defaultValue);
  const enabled = debouncedValue.length >= 2;
  const suggestions = trpc.plants.manageSuggestions.useQuery(
    { search: debouncedValue },
    { enabled, staleTime: 60_000 },
  );

  return (
    <div
      className="relative"
      onFocusCapture={() => setOpen(true)}
      onBlurCapture={(event) => {
        if (!event.currentTarget.contains(event.relatedTarget)) setOpen(false);
      }}
    >
      <SearchInput
        value={value}
        setValue={(nextValue) => {
          setValue(nextValue);
          setOpen(true);
        }}
        placeholder={placeholder}
        loading={suggestions.isFetching}
      />
      {open && enabled && suggestions.isSuccess && (
        <div className="absolute inset-x-0 top-[calc(100%+0.375rem)] z-30 overflow-hidden rounded-md border border-emerald-200 bg-white shadow-xl">
          {suggestions.data.length === 0 ? (
            <EmptySuggestions />
          ) : (
            <ul aria-label="Rekomendasi tumbuhan">
              {suggestions.data.map((plant) => (
                <li key={plant.id}>
                  <Link
                    href={`/manage/plants/${plant.id}/edit`}
                    className="block border-b border-emerald-100 px-4 py-3 last:border-b-0 hover:bg-emerald-50 focus:bg-emerald-50"
                  >
                    <span className="block text-sm font-bold text-slate-950">
                      {plant.local_name}
                    </span>
                    <span className="mt-0.5 block text-xs text-slate-500">
                      <span className="font-serif italic">{plant.scientific_name}</span>
                      {` · ${plant.code} · ${plant.group_type}`}
                    </span>
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </div>
      )}
    </div>
  );
}

export function ModuleManageSearch({ defaultValue, placeholder }: SearchInputProps) {
  const { value, setValue, debouncedValue, open, setOpen } = useDebouncedSearch(defaultValue);
  const enabled = debouncedValue.length >= 2;
  const suggestions = trpc.modules.manageSuggestions.useQuery(
    { search: debouncedValue },
    { enabled, staleTime: 60_000 },
  );

  return (
    <div
      className="relative flex-1"
      onFocusCapture={() => setOpen(true)}
      onBlurCapture={(event) => {
        if (!event.currentTarget.contains(event.relatedTarget)) setOpen(false);
      }}
    >
      <SearchInput
        value={value}
        setValue={(nextValue) => {
          setValue(nextValue);
          setOpen(true);
        }}
        placeholder={placeholder}
        loading={suggestions.isFetching}
      />
      {open && enabled && suggestions.isSuccess && (
        <div className="absolute inset-x-0 top-[calc(100%+0.375rem)] z-30 overflow-hidden rounded-md border border-emerald-200 bg-white shadow-xl">
          {suggestions.data.length === 0 ? (
            <EmptySuggestions />
          ) : (
            <ul aria-label="Rekomendasi modul">
              {suggestions.data.map((module) => (
                <li key={module.id}>
                  <Link
                    href={`/manage/modules/${module.id}/edit`}
                    className="block border-b border-emerald-100 px-4 py-3 last:border-b-0 hover:bg-emerald-50 focus:bg-emerald-50"
                  >
                    <span className="block text-sm font-bold text-slate-950">
                      {module.title}
                    </span>
                    <span className="mt-0.5 block text-xs text-slate-500">
                      Bab {module.module_order} · {module.status}
                    </span>
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </div>
      )}
    </div>
  );
}
