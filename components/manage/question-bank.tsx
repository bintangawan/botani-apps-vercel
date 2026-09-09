"use client";

import { LoaderCircle, Pencil, Plus, Trash2, X } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { useForm, useWatch } from "react-hook-form";
import { toast } from "sonner";
import type { inferRouterInputs } from "@trpc/server";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";
import type { AppRouter } from "@/server/api/root";

type Values = inferRouterInputs<AppRouter>["quizzes"]["saveQuestion"];
type Question = { id: number; question_text: string; question_type: "pilihan_ganda" | "esai" | "multiple_choice" | "true_false"; score_weight: number; options: { id: number; option_text: string }[]; correctOptionId: number | null };
type Props = { quizId: number; questions: Question[] };
const blank = (quizId: number): Values => ({ quizId, questionText: "", questionType: "pilihan_ganda", scoreWeight: 1, options: ["", "", "", ""], correctOptionIndex: 0 });

export function QuestionBank({ quizId, questions }: Props) {
  const router = useRouter();
  const [editing, setEditing] = useState<number | null>(null);
  const { register, handleSubmit, reset, control, setValue } = useForm<Values>({ defaultValues: blank(quizId) });
  const options = useWatch({ control, name: "options" }) ?? [];
  const questionType = useWatch({ control, name: "questionType" });
  const save = trpc.quizzes.saveQuestion.useMutation();
  const removeMutation = trpc.quizzes.deleteQuestion.useMutation();

  const submit = async (values: Values): Promise<void> => {
    const confirmed = await confirmAction({
      title: editing ? "Simpan perubahan soal?" : "Tambahkan soal ini?",
      text: editing
        ? "Pertanyaan, pilihan jawaban, dan kunci jawaban akan diperbarui."
        : "Soal baru beserta kunci jawabannya akan ditambahkan ke kuis.",
      confirmText: editing ? "Ya, simpan" : "Ya, tambahkan",
    });
    if (!confirmed) return;
    try {
      await save.mutateAsync({ ...values, id: editing ?? undefined, options: values.questionType === "esai" ? [] : values.options });
      toast.success("Soal berhasil disimpan.");
      setEditing(null);
      reset(blank(quizId));
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Soal gagal disimpan.");
    }
  };

  const edit = (question: Question): void => {
    const correctIndex = question.options.findIndex((option) => option.id === question.correctOptionId);
    setEditing(question.id);
    reset({ id: question.id, quizId, questionText: question.question_text, questionType: question.question_type === "esai" ? "esai" : "pilihan_ganda", scoreWeight: question.score_weight, options: question.options.map((option) => option.option_text), correctOptionIndex: Math.max(0, correctIndex) });
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const removeQuestion = async (id: number): Promise<void> => {
    const confirmed = await confirmAction({
      title: "Hapus soal ini?",
      text: "Soal, pilihan jawaban, dan kunci jawabannya akan dihapus permanen.",
      confirmText: "Ya, hapus soal",
      variant: "danger",
    });
    if (!confirmed) return;
    try {
      await removeMutation.mutateAsync({ quizId, questionId: id });
      toast.success("Soal dihapus.");
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Soal gagal dihapus.");
    }
  };

  return (
    <div className="grid gap-7 lg:grid-cols-[390px_1fr]">
      <form onSubmit={handleSubmit(submit)} className="panel h-fit p-6 lg:sticky lg:top-24">
        <div className="flex items-center justify-between"><h2 className="text-lg font-black text-slate-950">{editing ? "Edit soal" : "Tambah soal"}</h2>{editing && <button type="button" className="text-slate-500" onClick={() => { setEditing(null); reset(blank(quizId)); }}><X className="size-5" /></button>}</div>
        <div className="mt-5 space-y-4">
          <div><label className="form-label">Pertanyaan</label><textarea className="form-input min-h-28" required {...register("questionText")} /></div>
          <div className="grid grid-cols-2 gap-3"><div><label className="form-label">Jenis</label><select className="form-input" {...register("questionType")}><option value="pilihan_ganda">Pilihan ganda</option><option value="esai">Esai</option></select></div><div><label className="form-label">Bobot</label><input className="form-input" type="number" min={1} max={100} {...register("scoreWeight", { valueAsNumber: true })} /></div></div>
          {questionType === "pilihan_ganda" && (
            <div>
              <div className="mb-2 flex items-center justify-between"><label className="form-label mb-0">Opsi & kunci</label><button type="button" onClick={() => setValue("options", [...options, ""])} className="text-xs font-bold text-emerald-700"><Plus className="inline size-3" /> Opsi</button></div>
              <div className="space-y-2">
                {options.map((option, index) => (
                  <div key={`${index}-${options.length}`} className="flex items-center gap-2">
                    <input type="radio" value={index} className="accent-emerald-600" {...register("correctOptionIndex", { valueAsNumber: true })} />
                    <input className="form-input" placeholder={`Opsi ${index + 1}`} value={option} onChange={(event) => setValue("options", options.map((item, itemIndex) => itemIndex === index ? event.target.value : item))} />
                    <button type="button" onClick={() => setValue("options", options.filter((_, itemIndex) => itemIndex !== index))} disabled={options.length <= 2} className="text-rose-600 disabled:opacity-30"><Trash2 className="size-4" /></button>
                  </div>
                ))}
              </div>
            </div>
          )}
          <button className="btn-primary w-full" disabled={save.isPending}>{save.isPending ? <LoaderCircle className="size-4 animate-spin" /> : <Plus className="size-4" />}{editing ? "Simpan perubahan" : "Tambahkan soal"}</button>
        </div>
      </form>
      <div className="space-y-4">
        {questions.map((question, index) => (
          <article key={question.id} className="panel p-6">
            <div className="flex items-start justify-between gap-4"><div><p className="eyebrow">Soal {index + 1} · {question.question_type} · bobot {question.score_weight}</p><h3 className="mt-2 font-bold leading-7 text-slate-950">{question.question_text}</h3></div><div className="flex gap-2"><button type="button" onClick={() => edit(question)} className="inline-flex size-9 items-center justify-center rounded-md bg-emerald-50 text-emerald-700"><Pencil className="size-4" /></button><button type="button" onClick={() => removeQuestion(question.id)} className="inline-flex size-9 items-center justify-center rounded-md bg-rose-50 text-rose-700"><Trash2 className="size-4" /></button></div></div>
            {question.options.length > 0 && <ol className="mt-4 grid gap-2 sm:grid-cols-2">{question.options.map((option) => <li key={option.id} className={`rounded-md border p-3 text-sm ${option.id === question.correctOptionId ? "border-emerald-400 bg-emerald-50 font-bold text-emerald-900" : "border-slate-200 bg-slate-50 text-slate-600"}`}>{option.option_text}</li>)}</ol>}
          </article>
        ))}
        {!questions.length && <div className="panel p-10 text-center text-sm text-slate-500">Bank soal masih kosong.</div>}
      </div>
    </div>
  );
}
