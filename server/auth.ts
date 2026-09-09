import "server-only";
import { redirect } from "next/navigation";
import { getAuthState } from "@/server/auth-state";
import type { Tables, UserRole } from "@/types/database";

export async function getSessionProfile(): Promise<Tables<"profiles"> | null> {
  return (await getAuthState()).profile;
}

export async function requireRoles(roles: readonly UserRole[]): Promise<Tables<"profiles">> {
  const profile = await getSessionProfile();
  if (!profile) {
    redirect("/login");
  }
  if (profile.status !== "active") {
    redirect("/login?error=inactive");
  }
  if (!roles.includes(profile.role)) {
    if (profile.role === "admin") {
      redirect("/admin/dashboard");
    }
    if (profile.role === "dosen") {
      redirect("/dosen/dashboard");
    }
    redirect("/mahasiswa/dashboard");
  }
  return profile;
}

export async function redirectAuthenticatedUser(): Promise<void> {
  const profile = await getSessionProfile();
  if (!profile || profile.status !== "active") {
    return;
  }
  if (profile.role === "admin") {
    redirect("/admin/dashboard");
  }
  if (profile.role === "dosen") {
    redirect("/dosen/dashboard");
  }
  redirect("/mahasiswa/dashboard");
}
