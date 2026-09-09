"use client";

import { LoaderCircle, Save, Trash2, UserPlus } from "lucide-react";
import { useRouter } from "next/navigation";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import type { inferRouterInputs } from "@trpc/server";
import { confirmAction } from "@/lib/confirm-action";
import { trpc } from "@/lib/trpc/client";
import type { AppRouter } from "@/server/api/root";
import type { Tables } from "@/types/database";

type CreateValues = inferRouterInputs<AppRouter>["users"]["create"];
type Editable = {
  role: "mahasiswa" | "dosen" | "admin";
  status: "active" | "inactive";
  institution: string;
};
type Props = { users: Tables<"profiles">[]; currentId: string };

export function UsersPanel({ users, currentId }: Props) {
  const router = useRouter();
  const { register, handleSubmit, reset } = useForm<CreateValues>({
    defaultValues: {
      name: "",
      email: "",
      password: "",
      role: "dosen",
      institution: "",
      status: "active",
    },
  });
  const [edits, setEdits] = useState<Record<string, Editable>>(() =>
    Object.fromEntries(
      users.map((user) => [
        user.id,
        {
          role: user.role,
          status: user.status,
          institution: user.institution ?? "",
        },
      ]),
    ),
  );
  const create = trpc.users.create.useMutation();
  const update = trpc.users.update.useMutation();
  const remove = trpc.users.delete.useMutation();

  const submit = async (values: CreateValues): Promise<void> => {
    const confirmed = await confirmAction({
      title: "Buat akun pengguna baru?",
      text: `Akun ${values.name} akan dibuat dengan role ${values.role}.`,
      confirmText: "Ya, buat akun",
    });
    if (!confirmed) return;

    try {
      await create.mutateAsync(values);
      toast.success("Akun berhasil dibuat.");
      reset();
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Akun gagal dibuat.");
    }
  };

  const saveUser = async (id: string, name: string): Promise<void> => {
    const value = edits[id];
    if (!value) return;
    const confirmed = await confirmAction({
      title: `Simpan perubahan ${name}?`,
      text: "Role, status akun, dan institusi pengguna akan diperbarui.",
      confirmText: "Ya, simpan",
    });
    if (!confirmed) return;

    try {
      await update.mutateAsync({
        id,
        role: value.role,
        status: value.status,
        institution: value.institution || null,
      });
      toast.success("Profil diperbarui.");
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Profil gagal diperbarui.");
    }
  };

  const deleteUser = async (id: string, name: string): Promise<void> => {
    const confirmed = await confirmAction({
      title: `Hapus akun ${name}?`,
      text: "Akun autentikasi dan profil pengguna akan dihapus permanen.",
      confirmText: "Ya, hapus akun",
      variant: "danger",
    });
    if (!confirmed) return;

    try {
      await remove.mutateAsync({ id });
      toast.success("Akun dihapus.");
      router.refresh();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : "Akun gagal dihapus.");
    }
  };

  return (
    <div className="grid gap-7 xl:grid-cols-[360px_1fr]">
      <form onSubmit={handleSubmit(submit)} className="panel h-fit p-6">
        <h2 className="text-lg font-black text-slate-950">Tambah pengguna</h2>
        <p className="mt-2 text-xs leading-5 text-slate-500">
          Membutuhkan Supabase secret key di environment server Vercel.
        </p>
        <div className="mt-5 space-y-4">
          <div>
            <label className="form-label">Nama</label>
            <input className="form-input" required {...register("name")} />
          </div>
          <div>
            <label className="form-label">Email</label>
            <input className="form-input" type="email" required {...register("email")} />
          </div>
          <div>
            <label className="form-label">Kata sandi awal</label>
            <input
              className="form-input"
              type="password"
              minLength={8}
              required
              {...register("password")}
            />
          </div>
          <div>
            <label className="form-label">Institusi</label>
            <input className="form-input" {...register("institution")} />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="form-label">Role</label>
              <select className="form-input" {...register("role")}>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div>
              <label className="form-label">Status</label>
              <select className="form-input" {...register("status")}>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
              </select>
            </div>
          </div>
          <button className="btn-primary w-full" disabled={create.isPending}>
            {create.isPending ? (
              <LoaderCircle className="size-4 animate-spin" />
            ) : (
              <UserPlus className="size-4" />
            )}
            Buat akun
          </button>
        </div>
      </form>

      <div className="space-y-4">
        {users.map((user) => {
          const value = edits[user.id] ?? {
            role: user.role,
            status: user.status,
            institution: user.institution ?? "",
          };

          return (
            <article key={user.id} className="panel p-5">
              <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                <div className="min-w-0">
                  <p className="truncate font-extrabold text-slate-950">
                    {user.name}
                    {user.id === currentId && (
                      <span className="ml-2 rounded-md bg-emerald-100 px-2 py-0.5 text-[9px] uppercase text-emerald-800">
                        Anda
                      </span>
                    )}
                  </p>
                  <p className="mt-1 truncate text-xs text-slate-500">{user.email}</p>
                </div>
                <div className="grid flex-1 gap-2 sm:grid-cols-[150px_150px_1fr_auto] lg:max-w-2xl">
                  <select
                    className="form-input py-2"
                    value={value.role}
                    onChange={(event) =>
                      setEdits((current) => ({
                        ...current,
                        [user.id]: {
                          ...value,
                          role: event.target.value as Editable["role"],
                        },
                      }))
                    }
                  >
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="admin">Admin</option>
                  </select>
                  <select
                    className="form-input py-2"
                    value={value.status}
                    onChange={(event) =>
                      setEdits((current) => ({
                        ...current,
                        [user.id]: {
                          ...value,
                          status: event.target.value as Editable["status"],
                        },
                      }))
                    }
                  >
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                  </select>
                  <input
                    className="form-input py-2"
                    value={value.institution}
                    placeholder="Institusi"
                    onChange={(event) =>
                      setEdits((current) => ({
                        ...current,
                        [user.id]: {
                          ...value,
                          institution: event.target.value,
                        },
                      }))
                    }
                  />
                  <div className="flex gap-2">
                    <button
                      type="button"
                      onClick={() => { void saveUser(user.id, user.name); }}
                      className="inline-flex size-10 items-center justify-center rounded-md bg-emerald-100 text-emerald-800"
                    >
                      <Save className="size-4" />
                    </button>
                    <button
                      type="button"
                      disabled={user.id === currentId}
                      onClick={() => { void deleteUser(user.id, user.name); }}
                      className="inline-flex size-10 items-center justify-center rounded-md bg-rose-100 text-rose-700 disabled:opacity-30"
                    >
                      <Trash2 className="size-4" />
                    </button>
                  </div>
                </div>
              </div>
            </article>
          );
        })}
      </div>
    </div>
  );
}
