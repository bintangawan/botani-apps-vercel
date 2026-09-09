"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { LoaderCircle, Save, Upload } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { useForm, useWatch } from "react-hook-form";
import { toast } from "sonner";
import type { z } from "zod";
import { confirmAction } from "@/lib/confirm-action";
import { getPublicEnv } from "@/lib/env";
import { createSupabaseBrowserClient } from "@/lib/supabase/browser";
import { trpc } from "@/lib/trpc/client";
import { plantInputSchema } from "@/lib/validation/manage-forms";

type Values = z.input<typeof plantInputSchema>;
type ParsedValues = z.output<typeof plantInputSchema>;
type PlantFormProps = { initial: Values; mode: "create" | "edit" };

const morphologyFields = [
  { key: "root", label: "Akar" },
  { key: "stem", label: "Batang" },
  { key: "leaf", label: "Daun" },
  { key: "flower", label: "Bunga" },
  { key: "fruit", label: "Buah" },
  { key: "seed", label: "Biji" },
  { key: "specialCharacteristics", label: "Ciri khusus" },
] as const;

const taxonomyFields = [
  { key: "kingdom", label: "Kingdom" },
  { key: "divisi", label: "Divisi" },
  { key: "kelas", label: "Kelas" },
  { key: "ordo", label: "Ordo" },
  { key: "famili", label: "Famili" },
  { key: "genus", label: "Genus" },
  { key: "spesies", label: "Spesies" },
] as const;

function FieldInfo({ error, hint }: { error?: string; hint: string }) {
  return <p className={error ? "form-error" : "form-help"}>{error ?? hint}</p>;
}

