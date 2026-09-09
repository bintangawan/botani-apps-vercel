"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { LoaderCircle, Save } from "lucide-react";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import type { z } from "zod";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";
import { quizInputSchema } from "@/lib/validation/manage-forms";

type Values = z.input<typeof quizInputSchema>;
type ParsedValues = z.output<typeof quizInputSchema>;
type QuizFormProps = {
  initial: Values;
  modules: { id: number; title: string; module_order: number }[];
  mode: "create" | "edit";
};

function FieldInfo({ error, hint }: { error?: string; hint: string }) {
  return <p className={error ? "form-error" : "form-help"}>{error ?? hint}</p>;
}

export function QuizForm({ initial, modules, mode }: QuizFormProps) {
  const router = useRouter();
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<Values, undefined, ParsedValues>({
    defaultValues: initial,
    resolver: zodResolver(quizInputSchema),
    mode: "onBlur",
  });
  const mutation = trpc.quizzes.save.useMutation();

  const submit = async (values: ParsedValues): Promise<void> => {
    const confirmed = await confirmAction({
      title: mode === "create" ? "Buat kuis baru?" : "Simpan perubahan kuis?",
      text:
        mode === "create"
          ? "Konfigurasi kuis akan disimpan sebelum Anda menyusun bank soal."
          : "Modul, durasi, nilai lulus, dan status kuis akan diperbarui.",
      confirmText: mode === "create" ? "Ya, buat kuis" : "Ya, simpan",
    });
    if (!confirmed) return;

    try {
      const result = await mutation.mutateAsync(values);
      toast.success("Kuis berhasil disimpan.");
      router.push(
        mode === "create"
          ? `/manage/quizzes/${result.id}/questions`
          : "/manage/quizzes",
      );
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Kuis gagal disimpan.");
    }
  };

  const onInvalid = (): void => {
    toast.error("Periksa kembali konfigurasi kuis yang ditandai.");
  };

  return (
    <form className="panel space-y-5 p-6" noValidate onSubmit={handleSubmit(submit, onInvalid)}>
      <div>
        <label className="form-label">Modul</label>
        <select
          className="form-input"
          aria-invalid={Boolean(errors.moduleId)}
          {...register("moduleId", { valueAsNumber: true })}
        >
          {modules.map((module) => (
            <option key={module.id} value={module.id}>
              Bab {module.module_order} · {module.title}
            </option>
          ))}
        </select>
        <FieldInfo error={errors.moduleId?.message} hint="Wajib · pilih satu modul untuk kuis ini." />
      </div>
      <div>
        <label className="form-label">Judul kuis</label>
        <input className="form-input" aria-invalid={Boolean(errors.title)} {...register("title")} />
        <FieldInfo error={errors.title?.message} hint="Wajib · minimal 2, maksimal 255 karakter." />
      </div>
      <div className="grid gap-5 sm:grid-cols-2">
        <div>
          <label className="form-label">Jenis</label>
          <select className="form-input" {...register("quizType")}>
            <option value="pilihan_ganda">Pilihan ganda</option>
            <option value="esai">Esai</option>
            <option value="campuran">Campuran</option>
            <option value="pretest">Pretest</option>
            <option value="practice">Practice</option>
            <option value="posttest">Posttest</option>
          </select>
          <FieldInfo hint="Tentukan tipe evaluasi yang digunakan." />
        </div>
        <div>
          <label className="form-label">Status</label>
          <select className="form-input" {...register("status")}>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
          </select>
          <FieldInfo hint="Draft belum dapat dikerjakan mahasiswa." />
        </div>
        <div>
          <label className="form-label">Nilai lulus</label>
          <input
            className="form-input"
            type="number"
            min={0}
            max={100}
            aria-invalid={Boolean(errors.passingScore)}
            {...register("passingScore", { valueAsNumber: true })}
          />
          <FieldInfo error={errors.passingScore?.message} hint="Wajib · angka bulat 0–100." />
        </div>
        <div>
          <label className="form-label">Durasi (menit)</label>
          <input
            className="form-input"
            type="number"
            min={1}
            max={360}
            aria-invalid={Boolean(errors.duration)}
            {...register("duration", { valueAsNumber: true })}
          />
          <FieldInfo error={errors.duration?.message} hint="Wajib · angka bulat 1–360 menit." />
        </div>
      </div>
      <button className="btn-primary" disabled={mutation.isPending}>
        {mutation.isPending ? <LoaderCircle className="size-4 animate-spin" /> : <Save className="size-4" />}
        Simpan kuis
      </button>
    </form>
  );
}
