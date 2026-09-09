import { Suspense, type ReactNode } from "react";
import { ManageShell } from "@/components/manage-shell";
import { ManagementShellFallback } from "@/components/ui/runtime-fallbacks";
import { requireRoles } from "@/server/auth";

async function AuthenticatedManagementShell({ children }: { children: ReactNode }) {
  const profile = await requireRoles(["admin", "dosen"]);
  return <ManageShell profile={profile}>{children}</ManageShell>;
}

export default function ManagementLayout({ children }: { children: ReactNode }) {
  return (
    <Suspense fallback={<ManagementShellFallback />}>
      <AuthenticatedManagementShell>{children}</AuthenticatedManagementShell>
    </Suspense>
  );
}
