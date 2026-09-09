"use client";

import { LogOut } from "lucide-react";
import { useQueryClient } from "@tanstack/react-query";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";
import { cn } from "@/lib/utils";

type SignOutButtonProps = { compact?: boolean };

export function SignOutButton({ compact = false }: SignOutButtonProps) {
  const router = useRouter();
  const queryClient = useQueryClient();
  const mutation = trpc.auth.signOut.useMutation({
    onSuccess: ({ redirectTo }) => {
      queryClient.clear();
      router.replace(redirectTo);
      router.refresh();
    },
    onError: (error) => toast.error(error.message),
  });
  const signOut = async (): Promise<void> => {
    const confirmed = await confirmAction({
      title: "Keluar dari akun?",
      text: "Sesi aktif Anda akan diakhiri dan Anda perlu masuk kembali untuk mengakses dashboard.",
      confirmText: "Ya, keluar",
      variant: "danger",
    });
    if (confirmed) {
      mutation.mutate();
    }
  };

  return (
    <button
      type="button"
      onClick={signOut}
      disabled={mutation.isPending}
      className={cn(
        "inline-flex w-full items-center justify-center gap-2 rounded-md border border-rose-300 bg-rose-50 px-4 py-2.5 text-sm font-bold text-rose-700 transition hover:bg-rose-100 disabled:opacity-60",
        compact && "size-10 p-0",
      )}
      title="Keluar dari akun"
    >
      <LogOut className="size-4" aria-hidden="true" />
      {!compact && <span>Keluar</span>}
    </button>
  );
}
