import { notFound } from "next/navigation";
import { connection } from "next/server";
import { Suspense } from "react";
import { QuestionBank } from "@/components/manage/question-bank";
import { PageHeader } from "@/components/ui/page-header";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

type Props={params:Promise<{id:string}>};
export default function QuestionsPage({params}:Props){return <Suspense fallback={<PageContentFallback label="Memuat bank soal"/>}><QuestionsContent params={params}/></Suspense>;}
async function QuestionsContent({params}:Props){await connection();await requireRoles(["admin","dosen"]);const quizId=Number((await params).id);if(!Number.isInteger(quizId))notFound();const data=await (await api()).quizzes.questions({quizId});return <div className="mx-auto max-w-7xl"><PageHeader eyebrow="Bank soal" title={data.quiz.title} description="Kunci jawaban tersimpan terpisah dan hanya dapat dibaca admin/dosen serta fungsi penilaian server."/><div className="mt-7"><QuestionBank quizId={quizId} questions={data.questions}/></div></div>;}
