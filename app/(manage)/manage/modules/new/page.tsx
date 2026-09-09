import { ModuleForm } from "@/components/manage/module-form";
import { PageHeader } from "@/components/ui/page-header";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { Suspense } from "react";
import { api } from "@/server/api/server";

export default function NewModulePage(){return <Suspense fallback={<PageContentFallback label="Memuat formulir modul" maxWidth="5xl"/>}><NewModuleContent/></Suspense>;}
async function NewModuleContent(){const caller=await api();const[nextOrder,plants]=await Promise.all([caller.modules.nextOrder(),caller.plants.selectOptions()]);return <div className="mx-auto max-w-5xl"><PageHeader eyebrow="Bab baru" title="Buat Modul Pembelajaran" description="Tambahkan satu atau lebih subbab dalam satu penyimpanan transaksional."/><div className="mt-7"><ModuleForm mode="create" plants={plants} initial={{title:"",description:"",moduleOrder:nextOrder,estimatedMinutes:30,chapterSummary:[],sourceFile:null,status:"published",speciesIds:[],lessons:[{sourceId:null,title:"",slug:"",content:"",keyPoints:[],sourceSections:[],lessonOrder:1}]}}/></div></div>;}
