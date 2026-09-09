import { QuizForm } from "@/components/manage/quiz-form";
import { PageHeader } from "@/components/ui/page-header";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { Suspense } from "react";
import { api } from "@/server/api/server";

export default function NewQuizPage(){return <Suspense fallback={<PageContentFallback label="Memuat formulir kuis" maxWidth="2xl"/>}><NewQuizContent/></Suspense>;}
async function NewQuizContent(){const modules=await (await api()).modules.selectOptions();return <div className="mx-auto max-w-2xl"><PageHeader eyebrow="Kuis baru" title="Buat Evaluasi" description="Setelah disimpan, kamu akan diarahkan ke penyusunan bank soal."/><div className="mt-7"><QuizForm mode="create" modules={modules} initial={{moduleId:modules[0]?.id??0,title:"",quizType:"pilihan_ganda",passingScore:60,duration:30,status:"draft"}}/></div></div>;}
