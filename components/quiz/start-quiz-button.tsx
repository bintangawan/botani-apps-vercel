"use client";

import { LoaderCircle, Play } from "lucide-react";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";

export function StartQuizButton({ quizId, disabled }: { quizId: number; disabled: boolean }) {
  const router = useRouter();
  const mutation = trpc.quizzes.start.useMutation({ onSuccess: ({ attemptId }) => router.push(`/mahasiswa/quizzes/attempt/${attemptId}`), onError: (error) => toast.error(error.message) });
  const startQuiz = async (): Promise<void> => {
    const confirmed = await confirmAction({
      title: "Mulai kuis sekarang?",
      text: "Pastikan Anda sudah siap. Waktu pengerjaan akan berjalan setelah kuis dibuka.",
      confirmText: "Ya, mulai kuis",
    });
    if (confirmed) {
      mutation.mutate({ quizId });
    }
  };
  return <button type="button" className="btn-primary w-full" disabled={disabled || mutation.isPending} onClick={startQuiz}>{mutation.isPending ? <LoaderCircle className="size-4 animate-spin" /> : <Play className="size-4" />}{disabled ? "Belum ada soal" : "Mulai / lanjutkan"}</button>;
}
