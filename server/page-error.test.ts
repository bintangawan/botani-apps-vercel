import assert from "node:assert/strict";
import { test } from "node:test";
import { TRPCError } from "@trpc/server";
import { handlePageError } from "./page-error";

test("missing records render Next.js not-found", () => {
  assert.throws(
    () => handlePageError(new TRPCError({ code: "NOT_FOUND" })),
    (error: unknown) => error instanceof Error && "digest" in error && error.digest === "NEXT_HTTP_ERROR_FALLBACK;404",
  );
});

for (const code of ["INTERNAL_SERVER_ERROR", "UNAUTHORIZED", "FORBIDDEN"] as const) {
  test(`${code} is preserved instead of being disguised as a 404`, () => {
    const error = new TRPCError({ code });
    assert.throws(() => handlePageError(error), (actual) => actual === error);
  });
}

test("network failures remain available to the retry error boundary", () => {
  const error = new TypeError("fetch failed");
  assert.throws(() => handlePageError(error), (actual) => actual === error);
});
