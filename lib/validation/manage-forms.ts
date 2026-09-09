import { z } from "zod";

export const groupTypeSchema = z.enum(["Gymnospermae", "Angiospermae"]);
export const cotyledonTypeSchema = z.enum([
  "Monokotil",
  "Dikotil",
  "Tidak Berlaku",
]);
export const publicationStatusSchema = z.enum(["draft", "published"]);

const morphologySchema = z.object({
  root: z.string().max(5000, "Maksimal 5.000 karakter.").default(""),
  stem: z.string().max(5000, "Maksimal 5.000 karakter.").default(""),
  leaf: z.string().max(5000, "Maksimal 5.000 karakter.").default(""),
  flower: z.string().max(5000, "Maksimal 5.000 karakter.").default(""),
  fruit: z.string().max(5000, "Maksimal 5.000 karakter.").default(""),
  seed: z.string().max(5000, "Maksimal 5.000 karakter.").default(""),
  specialCharacteristics: z
    .string()
    .max(5000, "Maksimal 5.000 karakter.")
    .default(""),
});

const taxonomySchema = z.object({
  kingdom: z.string().trim().max(255, "Maksimal 255 karakter.").default("Plantae"),
  divisi: z.string().trim().max(255, "Maksimal 255 karakter.").default(""),
  kelas: z.string().trim().max(255, "Maksimal 255 karakter.").default(""),
  ordo: z.string().trim().max(255, "Maksimal 255 karakter.").default(""),
  famili: z.string().trim().max(255, "Maksimal 255 karakter.").default(""),
  genus: z.string().trim().max(255, "Maksimal 255 karakter.").default(""),
  spesies: z.string().trim().max(255, "Maksimal 255 karakter.").default(""),
});

export const plantInputSchema = z.object({
  id: z.number().int().positive().optional(),
  code: z
    .string()
    .trim()
    .min(2, "Kode minimal 2 karakter.")
    .max(50, "Kode maksimal 50 karakter.")
    .regex(/^[A-Za-z0-9_-]+$/, "Gunakan hanya huruf, angka, garis bawah, atau tanda hubung."),
  localName: z
    .string()
    .trim()
    .min(2, "Nama lokal minimal 2 karakter.")
    .max(255, "Nama lokal maksimal 255 karakter."),
  scientificName: z
    .string()
    .trim()
    .min(2, "Nama ilmiah minimal 2 karakter.")
    .max(255, "Nama ilmiah maksimal 255 karakter."),
  authorName: z.string().trim().max(255, "Nama author maksimal 255 karakter.").default(""),
  groupType: groupTypeSchema,
  cotyledonType: cotyledonTypeSchema,
  description: z.string().max(20000, "Deskripsi maksimal 20.000 karakter.").default(""),
  habitat: z.string().max(10000, "Habitat maksimal 10.000 karakter.").default(""),
  benefits: z.string().max(10000, "Manfaat maksimal 10.000 karakter.").default(""),
  imagePath: z.string().max(1000).nullable().default(null),
  status: publicationStatusSchema,
  morphology: morphologySchema,
  taxonomy: taxonomySchema,
});

const lessonInputSchema = z.object({
  sourceId: z.string().trim().max(255, "ID sumber maksimal 255 karakter.").nullable().default(null),
  title: z
    .string()
    .trim()
    .min(2, "Judul subbab minimal 2 karakter.")
    .max(255, "Judul subbab maksimal 255 karakter."),
  slug: z.string().trim().max(255, "Slug maksimal 255 karakter.").default(""),
  content: z
    .string()
    .min(1, "Isi materi wajib diisi.")
    .max(200000, "Isi materi maksimal 200.000 karakter."),
  keyPoints: z
    .array(z.string().trim().min(1).max(1000, "Setiap poin maksimal 1.000 karakter."))
    .max(50, "Maksimal 50 poin penting.")
    .default([]),
  sourceSections: z
    .array(z.string().trim().min(1).max(255, "Setiap bagian maksimal 255 karakter."))
    .max(50, "Maksimal 50 bagian sumber.")
    .default([]),
  lessonOrder: z.number().int().positive(),
});

export const moduleInputSchema = z.object({
  id: z.number().int().positive().optional(),
  title: z
    .string()
    .trim()
    .min(2, "Judul bab minimal 2 karakter.")
    .max(255, "Judul bab maksimal 255 karakter."),
  description: z.string().max(10000, "Deskripsi maksimal 10.000 karakter.").default(""),
  moduleOrder: z.number().int().min(1, "Urutan minimal 1.").max(10000, "Urutan maksimal 10.000."),
  estimatedMinutes: z.number().int().min(0, "Estimasi tidak boleh negatif.").max(10000, "Estimasi maksimal 10.000 menit."),
  chapterSummary: z
    .array(z.string().trim().min(1).max(2000, "Setiap ringkasan maksimal 2.000 karakter."))
    .max(100, "Maksimal 100 poin ringkasan.")
    .default([]),
  sourceFile: z.string().trim().max(255, "Nama file sumber maksimal 255 karakter.").nullable().default(null),
  status: publicationStatusSchema,
  speciesIds: z.array(z.number().int().positive()).max(500, "Maksimal 500 spesies.").default([]),
  lessons: z.array(lessonInputSchema).min(1, "Minimal satu subbab.").max(100, "Maksimal 100 subbab."),
});

export const quizTypeSchema = z.enum([
  "pilihan_ganda",
  "esai",
  "campuran",
  "pretest",
  "practice",
  "posttest",
]);

export const quizInputSchema = z.object({
  id: z.number().int().positive().optional(),
  moduleId: z.number().int().positive("Pilih modul yang valid."),
  title: z
    .string()
    .trim()
    .min(2, "Judul kuis minimal 2 karakter.")
    .max(255, "Judul kuis maksimal 255 karakter."),
  quizType: quizTypeSchema,
  passingScore: z.number().int().min(0, "Nilai minimal 0.").max(100, "Nilai maksimal 100."),
  duration: z.number().int().min(1, "Durasi minimal 1 menit.").max(360, "Durasi maksimal 360 menit."),
  status: publicationStatusSchema,
});
