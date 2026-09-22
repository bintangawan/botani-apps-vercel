import { NextResponse } from "next/server";
import { createSupabaseServerClient } from "@/lib/supabase/server";

export async function GET(request: Request): Promise<Response> {
  const url = new URL(request.url);
  const code = url.searchParams.get("code");

  if (code) {
    const supabase = await createSupabaseServerClient();
    const { error } = await supabase.auth.exchangeCodeForSession(code);
    if (!error) {
      // Confirmation finishes registration; users sign in explicitly afterward.
      await supabase.auth.signOut();
      const loginUrl = new URL("/login", url.origin);
      loginUrl.searchParams.set("confirmed", crypto.randomUUID());
      return NextResponse.redirect(loginUrl);
    }
  }

  return NextResponse.redirect(new URL("/login?error=confirmation", url.origin));
}
