import { Suspense, type ReactNode } from "react";
import { PublicFooter } from "@/components/public-footer";
import { PublicHeader } from "@/components/public-header";
import { PublicHeaderFallback } from "@/components/public-header-fallback";
import { getSessionProfile } from "@/server/auth";

async function SessionAwarePublicHeader() {
  const profile = await getSessionProfile();
  return <PublicHeader profile={profile} />;
}

export function PublicShell({ children }: { children: ReactNode }) {
  return (
    <div className="flex min-h-screen flex-col">
      <Suspense fallback={<PublicHeaderFallback />}>
        <SessionAwarePublicHeader />
      </Suspense>
      <main className="flex-1">{children}</main>
      <PublicFooter />
    </div>
  );
}