export function PlantForm({ initial, mode }: PlantFormProps) {
  const router = useRouter();
  const [file, setFile] = useState<File | null>(null);
  const {
    register,
    handleSubmit,
    control,
    setValue,
    formState: { errors },
  } = useForm<Values, undefined, ParsedValues>({
    defaultValues: initial,
    resolver: zodResolver(plantInputSchema),
    mode: "onBlur",
  });
  const uploadUrl = trpc.plants.createUploadUrl.useMutation();
  const save = trpc.plants.save.useMutation();
  const groupType = useWatch({ control, name: "groupType" });

  const onSubmit = async (values: ParsedValues): Promise<void> => {
    const confirmed = await confirmAction({
      title: mode === "create" ? "Tambahkan spesimen ini?" : "Simpan perubahan spesimen?",
      text:
        mode === "create"
          ? "Data identitas, morfologi, dan taksonomi akan ditambahkan ke katalog."
          : "Data identitas, morfologi, taksonomi, dan gambar terbaru akan disimpan.",
      confirmText: mode === "create" ? "Ya, tambahkan" : "Ya, simpan",
    });
    if (!confirmed) return;

    try {
      let imagePath = values.imagePath;
      if (file) {
        if (file.size > 4 * 1024 * 1024) {
          throw new Error("Ukuran gambar maksimal 4 MB.");
        }
        if (!["image/jpeg", "image/png", "image/webp"].includes(file.type)) {
          throw new Error("Format gambar harus JPG, PNG, atau WebP.");
        }
        const signed = await uploadUrl.mutateAsync({
          fileName: file.name,
          contentType: file.type as "image/jpeg" | "image/png" | "image/webp",
        });
        const supabase = createSupabaseBrowserClient();
        const { error } = await supabase.storage
          .from(getPublicEnv().NEXT_PUBLIC_STORAGE_BUCKET)
          .uploadToSignedUrl(signed.path, signed.token, file, {
            contentType: signed.contentType,
          });
        if (error) throw error;
        imagePath = signed.path;
        setValue("imagePath", signed.path);
      }

      await save.mutateAsync({
        ...values,
        imagePath,
        cotyledonType:
          values.groupType === "Gymnospermae"
            ? "Tidak Berlaku"
            : values.cotyledonType,
      });
      toast.success(
        mode === "create"
          ? "Spesimen berhasil ditambahkan."
          : "Spesimen berhasil diperbarui.",
      );
      router.push("/manage/plants");
      router.refresh();
    } catch (error) {
      toast.error(
        error instanceof Error ? error.message : "Spesimen gagal disimpan.",
      );
    }
  };

  const onInvalid = (): void => {
    toast.error("Periksa kembali field yang ditandai sebelum menyimpan.");
  };

  return (
    <form
      noValidate
      onSubmit={handleSubmit(onSubmit, onInvalid)}
      className="space-y-7"
    >
      <section className="panel p-6">
        <h2 className="text-lg font-black text-slate-950">Identitas spesimen</h2>
        <div className="mt-5 grid gap-5 md:grid-cols-2">
          <div>
            <label className="form-label">Kode</label>
            <input className="form-input" aria-invalid={Boolean(errors.code)} {...register("code")} />
            <FieldInfo error={errors.code?.message} hint="Wajib · 2–50 karakter · huruf, angka, _ atau -." />
          </div>
          <div>
            <label className="form-label">Nama lokal</label>
            <input className="form-input" aria-invalid={Boolean(errors.localName)} {...register("localName")} />
            <FieldInfo error={errors.localName?.message} hint="Wajib · minimal 2, maksimal 255 karakter." />
          </div>
          <div>
            <label className="form-label">Nama ilmiah</label>
            <input className="form-input font-serif italic" aria-invalid={Boolean(errors.scientificName)} {...register("scientificName")} />
            <FieldInfo error={errors.scientificName?.message} hint="Wajib · minimal 2, maksimal 255 karakter." />
          </div>
          <div>
            <label className="form-label">Author</label>
            <input className="form-input" aria-invalid={Boolean(errors.authorName)} {...register("authorName")} />
            <FieldInfo error={errors.authorName?.message} hint="Opsional · maksimal 255 karakter." />
          </div>
          <div>
            <label className="form-label">Kelompok</label>
            <select className="form-input" {...register("groupType")}>
              <option>Angiospermae</option>
              <option>Gymnospermae</option>
            </select>
            <FieldInfo hint="Pilih kelompok tumbuhan." />
          </div>
          <div>
            <label className="form-label">Kotiledon</label>
            <select className="form-input" disabled={groupType === "Gymnospermae"} {...register("cotyledonType")}>
              <option>Monokotil</option>
              <option>Dikotil</option>
              <option>Tidak Berlaku</option>
            </select>
            <FieldInfo hint="Gymnospermae otomatis menggunakan Tidak Berlaku." />
          </div>
          <div>
            <label className="form-label">Status</label>
            <select className="form-input" {...register("status")}>
              <option value="published">Published</option>
              <option value="draft">Draft</option>
            </select>
            <FieldInfo hint="Draft tidak ditampilkan pada katalog publik." />
          </div>
          <div>
            <label className="form-label">Gambar</label>
            <label className="flex cursor-pointer items-center gap-3 rounded-md border border-dashed border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
              <Upload className="size-4" />
              {file?.name ?? (initial.imagePath ? "Ganti gambar" : "Pilih gambar")}
              <input
                className="sr-only"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                onChange={(event) => setFile(event.target.files?.[0] ?? null)}
              />
            </label>
            <FieldInfo hint="Opsional · JPG, PNG, atau WebP · maksimal 4 MB." />
          </div>
        </div>
        <div className="mt-5 grid gap-5">
          <div>
            <label className="form-label">Deskripsi</label>
            <textarea className="form-input min-h-32" aria-invalid={Boolean(errors.description)} {...register("description")} />
            <FieldInfo error={errors.description?.message} hint="Opsional · maksimal 20.000 karakter." />
          </div>
          <div className="grid gap-5 md:grid-cols-2">
            <div>
              <label className="form-label">Habitat</label>
              <textarea className="form-input min-h-28" aria-invalid={Boolean(errors.habitat)} {...register("habitat")} />
              <FieldInfo error={errors.habitat?.message} hint="Opsional · maksimal 10.000 karakter." />
            </div>
            <div>
              <label className="form-label">Manfaat</label>
              <textarea className="form-input min-h-28" aria-invalid={Boolean(errors.benefits)} {...register("benefits")} />
              <FieldInfo error={errors.benefits?.message} hint="Opsional · maksimal 10.000 karakter." />
            </div>
          </div>
        </div>
      </section>

      <section className="panel p-6">
        <h2 className="text-lg font-black text-slate-950">Morfologi</h2>
        <div className="mt-5 grid gap-5 md:grid-cols-2">
          {morphologyFields.map((field) => {
            const error = errors.morphology?.[field.key]?.message;
            return (
              <div key={field.key} className={field.key === "specialCharacteristics" ? "md:col-span-2" : ""}>
                <label className="form-label">{field.label}</label>
                <textarea className="form-input min-h-24" aria-invalid={Boolean(error)} {...register(`morphology.${field.key}`)} />
                <FieldInfo error={error} hint="Opsional · maksimal 5.000 karakter." />
              </div>
            );
          })}
        </div>
      </section>

      <section className="panel p-6">
        <h2 className="text-lg font-black text-slate-950">Taksonomi</h2>
        <div className="mt-5 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {taxonomyFields.map((field) => {
            const error = errors.taxonomy?.[field.key]?.message;
            return (
              <div key={field.key}>
                <label className="form-label">{field.label}</label>
                <input className="form-input" aria-invalid={Boolean(error)} {...register(`taxonomy.${field.key}`)} />
                <FieldInfo error={error} hint="Opsional · maksimal 255 karakter." />
              </div>
            );
          })}
        </div>
      </section>

      <button type="submit" className="btn-primary" disabled={save.isPending || uploadUrl.isPending}>
        {save.isPending || uploadUrl.isPending ? <LoaderCircle className="size-4 animate-spin" /> : <Save className="size-4" />}
        Simpan spesimen
      </button>
    </form>
  );
}
