"use client";

import { Clock3, LoaderCircle, Send } from "lucide-react";
import { useRouter } from "next/navigation";
import { useEffect, useMemo, useRef, useState } from "react";
import { toast } from "sonner";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";

type Question = {
  id: number;
  question_text: string;
  question_type: "pilihan_ganda" | "esai" | "multiple_choice" | "true_false";
  score_weight: number;
  options: { id: number; option_text: string }[];
};

type QuizAttemptFormProps = {
  attemptId: number;
  startedAt: string;
  durationMinutes: number;
  questions: Question[];
};

export function QuizAttemptForm({ attemptId, startedAt, durationMinutes, questions }: QuizAttemptFormProps) {
  const router = useRouter();
  const [answers, setAnswers] = useState<Record<string, string>>({});
  const deadline = useMemo(() => new Date(startedAt).getTime() + durationMinutes * 60_000, [startedAt, durationMinutes]);
  const [remaining, setRemaining] = useState(() => Math.max(0, Math.floor((deadline - Date.now()) / 1000)));
  const submitted = useRef(false);
  const mutation = trpc.quizzes.submit.useMutation({
    onSuccess: ({ attempt_id }) => {
      toast.success("Kuis selesai dinilai.");
      router.push(`/mahasiswa/quizzes/result/${attempt_id}`);
      router.refresh();
    },
    onError: (error) => {
      submitted.current = false;
      toast.error(error.message);
    },
  });
  const { mutate, isPending } = mutation;

  useEffect(() => {
    const timer = window.setInterval(() => {
      setRemaining(Math.max(0, Math.floor((deadline - Date.now()) / 1000)));
    }, 1000);
    return () => window.clearInterval(timer);
  }, [deadline]);

  useEffect(() => {
    if (remaining === 0 && !submitted.current) {
      submitted.current = true;
      mutate({ attemptId, answers });
    }
  }, [remaining, attemptId, answers, mutate]);

  const submit = async (): Promise<void> => {
    if (isPending || submitted.current) return;
    const unanswered = questions.length - Object.keys(answers).length;
    const confirmed = await confirmAction({
      title: "Kumpulkan jawaban sekarang?",
      text: unanswered > 0
        ? `Masih ada ${unanswered} soal yang belum dijawab. Setelah dikirim, jawaban tidak dapat diubah.`
        : "Setelah dikirim, jawaban tidak dapat diubah dan nilai akan dihitung otomatis.",
      confirmText: "Ya, kirim jawaban",
    });
    if (!confirmed) return;
    submitted.current = true;
    mutate({ attemptId, answers });
  };
  const minutes = Math.floor(remaining / 60);
  const seconds = remaining % 60;

  return (
    <form onSubmit={(event) => { event.preventDefault(); void submit(); }}>
      <div className="sticky top-20 z-20 mb-6 flex items-center justify-between rounded-md border border-amber-200 bg-amber-50 px-5 py-4 shadow-lg">
        <span className="text-sm font-bold text-amber-900">{Object.keys(answers).length} / {questions.length} dijawab</span>
        <span className="flex items-center gap-2 font-mono text-lg font-black text-amber-900"><Clock3 className="size-5" />{String(minutes).padStart(2, "0")}:{String(seconds).padStart(2, "0")}</span>
      </div>
      <div className="space-y-5">
        {questions.map((question, index) => (
          <fieldset key={question.id} className="panel p-6">
            <legend className="w-full"><span className="eyebrow">Soal {index + 1} · bobot {question.score_weight}</span><span className="mt-3 block text-lg font-bold leading-7 text-slate-950">{question.question_text}</span></legend>
            {question.question_type === "esai" ? (
              <textarea className="form-input mt-5 min-h-32" value={answers[String(question.id)] ?? ""} onChange={(event) => setAnswers((current) => ({ ...current, [question.id]: event.target.value }))} placeholder="Tulis jawaban esai" />
            ) : (
              <div className="mt-5 space-y-3">
                {question.options.map((option) => (
                  <label key={option.id} className="flex cursor-pointer items-start gap-3 rounded-md border border-emerald-100 p-4 transition has-checked:border-emerald-500 has-checked:bg-emerald-50">
                    <input type="radio" className="mt-1 accent-emerald-600" name={`question-${question.id}`} value={option.id} checked={answers[String(question.id)] === String(option.id)} onChange={(event) => setAnswers((current) => ({ ...current, [question.id]: event.target.value }))} />
                    <span className="text-sm leading-6 text-slate-700">{option.option_text}</span>
                  </label>
                ))}
              </div>
            )}
          </fieldset>
        ))}
      </div>
      <button type="submit" disabled={isPending} className="btn-primary mt-7 w-full sm:w-auto">{isPending ? <LoaderCircle className="size-4 animate-spin" /> : <Send className="size-4" />}Kirim dan nilai jawaban</button>
    </form>
  );
}
