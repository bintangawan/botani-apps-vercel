import { Suspense, type ReactNode } from "react";
import { PublicShell } from "@/components/public-shell";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { requireRoles } from "@/server/auth";

async function AuthenticatedStudentContent({ children }: { children: ReactNode }) {
  await requireRoles(["mahasiswa"]);
  return children;
}

export default function StudentLayout({ children }: { children: ReactNode }) {
  return (
    <PublicShell>
      <Suspense
        fallback={
          <div className="page-container py-12 sm:py-16">
            <PageContentFallback label="Memeriksa akses mahasiswa" />
          </div>
        }
      >
        <AuthenticatedStudentContent>{children}</AuthenticatedStudentContent>
      </Suspense>
    </PublicShell>
  );
}
