"use client";

import { LoaderCircle, Trash2 } from "lucide-react";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";

type DeleteButtonProps = { type: "plant" | "module" | "quiz"; id: number; label: string };

export function DeleteButton({ type, id, label }: DeleteButtonProps) {
  const router = useRouter();
  const plantMutation = trpc.plants.delete.useMutation();
  const moduleMutation = trpc.modules.delete.useMutation();
  const quizMutation = trpc.quizzes.delete.useMutation();
  const pending = plantMutation.isPending || moduleMutation.isPending || quizMutation.isPending;
  const remove = async (): Promise<void> => {
    const confirmed = await confirmAction({
      title: `Hapus ${label}?`,
      text: "Data yang sudah dihapus tidak dapat dipulihkan kembali.",
      confirmText: "Ya, hapus",
      variant: "danger",
    });
    if (!confirmed) return;
    try {
      if (type === "plant") await plantMutation.mutateAsync({ id });
      if (type === "module") await moduleMutation.mutateAsync({ id });
      if (type === "quiz") await quizMutation.mutateAsync({ id });
      toast.success(`${label} berhasil dihapus.`); router.refresh();
    } catch (error) { toast.error(error instanceof Error ? error.message : "Data gagal dihapus."); }
  };
  return <button type="button" onClick={remove} disabled={pending} className="inline-flex size-9 items-center justify-center rounded-md border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100">{pending ? <LoaderCircle className="size-4 animate-spin" /> : <Trash2 className="size-4" />}<span className="sr-only">Hapus {label}</span></button>;
}
