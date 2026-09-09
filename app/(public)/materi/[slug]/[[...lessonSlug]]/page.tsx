import type { Metadata } from "next";
import Link from "next/link";
import {
  ArrowLeft,
  ArrowRight,
  CheckCircle2,
  Clock3,
  ListChecks,
  ListTree,
  LockKeyhole,
} from "lucide-react";
import { notFound } from "next/navigation";
import { Suspense } from "react";
import ReactMarkdown from "react-markdown";
import remarkGfm from "remark-gfm";
import { ModuleDetailFallback } from "@/components/ui/runtime-fallbacks";
import { asStringArray, cn } from "@/lib/utils";
import { getModuleBySlug } from "@/server/public-data";
import { handlePageError } from "@/server/page-error";

type ModuleDetailProps = {
  params: Promise<{ slug: string; lessonSlug?: string[] }>;
};

export async function generateMetadata({
  params,
}: ModuleDetailProps): Promise<Metadata> {
  const { slug, lessonSlug } = await params;
  try {
    const data = await getModuleBySlug(slug, lessonSlug?.[0]);
    return { title: data.lesson.title, description: data.module.description };
  } catch {
    return { title: "Materi pembelajaran" };
  }
}

export default function ModuleDetailPage({ params }: ModuleDetailProps) {
  return (
    <Suspense fallback={<ModuleDetailFallback />}>
      <ModuleDetailContent params={params} />
    </Suspense>
  );
}

async function ModuleDetailContent({ params }: ModuleDetailProps) {
  const { slug, lessonSlug } = await params;
  if (lessonSlug && lessonSlug.length > 1) notFound();
  const data = await getModuleBySlug(slug, lessonSlug?.[0]).catch(
    handlePageError,
  );
  const keyPoints = asStringArray(data.lesson.key_points);
  const isLastLessonInModule = data.lessons.at(-1)?.id === data.lesson.id;

  return (
    <div className="relative mx-auto max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8">
      <div className="grid items-start gap-8 lg:grid-cols-[320px_minmax(0,1fr)]">
        <aside className="lg:sticky lg:top-24 lg:h-[calc(100vh-7rem)] lg:overflow-y-auto">
          <div className="overflow-hidden rounded-md border border-slate-200 bg-white p-4 shadow-lg">
            <div className="flex items-center gap-2 px-2 py-2">
              <ListTree className="size-5 text-emerald-600" />
              <h2 className="font-extrabold text-slate-950">Daftar isi</h2>
            </div>
            <nav className="mt-3 space-y-4">
              {data.outline.map((chapter) => (
                <div key={chapter.id}>
                  <p className="px-2 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                    Bab {chapter.module_order}
                  </p>
                  <div className="mt-1 space-y-1">
                    {chapter.lessons.map((lesson) => (
                      <Link
                        key={lesson.id}
                        href={`/materi/${chapter.slug}/${lesson.slug}`}
                        className={cn(
                          "block rounded-md px-3 py-2.5 text-xs leading-5 text-slate-600 hover:bg-emerald-50 hover:text-emerald-900",
                          lesson.id === data.lesson.id &&
                            "bg-emerald-100 font-bold text-emerald-900",
                        )}
                      >
                        {lesson.title}
                      </Link>
                    ))}
                  </div>
                  <Link
                    href={`/mahasiswa/quizzes?module=${chapter.module_order}`}
                    className="mt-3 flex items-center gap-3 rounded-md border border-amber-200 bg-amber-50 px-3 py-3 text-amber-800 transition-colors hover:border-amber-300 hover:bg-amber-100"
                  >
                    <ListChecks className="size-5 shrink-0" />
                    <span className="min-w-0 flex-1">
                      <span className="block text-xs font-extrabold">
                        Kuis Bab {chapter.module_order}
                      </span>
                      <span className="mt-0.5 block text-[10px] text-amber-700">
                        Login untuk mengakses kuis
                      </span>
                    </span>
                    <LockKeyhole className="size-4 shrink-0" />
                  </Link>
                </div>
              ))}
            </nav>
          </div>
        </aside>
        <article className="min-w-0">
          <div className="panel overflow-hidden">
            <header className="border-b border-emerald-300 bg-emerald-100 p-6 sm:p-9">
              <p className="text-xs font-extrabold uppercase tracking-[.18em] text-emerald-700">
                Bab {data.module.module_order} · Subbab {data.lessonNumber} dari{" "}
                {data.totalLessons}
              </p>
              <h1 className="mt-3 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
                {data.lesson.title}
              </h1>
              <p className="mt-4 flex items-center gap-2 text-sm text-slate-600">
                <Clock3 className="size-4" /> Estimasi bab{" "}
                {data.module.estimated_minutes} menit
              </p>
            </header>
            <div className="p-6 sm:p-10">
              <div className="prose-botani">
                <ReactMarkdown remarkPlugins={[remarkGfm]} components={{
                  // Markdown images can be external and have no known dimensions.
                  img: ({ src, alt, title }) => (
                    <img src={src} alt={alt ?? ""} title={title} loading="lazy" decoding="async" className="h-auto max-w-full rounded-md" />
                  ),
                }}>
                  {data.lesson.content}
                </ReactMarkdown>
              </div>
              {keyPoints.length > 0 && (
                <section className="mt-10 rounded-md border border-amber-200 bg-amber-50 p-6 sm:p-8">
                  <p className="text-xs font-bold uppercase tracking-widest text-amber-700">
                    Rangkuman Subbab
                  </p>
                  <h2 className="mt-1 font-extrabold text-slate-900">
                    Yang perlu diingat
                  </h2>
                  <ul className="mt-5 grid gap-3 sm:grid-cols-2">
                    {keyPoints.map((point) => (
                      <li
                        key={point}
                        className="flex gap-3 rounded-md border border-amber-100 bg-white/70 p-3 text-sm leading-6 text-slate-700"
                      >
                        <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-amber-600" />{" "}
                        {point}
                      </li>
                    ))}
                  </ul>
                </section>
              )}
            </div>
          </div>
          <nav className="mt-6 grid gap-3 sm:grid-cols-2">
            {data.previous ? (
              <Link
                href={`/materi/${data.previous.moduleSlug}/${data.previous.lessonSlug}`}
                className="btn-secondary justify-start"
              >
                <ArrowLeft className="size-4" />
                <span className="truncate">{data.previous.lessonTitle}</span>
              </Link>
            ) : (
              <span />
            )}
            {isLastLessonInModule ? (
              <Link
                href={`/mahasiswa/quizzes?module=${data.module.module_order}`}
                className="btn-secondary justify-end"
              >
                <span className="truncate">
                  Kuis Bab {data.module.module_order}
                </span>
                <ArrowRight className="size-4" />
              </Link>
            ) : (
              data.next && (
                <Link
                  href={`/materi/${data.next.moduleSlug}/${data.next.lessonSlug}`}
                  className="btn-secondary justify-end"
                >
                  <span className="truncate">{data.next.lessonTitle}</span>
                  <ArrowRight className="size-4" />
                </Link>
              )
            )}
          </nav>
        </article>
      </div>
    </div>
  );
}
