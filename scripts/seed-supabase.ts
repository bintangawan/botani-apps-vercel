import "@/scripts/load-env";
import { readFile } from "node:fs/promises";
import path from "node:path";
import { createClient, type PostgrestSingleResponse } from "@supabase/supabase-js";
import { z } from "zod";
import { getServiceEnv } from "@/lib/env";
import { slugify } from "@/lib/utils";
import { seedQuizzes } from "@/scripts/quiz-seed";
import type { CotyledonType, Database, GroupType, TaxonRank, TablesInsert } from "@/types/database";

const locationSchema = z.object({ kecamatan: z.string().optional(), kabupaten: z.string().optional(), kota: z.string().optional(), provinsi: z.string().optional(), tempat: z.string().optional(), desa: z.string().optional() });
const taxonomySchema = z.object({ kingdom: z.string().optional(), divisi: z.string().optional(), kelas: z.string().optional(), ordo: z.string().optional(), famili: z.string().optional(), genus: z.string().optional(), spesies: z.string().optional() });
const plantSchema = z.object({ id: z.string().min(1), nama: z.string().min(1), nama_ilmiah: z.string().min(1), lokasi: locationSchema.optional(), taksonomi: taxonomySchema.optional(), image: z.object({ file_name: z.string(), link: z.string() }).optional() });
const plantsSchema = z.array(plantSchema);
const lessonSchema = z.object({ id: z.string(), order: z.number().int(), title: z.string(), slug: z.string(), content: z.string(), content_format: z.string().optional(), key_points: z.array(z.string()).optional(), source_sections: z.array(z.string()).optional() });
const chapterFileSchema = z.object({ chapter: z.number().int(), title: z.string(), slug: z.string(), description: z.string(), estimated_minutes: z.number().int(), chapter_summary: z.array(z.string()).optional(), lessons: z.array(lessonSchema) });
const moduleIndexSchema = z.object({ chapters: z.array(z.object({ file: z.string() })).length(9) });

type Theory = {
  description: string;
  habitat: string;
  benefits: string;
  morphology: Omit<TablesInsert<"morphologies">, "species_id">;
  physiology: Omit<TablesInsert<"physiologies">, "species_id">;
};

const datasetFiles = ["dataset_botani_observasi.json", "dataset_botani2_observasi.json", "dataset_botani3_observasi.json", "dataset_botani4_observasi.json", "dataset_botani5_observasi.json"] as const;
const ranks: TaxonRank[] = ["kingdom", "divisi", "kelas", "ordo", "famili", "genus", "spesies"];
const gymnoDivisions = new Set(["pinophyta", "cycadophyta", "gnetophyta", "coniferophyta", "ginkgophyta"]);
const gymnoClasses = new Set(["pinopsida", "cycadopsida", "gnetopsida", "gymnospermae"]);
const monoClasses = new Set(["liliopsida", "monocotyledoneae"]);
const dicotClasses = new Set(["magnoliopsida", "dicotyledoneae", "rosopsida"]);
const monoFamilies = new Set(["arecaceae", "poaceae", "musaceae", "zingiberaceae", "orchidaceae", "araceae", "cyperaceae", "pontederiaceae", "bromeliaceae"]);

function classification(taxonomy: z.infer<typeof taxonomySchema>): { group: GroupType; cotyledon: CotyledonType } {
  const division = taxonomy.divisi?.toLowerCase() ?? "";
  const plantClass = taxonomy.kelas?.toLowerCase() ?? "";
  const family = taxonomy.famili?.toLowerCase() ?? "";
  if (gymnoDivisions.has(division) || gymnoClasses.has(plantClass)) return { group: "Gymnospermae", cotyledon: "Tidak Berlaku" };
  if (monoClasses.has(plantClass) || monoFamilies.has(family)) return { group: "Angiospermae", cotyledon: "Monokotil" };
  if (dicotClasses.has(plantClass)) return { group: "Angiospermae", cotyledon: "Dikotil" };
  return { group: "Angiospermae", cotyledon: "Dikotil" };
}

