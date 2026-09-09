import { notFound } from "next/navigation";
import { connection } from "next/server";
import { Suspense } from "react";
import { QuizForm } from "@/components/manage/quiz-form";
import { PageHeader } from "@/components/ui/page-header";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

type Props={params:Promise<{id:string}>};
export default function EditQuizPage({params}:Props){return <Suspense fallback={<PageContentFallback label="Memuat data kuis" maxWidth="2xl"/>}><EditQuizContent params={params}/></Suspense>;}
async function EditQuizContent({params}:Props){await connection();await requireRoles(["admin","dosen"]);const id=Number((await params).id);if(!Number.isInteger(id))notFound();const caller=await api();const[quiz,modules]=await Promise.all([caller.quizzes.manageById({id}),caller.modules.selectOptions()]);return <div className="mx-auto max-w-2xl"><PageHeader eyebrow="Konfigurasi kuis" title={`Edit ${quiz.title}`}/><div className="mt-7"><QuizForm mode="edit" modules={modules} initial={{id:quiz.id,moduleId:quiz.module_id,title:quiz.title,quizType:quiz.quiz_type,passingScore:quiz.passing_score,duration:quiz.duration,status:quiz.status}}/></div></div>;}
