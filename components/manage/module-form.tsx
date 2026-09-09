"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { LoaderCircle, Plus, Save, Trash2 } from "lucide-react";
import { useRouter } from "next/navigation";
import { useFieldArray, useForm, useWatch } from "react-hook-form";
import { toast } from "sonner";
import type { z } from "zod";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";
import { moduleInputSchema } from "@/lib/validation/manage-forms";
import { MarkdownEditor } from "@/components/manage/markdown-editor";

type Values = z.input<typeof moduleInputSchema>;
type ParsedValues = z.output<typeof moduleInputSchema>;
type PlantOption = {
  id: number;
  local_name: string;
  scientific_name: string;
  code: string;
};
type ModuleFormProps = {
  initial: Values;
  plants: PlantOption[];
  mode: "create" | "edit";
};

function splitLines(value: string): string[] {
  return value
    .split(/\r?\n/)
    .map((item) => item.trim())
    .filter(Boolean);
}

function FieldInfo({ error, hint }: { error?: string; hint: string }) {
  return <p className={error ? "form-error" : "form-help"}>{error ?? hint}</p>;
}

export function ModuleForm({ initial, plants, mode }: ModuleFormProps) {
  const router = useRouter();
  const {
    register,
    control,
    handleSubmit,
    setValue,
    formState: { errors },
  } = useForm<Values, undefined, ParsedValues>({
    defaultValues: initial,
    resolver: zodResolver(moduleInputSchema),
    mode: "onBlur",
  });
  const { fields, append, remove } = useFieldArray({ control, name: "lessons" });
  const chapterSummary = useWatch({ control, name: "chapterSummary" }) ?? [];
  const lessonValues = useWatch({ control, name: "lessons" }) ?? [];
  const speciesIds = useWatch({ control, name: "speciesIds" }) ?? [];
  const mutation = trpc.modules.save.useMutation();

  const onSubmit = async (values: ParsedValues): Promise<void> => {
    const confirmed = await confirmAction({
      title: mode === "create" ? "Buat modul baru?" : "Simpan perubahan modul?",
      text:
        mode === "create"
          ? "Modul beserta seluruh subbab akan ditambahkan ke kurikulum."
          : "Perubahan modul dan seluruh subbab di dalamnya akan disimpan.",
      confirmText: mode === "create" ? "Ya, buat modul" : "Ya, simpan",
    });
    if (!confirmed) return;

    try {
      await mutation.mutateAsync(values);
      toast.success(mode === "create" ? "Bab berhasil dibuat." : "Bab berhasil diperbarui.");
      router.push("/manage/modules");
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Bab gagal disimpan.");
    }
  };

  const removeLesson = async (index: number): Promise<void> => {
    const lessonTitle = lessonValues[index]?.title.trim();
    const confirmed = await confirmAction({
      title: "Hapus subbab ini?",
      text: lessonTitle
        ? `Subbab “${lessonTitle}” akan dikeluarkan dari modul saat perubahan disimpan.`
        : "Subbab ini akan dikeluarkan dari modul saat perubahan disimpan.",
      confirmText: "Ya, hapus subbab",
      variant: "danger",
    });
    if (confirmed) remove(index);
  };

  const onInvalid = (): void => {
    toast.error("Periksa kembali field modul atau subbab yang ditandai.");
  };

  return (
    <form className="space-y-7" noValidate onSubmit={handleSubmit(onSubmit, onInvalid)}>
      <section className="panel p-6">
        <h2 className="text-lg font-black text-slate-950">Informasi bab</h2>
        <div className="mt-5 grid gap-5 md:grid-cols-2">
          <div className="md:col-span-2">
            <label className="form-label">Judul bab</label>
            <input className="form-input" aria-invalid={Boolean(errors.title)} {...register("title")} />
            <FieldInfo error={errors.title?.message} hint="Wajib · minimal 2, maksimal 255 karakter." />
          </div>
          <div className="md:col-span-2">
            <label className="form-label">Deskripsi</label>
            <textarea className="form-input min-h-24" aria-invalid={Boolean(errors.description)} {...register("description")} />
            <FieldInfo error={errors.description?.message} hint="Opsional · maksimal 10.000 karakter." />
          </div>
          <div>
            <label className="form-label">Urutan bab</label>
            <input className="form-input" type="number" min={1} max={10000} aria-invalid={Boolean(errors.moduleOrder)} {...register("moduleOrder", { valueAsNumber: true })} />
            <FieldInfo error={errors.moduleOrder?.message} hint="Wajib · angka bulat 1–10.000." />
          </div>
          <div>
            <label className="form-label">Estimasi menit</label>
            <input className="form-input" type="number" min={0} max={10000} aria-invalid={Boolean(errors.estimatedMinutes)} {...register("estimatedMinutes", { valueAsNumber: true })} />
            <FieldInfo error={errors.estimatedMinutes?.message} hint="Wajib · angka bulat 0–10.000 menit." />
          </div>
          <div>
            <label className="form-label">Status</label>
            <select className="form-input" {...register("status")}>
              <option value="published">Published</option>
              <option value="draft">Draft</option>
            </select>
            <FieldInfo hint="Draft tidak tampil pada halaman pembelajaran publik." />
          </div>
          <div>
            <label className="form-label">File sumber</label>
            <input className="form-input" aria-invalid={Boolean(errors.sourceFile)} {...register("sourceFile")} />
            <FieldInfo error={errors.sourceFile?.message} hint="Opsional · maksimal 255 karakter." />
          </div>
          <div className="md:col-span-2">
            <label className="form-label">Ringkasan bab (satu poin per baris)</label>
            <textarea
              className="form-input min-h-28"
              aria-invalid={Boolean(errors.chapterSummary)}
              value={chapterSummary.join("\n")}
              onChange={(event) =>
                setValue("chapterSummary", splitLines(event.target.value), {
                  shouldDirty: true,
                  shouldValidate: true,
                })
              }
            />
            <FieldInfo
              error={errors.chapterSummary?.message}
              hint="Opsional · maksimal 100 poin, masing-masing 2.000 karakter."
            />
          </div>
        </div>
      </section>

      <section className="panel p-6">
        <div className="flex items-center justify-between gap-4">
          <div>
            <h2 className="text-lg font-black text-slate-950">Subbab / Lesson</h2>
            <p className="mt-1 text-sm text-slate-500">
              Editor menyimpan konten dalam Markdown dan mendukung heading, tebal, miring,
              daftar, kutipan, serta tautan.
            </p>
          </div>
          <button
            type="button"
            className="btn-secondary px-4 py-2"
            onClick={() =>
              append({
                sourceId: null,
                title: "",
                slug: "",
                content: "",
                keyPoints: [],
                sourceSections: [],
                lessonOrder: fields.length + 1,
              })
            }
          >
            <Plus className="size-4" />
            Tambah subbab
          </button>
        </div>
        {errors.lessons?.root?.message && (
          <p className="form-error">{errors.lessons.root.message}</p>
        )}
        <div className="mt-6 space-y-6">
          {fields.map((field, index) => {
            const lessonErrors = errors.lessons?.[index];
            const lesson = lessonValues[index];
            return (
              <article key={field.id} className="rounded-md border border-emerald-200 bg-emerald-50/40 p-5">
                <div className="flex items-center justify-between">
                  <p className="font-extrabold text-emerald-900">Subbab {index + 1}</p>
                  <button
                    type="button"
                    onClick={() => void removeLesson(index)}
                    disabled={fields.length === 1}
                    className="inline-flex size-9 items-center justify-center rounded-md text-rose-600 hover:bg-rose-100 disabled:opacity-30"
                  >
                    <Trash2 className="size-4" />
                  </button>
                </div>
                <div className="mt-4 grid gap-4 md:grid-cols-2">
                  <div>
                    <label className="form-label">Judul</label>
                    <input className="form-input" aria-invalid={Boolean(lessonErrors?.title)} {...register(`lessons.${index}.title`)} />
                    <FieldInfo error={lessonErrors?.title?.message} hint="Wajib · minimal 2, maksimal 255 karakter." />
                  </div>
                  <div>
                    <label className="form-label">Slug opsional</label>
                    <input className="form-input" aria-invalid={Boolean(lessonErrors?.slug)} {...register(`lessons.${index}.slug`)} />
                    <FieldInfo error={lessonErrors?.slug?.message} hint="Opsional · maksimal 255 karakter; kosongkan untuk dibuat otomatis." />
                  </div>
                  <input type="hidden" value={index + 1} {...register(`lessons.${index}.lessonOrder`, { valueAsNumber: true })} />
                  <div className="md:col-span-2">
                    <label className="form-label">Isi materi</label>
                    <input type="hidden" {...register(`lessons.${index}.content`)} />
                    <MarkdownEditor
                      markdown={lesson?.content ?? ""}
                      onChange={(markdown, initialMarkdownNormalize) =>
                        setValue(`lessons.${index}.content`, markdown, {
                          shouldDirty: !initialMarkdownNormalize,
                          shouldValidate: true,
                        })
                      }
                    />
                    <FieldInfo error={lessonErrors?.content?.message} hint="Wajib · maksimal 200.000 karakter · disimpan sebagai Markdown." />
                  </div>
                  <div>
                    <label className="form-label">Poin penting (per baris)</label>
                    <textarea
                      className="form-input min-h-24"
                      aria-invalid={Boolean(lessonErrors?.keyPoints)}
                      value={(lesson?.keyPoints ?? []).join("\n")}
                      onChange={(event) =>
                        setValue(`lessons.${index}.keyPoints`, splitLines(event.target.value), {
                          shouldDirty: true,
                          shouldValidate: true,
                        })
                      }
                    />
                    <FieldInfo error={lessonErrors?.keyPoints?.message} hint="Opsional · maksimal 50 poin, masing-masing 1.000 karakter." />
                  </div>
                  <div>
                    <label className="form-label">Bagian sumber (per baris)</label>
                    <textarea
                      className="form-input min-h-24"
                      aria-invalid={Boolean(lessonErrors?.sourceSections)}
                      value={(lesson?.sourceSections ?? []).join("\n")}
                      onChange={(event) =>
                        setValue(`lessons.${index}.sourceSections`, splitLines(event.target.value), {
                          shouldDirty: true,
                          shouldValidate: true,
                        })
                      }
                    />
                    <FieldInfo error={lessonErrors?.sourceSections?.message} hint="Opsional · maksimal 50 baris, masing-masing 255 karakter." />
                  </div>
                </div>
              </article>
            );
          })}
        </div>
      </section>

      <section className="panel p-6">
        <h2 className="text-lg font-black text-slate-950">Spesies terkait</h2>
        <p className="form-help">Opsional · pilih maksimal 500 spesies.</p>
        <div className="mt-5 grid max-h-80 gap-2 overflow-y-auto sm:grid-cols-2 lg:grid-cols-3">
          {plants.map((plant) => (
            <label key={plant.id} className="flex items-start gap-3 rounded-md border border-slate-200 bg-white p-3 text-sm">
              <input
                type="checkbox"
                className="mt-1 accent-emerald-600"
                checked={speciesIds.includes(plant.id)}
                onChange={(event) => {
                  const nextIds = event.target.checked
                    ? [...speciesIds, plant.id]
                    : speciesIds.filter((id) => id !== plant.id);
                  setValue("speciesIds", nextIds, {
                    shouldDirty: true,
                    shouldValidate: true,
                  });
                }}
              />
              <span>
                <strong className="block text-slate-900">{plant.local_name}</strong>
                <span className="text-xs font-serif italic text-slate-500">{plant.scientific_name}</span>
              </span>
            </label>
          ))}
        </div>
        {errors.speciesIds?.message && <p className="form-error">{errors.speciesIds.message}</p>}
      </section>

      <button className="btn-primary" type="submit" disabled={mutation.isPending}>
        {mutation.isPending ? <LoaderCircle className="size-4 animate-spin" /> : <Save className="size-4" />}
        Simpan bab dan subbab
      </button>
    </form>
  );
}
