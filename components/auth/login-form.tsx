"use client";

import { zodResolver } from "@hookform/resolvers/zod";
import { useQueryClient } from "@tanstack/react-query";
import { Eye, EyeOff, LoaderCircle, LogIn } from "lucide-react";
import { useState } from "react";
import { useRouter } from "next/navigation";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { z } from "zod";
import { trpc } from "@/lib/trpc/client";

const schema = z.object({
  email: z.string().trim().pipe(z.email("Format email tidak valid.").max(255)),
  password: z.string().min(1, "Masukkan kata sandi.").max(128),
});
type Values = z.infer<typeof schema>;

export function LoginForm() {
  const router = useRouter();
  const [showPassword, setShowPassword] = useState(false);
  const queryClient = useQueryClient();
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<Values>({
    resolver: zodResolver(schema),
    defaultValues: { email: "", password: "" },
  });
  const mutation = trpc.auth.signIn.useMutation({
    onSuccess: ({ redirectTo }) => {
      toast.success("Selamat datang kembali!");
      queryClient.clear();
      router.replace(redirectTo);
      router.refresh();
    },
    onError: (error) => toast.error(error.message),
  });
  return (
    <form
      className="space-y-6"
      noValidate
      aria-busy={mutation.isPending}
      onSubmit={handleSubmit((values) => mutation.mutate(values))}
    >
      <div className="space-y-4">
        <div>
          <label className="form-label" htmlFor="email">
            Alamat Email Universitas / Akun
          </label>
          <input
            className="form-input"
            id="email"
            type="email"
            autoComplete="email"
            aria-invalid={Boolean(errors.email)}
            aria-describedby={errors.email ? "email-error" : undefined}
            disabled={mutation.isPending}
            placeholder="contoh: mahasiswa@botaniapps.site"
            {...register("email")}
          />
          {errors.email && (
            <p id="email-error" role="alert" className="mt-1 text-xs text-rose-600">{errors.email.message}</p>
          )}
        </div>
        <div>
          <label className="form-label" htmlFor="password">
            Kata Sandi (Password)
          </label>
          <div className="relative">
          <input
            className="form-input pr-12"
            id="password"
            type={showPassword ? "text" : "password"}
            autoComplete="current-password"
            aria-invalid={Boolean(errors.password)}
            aria-describedby={errors.password ? "password-error" : undefined}
            disabled={mutation.isPending}
            placeholder="••••••••"
            {...register("password")}
          />
          <button type="button" onClick={() => setShowPassword((value) => !value)} aria-label={showPassword ? "Sembunyikan kata sandi" : "Tampilkan kata sandi"} aria-pressed={showPassword} className="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-md text-slate-500 hover:text-emerald-700 focus-visible:outline-2 focus-visible:outline-emerald-600">
            {showPassword ? <EyeOff className="size-5" /> : <Eye className="size-5" />}
          </button>
          </div>
          {errors.password && (
            <p id="password-error" role="alert" className="mt-1 text-xs text-rose-600">
              {errors.password.message}
            </p>
          )}
        </div>
      </div>
      {mutation.error && <p role="alert" className="rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">{mutation.error.message}</p>}
      <button
        className="btn-primary w-full text-white py-3.5"
        type="submit"
        disabled={mutation.isPending}
      >
        {mutation.isPending ? (
          <LoaderCircle className="size-4 animate-spin" />
        ) : (
          <LogIn className="size-4" />
        )}
        {mutation.isPending ? "Sedang masuk..." : "Masuk Sekarang"}
      </button>
    </form>
  );
}
