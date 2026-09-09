import { TRPCError } from "@trpc/server";
import { notFound } from "next/navigation";

// Only missing records should render a 404; service failures must remain retryable.
export function handlePageError(error: unknown): never {
  if (error instanceof TRPCError && error.code === "NOT_FOUND") {
    notFound();
  }
  throw error;
}
