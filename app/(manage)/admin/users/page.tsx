import { PageHeader } from "@/components/ui/page-header";
import { Pagination } from "@/components/ui/pagination";
import { UsersPanel } from "@/components/manage/users-panel";
import { Suspense } from "react";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { requireRoles } from "@/server/auth";
import { api } from "@/server/api/server";

type Props={searchParams:Promise<Record<string,string|string[]|undefined>>};function v(input:string|string[]|undefined):string{return Array.isArray(input)?(input[0]??""):(input??"");}
export default function UsersPage({searchParams}:Props){return <Suspense fallback={<PageContentFallback label="Memuat daftar pengguna"/>}><UsersContent searchParams={searchParams}/></Suspense>;}
async function UsersContent({searchParams}:Props){const profile=await requireRoles(["admin"]);const params=await searchParams;const search=v(params.search);const role=v(params.role);const page=Math.max(1,Number(v(params.page))||1);const data=await (await api()).users.list({search,role:role==="admin"||role==="dosen"||role==="mahasiswa"?role:"all",page,pageSize:10});return <div className="mx-auto max-w-7xl"><PageHeader eyebrow="Hak akses" title="Kelola Pengguna" description="Buat akun internal, ubah role, aktifkan/nonaktifkan, atau hapus akun Supabase Auth."/><form className="panel mt-7 grid gap-3 p-5 sm:grid-cols-[1fr_220px_auto]"><input className="form-input" name="search" defaultValue={search} placeholder="Cari nama, email, atau institusi"/><select className="form-input" name="role" defaultValue={role||"all"}><option value="all">Semua role</option><option value="mahasiswa">Mahasiswa</option><option value="dosen">Dosen</option><option value="admin">Admin</option></select><button className="btn-primary">Filter</button></form><div className="mt-7"><UsersPanel users={data.items} currentId={profile.id}/></div><Pagination page={data.page} pageSize={data.pageSize} total={data.total} pathname="/admin/users" searchParams={{search,role}}/></div>;}