function theory(name: string, scientificName: string, group: GroupType, cotyledon: CotyledonType, family: string): Theory {
  if (group === "Gymnospermae") return {
    description: `${name} (${scientificName}) merupakan tumbuhan berbiji terbuka dari famili ${family}. Bijinya tidak terbungkus ovarium dan organ reproduksinya umumnya tersusun dalam strobilus.`,
    habitat: "Umumnya tumbuh pada hutan dataran tinggi, kawasan pegunungan, atau area konservasi.",
    benefits: "Berperan dalam konservasi tanah, penyerapan karbon, pertamanan, serta pemanfaatan kayu atau resin.",
    morphology: { root: "Akar tunggang kuat yang berkembang dalam.", stem: "Batang berkayu dengan pertumbuhan sekunder.", leaf: "Daun berbentuk jarum, sisik, atau helaian tebal berkutikula.", flower: "Tidak memiliki bunga sejati; organ reproduksi berupa strobilus.", fruit: "Tidak menghasilkan buah sejati.", seed: "Biji terbuka pada sisik megasporofil.", special_characteristics: "Jaringan batang dan daun sering memiliki saluran resin." },
    physiology: { reproduction: "Penyerbukan umumnya dibantu angin.", growth: "Kambium sekunder membentuk xilem dan floem baru.", adaptation: "Adaptif terhadap radiasi tinggi dan variasi kelembapan.", additional_information: "Uraian awal dapat dikembangkan lebih lanjut oleh dosen berdasarkan observasi." },
  };
  if (cotyledon === "Monokotil") return {
    description: `${name} (${scientificName}) merupakan Angiospermae monokotil dari famili ${family}, dengan satu kotiledon dan berkas pembuluh batang yang tersebar.`,
    habitat: "Tumbuh pada lingkungan tropis dari dataran rendah hingga menengah sesuai karakter ekologis spesies.",
    benefits: "Berpotensi sebagai pangan, bahan industri, tanaman hias, atau sumber senyawa alami.",
    morphology: { root: "Sistem akar serabut berkembang pada lapisan atas tanah.", stem: "Batang memiliki berkas pembuluh tersebar dan umumnya tanpa kambium.", leaf: "Daun bertulang sejajar atau melengkung dengan pelepah.", flower: "Bagian bunga umumnya berkelipatan tiga.", fruit: "Tipe buah bervariasi sesuai famili.", seed: "Biji memiliki satu kotiledon.", special_characteristics: "Akar serabut dan pertulangan daun sejajar menjadi karakter umum." },
    physiology: { reproduction: "Reproduksi berlangsung secara generatif dan dapat pula vegetatif.", growth: "Pertumbuhan dominan berasal dari meristem primer.", adaptation: "Pengaturan stomata mendukung adaptasi terhadap ketersediaan air.", additional_information: "Uraian awal dapat dikembangkan lebih lanjut oleh dosen berdasarkan observasi." },
  };
  return {
    description: `${name} (${scientificName}) merupakan Angiospermae dikotil dari famili ${family}, dengan dua kotiledon dan berkas pembuluh tersusun melingkar.`,
    habitat: "Dijumpai pada hutan tropis, kebun, atau lahan budidaya dari dataran rendah hingga menengah.",
    benefits: "Berpotensi sebagai pangan, bahan kayu, tanaman obat, peneduh, atau penopang ekosistem.",
    morphology: { root: "Sistem akar tunggang dengan cabang lateral.", stem: "Batang bercabang dengan kambium aktif.", leaf: "Daun umumnya bertulang menyirip atau menjari.", flower: "Bagian bunga umumnya berkelipatan empat atau lima.", fruit: "Buah melindungi biji di dalam ovarium yang berkembang.", seed: "Biji memiliki dua kotiledon.", special_characteristics: "Pertumbuhan sekunder dan tajuk bercabang menjadi karakter umum." },
    physiology: { reproduction: "Penyerbukan dapat dibantu serangga, hewan lain, atau angin.", growth: "Kambium vaskuler mendukung pertumbuhan sekunder.", adaptation: "Proses fisiologis beradaptasi terhadap kondisi lingkungan tropis.", additional_information: "Uraian awal dapat dikembangkan lebih lanjut oleh dosen berdasarkan observasi." },
  };
}

function mimeType(fileName: string): string {
  const extension = path.extname(fileName).toLowerCase();
  if (extension === ".png") return "image/png";
  if (extension === ".webp") return "image/webp";
  return "image/jpeg";
}

