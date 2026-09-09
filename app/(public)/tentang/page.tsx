import type { Metadata } from "next";
import { BotaniLogo } from "@/components/botani-logo";

export const metadata: Metadata = { title: "Tentang" };

const roles = [
  {
    icon: "🎓",
    title: "Mahasiswa",
    tone: "bg-emerald-100 text-emerald-700",
    description:
      "Mengakses katalog dan modul pembelajaran, mengikuti kuis evaluasi, serta memantau riwayat skor dari dashboard belajar pribadi.",
  },
  {
    icon: "👨‍🏫",
    title: "Dosen Pengampu",
    tone: "bg-teal-100 text-teal-700",
    description:
      "Mengelola materi teori dan spesimen tumbuhan, menyusun bank soal dan kuis, serta memonitor kemajuan belajar mahasiswa.",
  },
  {
    icon: "⚙️",
    title: "Administrator",
    tone: "bg-amber-100 text-amber-700",
    description:
      "Memegang kendali manajemen pengguna, observasi lapangan, serta integritas basis data flora dan aset herbarium.",
  },
];

export default function AboutPage() {
  return (
    <div className="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-20 lg:px-8">
      <header className="mx-auto mb-16 max-w-3xl text-center">
        <p className="eyebrow mb-2 inline-flex items-center gap-1.5"><BotaniLogo decorative className="size-4" />Penelitian &amp; Pengabdian</p>
        <h1 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
          Tentang Botani Phanerogamae
        </h1>
        <p className="mt-3 text-sm text-slate-700 sm:text-base">
          Integrasi teknologi informasi, herbarium digital, dan media evaluasi
          pembelajaran taksonomi tumbuhan tingkat tinggi di Sumatera Utara.
        </p>
      </header>

      <div className="space-y-12">
        <section className="space-y-6 rounded-xl border border-emerald-200 bg-white p-8 shadow-xl sm:p-12">
          <h2 className="border-b border-emerald-200 pb-4 text-2xl font-bold text-slate-900">
            Latar Belakang &amp; Tujuan
          </h2>
          <p className="leading-relaxed text-slate-700">
            Mata kuliah Botani Phanerogamae merupakan salah satu pondasi penting
            dalam studi ilmu Biologi yang berfokus pada pengenalan, klasifikasi,
            anatomi, serta distribusi tumbuhan berbiji (
            <span className="font-serif italic">Spermatophyta</span>). Provinsi
            Sumatera Utara memiliki keanekaragaman flora tropis yang melimpah,
            dari kawasan pesisir hingga pegunungan tinggi, baik kelompok{" "}
            <span className="font-semibold text-amber-700">Gymnospermae</span>{" "}
            maupun{" "}
            <span className="font-semibold text-emerald-700">Angiospermae</span>
            .
          </p>
          <p className="leading-relaxed text-slate-700">
            Platform ini dikembangkan sebagai media pembelajaran interaktif
            sekaligus repositori herbarium digital. Mahasiswa dan peneliti dapat
            mempelajari karakteristik morfologi, mengikuti evaluasi pretest dan
            posttest, serta memperdalam pemahaman taksonomi secara visual dan
            modern.
          </p>
        </section>

        <section className="space-y-8 rounded-xl border border-emerald-200 bg-white p-8 shadow-xl sm:p-12">
          <h2 className="border-b border-emerald-200 pb-4 text-2xl font-bold text-slate-900">
            Struktur Hak Akses &amp; Peran Pengguna
          </h2>
          <div className="grid gap-6 md:grid-cols-3">
            {roles.map((role) => (
              <article
                key={role.title}
                className="space-y-3 rounded-xl border border-emerald-300 bg-white p-6"
              >
                <span
                  className={`grid size-10 place-items-center rounded-xl text-lg ${role.tone}`}
                >
                  {role.icon}
                </span>
                <h3 className="text-lg font-bold text-slate-900">
                  {role.title}
                </h3>
                <p className="text-xs leading-relaxed text-slate-600">
                  {role.description}
                </p>
              </article>
            ))}
          </div>
        </section>

        <section className="space-y-4 rounded-xl border border-emerald-300 bg-emerald-100 p-8 text-center sm:p-12">
          <p className="eyebrow">⚡ Metodologi &amp; Eksplorasi</p>
          <h2 className="text-2xl font-bold text-slate-900">
            Ekosistem Digital Riset &amp; Pembelajaran Taksonomi
          </h2>
          <p className="mx-auto max-w-2xl text-sm text-slate-700">
            Menghadirkan ensiklopedia flora terintegrasi dengan klasifikasi
            taksonomi mutakhir, dokumentasi herbarium, serta modul pembelajaran
            interaktif untuk eksplorasi biologi tropis.
          </p>
        </section>
      </div>
    </div>
  );
}
