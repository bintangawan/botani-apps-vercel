"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { LoaderCircle, UserPlus } from "lucide-react";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { z } from "zod";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";

const schema = z
  .object({
    name: z.string().trim().min(2, "Nama minimal 2 karakter."),
    email: z.email("Format email tidak valid."),
    institution: z.string().trim().max(255),
    password: z.string().min(8, "Kata sandi minimal 8 karakter."),
    passwordConfirmation: z.string(),
  })
  .refine((values) => values.password === values.passwordConfirmation, {
    path: ["passwordConfirmation"],
    message: "Konfirmasi kata sandi tidak cocok.",
  });
type Values = z.infer<typeof schema>;

export function RegisterForm() {
  const router = useRouter();
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<Values>({
    resolver: zodResolver(schema),
    defaultValues: {
      name: "",
      email: "",
      institution: "",
      password: "",
      passwordConfirmation: "",
    },
  });
  const mutation = trpc.auth.signUp.useMutation({
    onSuccess: ({ redirectTo, requiresEmailConfirmation }) => {
      toast.success(
        requiresEmailConfirmation
          ? "Cek email untuk mengonfirmasi akun."
          : "Akun berhasil dibuat.",
      );
      router.push(redirectTo);
      router.refresh();
    },
    onError: (error) => toast.error(error.message),
  });
  const onSubmit = async (values: Values): Promise<void> => {
    const confirmed = await confirmAction({
      title: "Buat akun sekarang?",
      text: `Pastikan nama dan alamat email ${values.email} sudah benar sebelum melanjutkan.`,
      confirmText: "Ya, buat akun",
    });
    if (!confirmed) return;

    mutation.mutate({
      name: values.name,
      email: values.email,
      institution: values.institution || undefined,
      password: values.password,
    });
  };
  return (
    <form className="space-y-5" onSubmit={handleSubmit(onSubmit)}>
      <div>
        <label className="form-label" htmlFor="name">
          Nama Lengkap Mahasiswa
        </label>
        <input
          className="form-input"
          id="name"
          autoComplete="name"
          placeholder="Contoh: Siti Aminah, S.Si."
          {...register("name")}
        />
        {errors.name && (
          <p className="mt-1 text-xs text-rose-600">{errors.name.message}</p>
        )}
      </div>
      <div>
        <label className="form-label" htmlFor="register-email">
          Alamat Email Aktif
        </label>
        <input
          className="form-input"
          id="register-email"
          type="email"
          autoComplete="email"
          placeholder="contoh: siti.aminah@botani.ac.id"
          {...register("email")}
        />
        {errors.email && (
          <p className="mt-1 text-xs text-rose-600">{errors.email.message}</p>
        )}
      </div>
      <div>
        <label className="form-label" htmlFor="institution">
          Institusi / Universitas / Fakultas
        </label>
        <input
          className="form-input"
          id="institution"
          placeholder="contoh: Universitas Negeri Medan"
          {...register("institution")}
        />
      </div>
      <div>
        <label className="form-label" htmlFor="register-password">
          Kata Sandi (Min. 8 Karakter)
        </label>
        <input
          className="form-input"
          id="register-password"
          type="password"
          autoComplete="new-password"
          placeholder="••••••••"
          {...register("password")}
        />
        {errors.password && (
          <p className="mt-1 text-xs text-rose-600">
            {errors.password.message}
          </p>
        )}
      </div>
      <div>
        <label className="form-label" htmlFor="password-confirmation">
          Konfirmasi Kata Sandi
        </label>
        <input
          className="form-input"
          id="password-confirmation"
          type="password"
          autoComplete="new-password"
          placeholder="••••••••"
          {...register("passwordConfirmation")}
        />
        {errors.passwordConfirmation && (
          <p className="mt-1 text-xs text-rose-600">
            {errors.passwordConfirmation.message}
          </p>
        )}
      </div>
      <button
        className="btn-primary w-full text-white py-3.5"
        type="submit"
        disabled={mutation.isPending}
      >
        {mutation.isPending ? (
          <LoaderCircle className="size-4 animate-spin" />
        ) : (
          <UserPlus className="size-4" />
        )}
        Daftar Akun
      </button>
    </form>
  );
}
