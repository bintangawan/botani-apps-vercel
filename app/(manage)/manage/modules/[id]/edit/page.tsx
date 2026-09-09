import { notFound } from "next/navigation";
import { connection } from "next/server";
import { Suspense } from "react";
import { ModuleForm } from "@/components/manage/module-form";
import { PageHeader } from "@/components/ui/page-header";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { asStringArray } from "@/lib/utils";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

type Props={params:Promise<{id:string}>};
export default function EditModulePage({params}:Props){return <Suspense fallback={<PageContentFallback label="Memuat data modul" maxWidth="5xl"/>}><EditModuleContent params={params}/></Suspense>;}
async function EditModuleContent({params}:Props){await connection();await requireRoles(["admin","dosen"]);const id=Number((await params).id);if(!Number.isInteger(id))notFound();const caller=await api();const[module,plants]=await Promise.all([caller.modules.manageById({id}),caller.plants.selectOptions()]);return <div className="mx-auto max-w-5xl"><PageHeader eyebrow={`Bab ${module.module_order}`} title={`Edit ${module.title}`} description="Perubahan subbab disimpan sebagai satu transaksi database."/><div className="mt-7"><ModuleForm mode="edit" plants={plants} initial={{id:module.id,title:module.title,description:module.description,moduleOrder:module.module_order,estimatedMinutes:module.estimated_minutes,chapterSummary:asStringArray(module.chapter_summary),sourceFile:module.source_file,status:module.status,speciesIds:module.speciesIds,lessons:module.lessons.map((lesson)=>({sourceId:lesson.source_id,title:lesson.title,slug:lesson.slug,content:lesson.content,keyPoints:asStringArray(lesson.key_points),sourceSections:asStringArray(lesson.source_sections),lessonOrder:lesson.lesson_order}))}}/></div></div>;}
