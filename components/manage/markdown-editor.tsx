"use client";

import dynamic from "next/dynamic";
import type { MDXEditorProps } from "@mdxeditor/editor";

const InitializedMarkdownEditor = dynamic(
  () => import("@/components/manage/initialized-markdown-editor"),
  {
    ssr: false,
    loading: () => (
      <div className="flex min-h-64 items-center justify-center rounded-md border border-emerald-300 bg-white text-sm text-slate-500">
        Menyiapkan editor materi…
      </div>
    ),
  },
);

type MarkdownEditorProps = Pick<MDXEditorProps, "markdown" | "onChange">;

export function MarkdownEditor(props: MarkdownEditorProps) {
  return <InitializedMarkdownEditor {...props} />;
}