async function seed(): Promise<void> {
  const env = getServiceEnv();
  const supabase = createClient<Database>(env.NEXT_PUBLIC_SUPABASE_URL, env.SUPABASE_SECRET_KEY, { auth: { persistSession: false, autoRefreshToken: false } });
  const bucket = env.NEXT_PUBLIC_STORAGE_BUCKET;
  const { error: bucketError } = await supabase.storage.createBucket(bucket, { public: true, fileSizeLimit: 4 * 1024 * 1024, allowedMimeTypes: ["image/jpeg", "image/png", "image/webp"] });
  if (bucketError && !bucketError.message.toLowerCase().includes("already exists")) throw bucketError;
  const { error: settingError } = await supabase.from("app_settings").upsert({ key: "storage_bucket", value: bucket });
  if (settingError) throw settingError;

  for (const fileName of datasetFiles) {
    const raw = await readFile(path.join(process.cwd(), "json-data", fileName), "utf8");
    const items = plantsSchema.parse(JSON.parse(raw) as unknown);
    for (const item of items) {
      const taxonomy = item.taksonomi ?? {};
      const group = classification(taxonomy);
      const plantTheory = theory(item.nama, item.nama_ilmiah, group.group, group.cotyledon, taxonomy.famili ?? "belum ditentukan");
      const imagePath = item.image ? `plants/seed/${item.image.file_name}` : null;
      if (item.image && imagePath) {
        const image = await readFile(path.join(process.cwd(), "storages", "images", item.image.file_name));
        const { error: uploadError } = await supabase.storage.from(bucket).upload(imagePath, image, { contentType: mimeType(item.image.file_name), upsert: true });
        if (uploadError) throw uploadError;
      }
      const { data: plant, error: plantError } = await supabase.from("plant_species").upsert({ code: item.id, slug: `${slugify(item.nama)}-${item.id}`, local_name: item.nama, scientific_name: item.nama_ilmiah, author_name: null, group_type: group.group, cotyledon_type: group.cotyledon, description: plantTheory.description, habitat: plantTheory.habitat, benefits: plantTheory.benefits, image_path: imagePath, status: "published" }, { onConflict: "code" }).select("id").single();
      if (plantError) throw plantError;
      const [morphologyResult, physiologyResult] = await Promise.all([
        supabase.from("morphologies").upsert({ species_id: plant.id, ...plantTheory.morphology }),
        supabase.from("physiologies").upsert({ species_id: plant.id, ...plantTheory.physiology }),
      ]);
      if (morphologyResult.error) throw morphologyResult.error;
      if (physiologyResult.error) throw physiologyResult.error;
      const { error: linkDeleteError } = await supabase.from("species_taxa").delete().eq("species_id", plant.id);
      if (linkDeleteError) throw linkDeleteError;
      let parentId: number | null = null;
      for (const rank of ranks) {
        const name = taxonomy[rank];
        if (!name) continue;
        const taxonResult: PostgrestSingleResponse<{ id: number }> = await supabase.from("taxa").upsert({ rank, name, parent_id: parentId }, { onConflict: "rank,name" }).select("id").single();
        if (taxonResult.error) throw taxonResult.error;
        const taxon: { id: number } | null = taxonResult.data;
        if (!taxon) throw new Error("ID takson tidak diterima dari database.");
        const { error: linkError } = await supabase.from("species_taxa").upsert({ species_id: plant.id, taxon_id: taxon.id });
        if (linkError) throw linkError;
        parentId = taxon.id;
      }
      const location = item.lokasi ?? {};
      const district = location.kecamatan ?? "Tidak diketahui";
      const regency = location.kabupaten ?? location.kota ?? "Tidak diketahui";
      const province = location.provinsi ?? "Tidak diketahui";
      const village = location.tempat ?? location.desa ?? null;
      const { data: savedLocation, error: locationError } = await supabase.from("locations").upsert({ location_name: village ? `${village}, ${district}` : `${district}, ${regency}`, village, district, regency, province }, { onConflict: "district,regency,province" }).select("id").single();
      if (locationError) throw locationError;
      const { data: observation, error: observationError } = await supabase.from("plant_observations").upsert({ species_id: plant.id, location_id: savedLocation.id, observation_date: null, notes: `Spesimen ${item.nama} (${item.nama_ilmiah}) didokumentasikan di ${district}, ${regency}.` }, { onConflict: "species_id,location_id" }).select("id").single();
      if (observationError) throw observationError;
      if (item.image && imagePath) {
        const { error: mediaError } = await supabase.from("media").upsert({ species_id: plant.id, observation_id: observation.id, filename: item.image.file_name, file_path: imagePath, media_type: "image", caption: `Dokumentasi ${item.nama} (${item.nama_ilmiah})`, is_primary: true }, { onConflict: "species_id,file_path" });
        if (mediaError) throw mediaError;
      }
    }
    console.info(`Dataset selesai: ${fileName}`);
  }

  const moduleIndexRaw = await readFile(path.join(process.cwd(), "data", "modules.json"), "utf8");
  const moduleIndex = moduleIndexSchema.parse(JSON.parse(moduleIndexRaw) as unknown);
  for (const entry of moduleIndex.chapters) {
    const chapterRaw = await readFile(path.join(process.cwd(), "data", entry.file), "utf8");
    const chapter = chapterFileSchema.parse(JSON.parse(chapterRaw) as unknown);
    const { data: savedModule, error: moduleError } = await supabase.from("learning_modules").upsert({ title: chapter.title, slug: chapter.slug, description: chapter.description, module_order: chapter.chapter, estimated_minutes: chapter.estimated_minutes, chapter_summary: chapter.chapter_summary ?? [], source_file: entry.file, status: "published" }, { onConflict: "slug" }).select("id").single();
    if (moduleError) throw moduleError;
    const { error: lessonDeleteError } = await supabase.from("learning_lessons").delete().eq("module_id", savedModule.id);
    if (lessonDeleteError) throw lessonDeleteError;
    const lessons: TablesInsert<"learning_lessons">[] = chapter.lessons.map((lesson) => ({ module_id: savedModule.id, source_id: lesson.id, title: lesson.title, slug: lesson.slug, content: lesson.content, content_format: "markdown", key_points: lesson.key_points ?? [], source_sections: lesson.source_sections ?? [], lesson_order: lesson.order }));
    const { error: lessonsError } = await supabase.from("learning_lessons").insert(lessons);
    if (lessonsError) throw lessonsError;
    console.info(`Modul selesai: ${chapter.title}`);
  }
  await seedQuizzes(supabase);
  console.info("Seed Supabase selesai tanpa akun hardcoded.");
}

seed().catch((error: unknown) => { console.error(error instanceof Error ? error.message : error); process.exitCode = 1; });
