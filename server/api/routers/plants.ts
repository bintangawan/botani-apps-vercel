import { TRPCError } from "@trpc/server";
import { z } from "zod";
import { getPublicEnv } from "@/lib/env";
import { nullIfEmpty } from "@/lib/utils";
import {
  groupTypeSchema,
  cotyledonTypeSchema,
  plantInputSchema,
} from "@/lib/validation/manage-forms";
import {
  createUniqueSlug,
  decoratePlantCards,
  throwDatabaseError,
} from "@/server/api/helpers";
import {
  createTRPCRouter,
  managerProcedure,
  publicProcedure,
} from "@/server/api/trpc";
import type { Json } from "@/types/database";

const catalogInputSchema = z.object({
  search: z.string().trim().max(100).default(""),
  groupType: z.union([groupTypeSchema, z.literal("all")]).default("all"),
  cotyledonType: z.union([cotyledonTypeSchema, z.literal("all")]).default("all"),
  family: z.string().trim().max(100).default("all"),
  regency: z.string().trim().max(100).default("all"),
  page: z.number().int().min(1).default(1),
  pageSize: z.number().int().min(1).max(48).default(12),
});

function safeSearchValue(value: string): string {
  return value.replace(/[%_,().]/g, " ").replace(/\s+/g, " ").trim();
}

function intersectIds(left: number[] | null, right: number[]): number[] {
  if (left === null) {
    return [...new Set(right)];
  }
  const rightSet = new Set(right);
  return left.filter((id) => rightSet.has(id));
}

async function getFilteredSpeciesIds(
  supabase: Parameters<typeof decoratePlantCards>[0],
  family: string,
  regency: string,
): Promise<number[] | null> {
  let filtered: number[] | null = null;

  if (family !== "all") {
    const { data: familyTaxa, error: taxaError } = await supabase
      .from("taxa")
      .select("id")
      .eq("rank", "famili")
      .ilike("name", family);
    throwDatabaseError(taxaError, "Gagal memfilter famili.");

    const taxonIds = (familyTaxa ?? []).map((taxon) => taxon.id);
    if (taxonIds.length === 0) {
      return [];
    }

    const { data: links, error: linkError } = await supabase
      .from("species_taxa")
      .select("species_id")
      .in("taxon_id", taxonIds);
    throwDatabaseError(linkError, "Gagal memfilter spesies berdasarkan famili.");
    filtered = intersectIds(filtered, (links ?? []).map((link) => link.species_id));
  }

  if (regency !== "all") {
    const { data: locations, error: locationError } = await supabase
      .from("locations")
      .select("id")
      .eq("regency", regency);
    throwDatabaseError(locationError, "Gagal memfilter kabupaten/kota.");

    const locationIds = (locations ?? []).map((location) => location.id);
    if (locationIds.length === 0) {
      return [];
    }

    const { data: observations, error: observationError } = await supabase
      .from("plant_observations")
      .select("species_id")
      .in("location_id", locationIds);
    throwDatabaseError(observationError, "Gagal memfilter lokasi observasi.");
    filtered = intersectIds(filtered, (observations ?? []).map((item) => item.species_id));
  }

  return filtered;
}

async function getFilterOptions(supabase: Parameters<typeof decoratePlantCards>[0]) {
  const [taxaResult, locationResult] = await Promise.all([
    supabase.from("taxa").select("name").eq("rank", "famili").order("name"),
    supabase.from("locations").select("regency").order("regency"),
  ]);
  throwDatabaseError(taxaResult.error, "Gagal memuat daftar famili.");
  throwDatabaseError(locationResult.error, "Gagal memuat daftar wilayah.");

  return {
    families: [...new Set((taxaResult.data ?? []).map((item) => item.name))],
    regencies: [...new Set((locationResult.data ?? []).map((item) => item.regency))],
  };
}

export const plantsRouter = createTRPCRouter({
  selectOptions: managerProcedure.query(async ({ ctx }) => {
    const { data, error } = await ctx.supabase
      .from("plant_species")
      .select("id,local_name,scientific_name,code")
      .eq("status", "published")
      .order("local_name");
    throwDatabaseError(error, "Daftar pilihan tumbuhan gagal dimuat.");
    return data ?? [];
  }),

  catalog: publicProcedure.input(catalogInputSchema).query(async ({ ctx, input }) => {
    const filteredIds = await getFilteredSpeciesIds(ctx.supabase, input.family, input.regency);
    const optionsPromise = getFilterOptions(ctx.supabase);

    if (filteredIds?.length === 0) {
      return { items: [], total: 0, page: input.page, pageSize: input.pageSize, ...(await optionsPromise) };
    }

    const from = (input.page - 1) * input.pageSize;
    const to = from + input.pageSize - 1;
    let query = ctx.supabase
      .from("plant_species")
      .select("*", { count: "exact" })
      .eq("status", "published");

    const search = safeSearchValue(input.search);
    if (search) {
      query = query.or(`local_name.ilike.%${search}%,scientific_name.ilike.%${search}%,code.ilike.%${search}%`);
    }
    if (input.groupType !== "all") {
      query = query.eq("group_type", input.groupType);
    }
    if (input.cotyledonType !== "all") {
      query = query.eq("cotyledon_type", input.cotyledonType);
    }
    if (filteredIds) {
      query = query.in("id", filteredIds);
    }

    const [{ data, error, count }, options] = await Promise.all([
      query.order("local_name").range(from, to),
      optionsPromise,
    ]);
    throwDatabaseError(error, "Katalog tumbuhan gagal dimuat.");

    return {
      items: await decoratePlantCards(ctx.supabase, data ?? []),
      total: count ?? 0,
      page: input.page,
      pageSize: input.pageSize,
      ...options,
    };
  }),

  featured: publicProcedure.input(z.object({ limit: z.number().int().min(1).max(12).default(6) })).query(async ({ ctx, input }) => {
    const { data, error } = await ctx.supabase
      .from("plant_species")
      .select("*")
      .eq("status", "published")
      .order("updated_at", { ascending: false })
      .limit(input.limit);
    throwDatabaseError(error, "Spesimen unggulan gagal dimuat.");
    return decoratePlantCards(ctx.supabase, data ?? []);
  }),

  statistics: publicProcedure.query(async ({ ctx }) => {
    const { data, error } = await ctx.supabase.rpc("get_catalog_statistics").single();
    throwDatabaseError(error, "Statistik katalog gagal dimuat.");
    return {
      totalSpecies: Number(data?.total_species ?? 0),
      gymnospermae: Number(data?.gymnospermae ?? 0),
      angiospermae: Number(data?.angiospermae ?? 0),
    };
  }),

  bySlug: publicProcedure.input(z.object({ slug: z.string().min(1).max(255) })).query(async ({ ctx, input }) => {
    const { data: plant, error } = await ctx.supabase
      .from("plant_species")
      .select("*")
      .eq("slug", input.slug)
      .eq("status", "published")
      .maybeSingle();
    throwDatabaseError(error, "Detail tumbuhan gagal dimuat.");
    if (!plant) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Spesimen tumbuhan tidak ditemukan." });
    }

    const [morphologyResult, physiologyResult, linksResult, mediaResult, observationsResult, moduleLinksResult] = await Promise.all([
      ctx.supabase.from("morphologies").select("*").eq("species_id", plant.id).maybeSingle(),
      ctx.supabase.from("physiologies").select("*").eq("species_id", plant.id).maybeSingle(),
      ctx.supabase.from("species_taxa").select("taxon_id").eq("species_id", plant.id),
      ctx.supabase.from("media").select("*").eq("species_id", plant.id).order("is_primary", { ascending: false }),
      ctx.supabase.from("plant_observations").select("*").eq("species_id", plant.id).order("observation_date", { ascending: false }),
      ctx.supabase.from("module_species").select("module_id").eq("species_id", plant.id),
    ]);

    for (const result of [morphologyResult, physiologyResult, linksResult, mediaResult, observationsResult, moduleLinksResult]) {
      throwDatabaseError(result.error, "Relasi detail tumbuhan gagal dimuat.");
    }

    const taxonIds = (linksResult.data ?? []).map((item) => item.taxon_id);
    const locationIds = (observationsResult.data ?? []).map((item) => item.location_id);
    const moduleIds = (moduleLinksResult.data ?? []).map((item) => item.module_id);
    const [taxaResult, locationsResult, modulesResult] = await Promise.all([
      taxonIds.length ? ctx.supabase.from("taxa").select("*").in("id", taxonIds) : Promise.resolve({ data: [], error: null }),
      locationIds.length ? ctx.supabase.from("locations").select("*").in("id", locationIds) : Promise.resolve({ data: [], error: null }),
      moduleIds.length ? ctx.supabase.from("learning_modules").select("id,title,slug").in("id", moduleIds).eq("status", "published") : Promise.resolve({ data: [], error: null }),
    ]);
    throwDatabaseError(taxaResult.error, "Taksonomi gagal dimuat.");
    throwDatabaseError(locationsResult.error, "Lokasi gagal dimuat.");
    throwDatabaseError(modulesResult.error, "Modul terkait gagal dimuat.");

    const locations = new Map((locationsResult.data ?? []).map((item) => [item.id, item]));
    return {
      ...plant,
      imageUrl: (mediaResult.data ?? []).map((item) => ({ ...item, url: item.file_path ? ctx.supabase.storage.from(getPublicEnv().NEXT_PUBLIC_STORAGE_BUCKET).getPublicUrl(item.file_path).data.publicUrl : null })),
      morphology: morphologyResult.data,
      physiology: physiologyResult.data,
      taxonomy: (taxaResult.data ?? []).sort((left, right) => left.id - right.id),
      observations: (observationsResult.data ?? []).map((item) => ({ ...item, location: locations.get(item.location_id) ?? null })),
      modules: modulesResult.data ?? [],
    };
  }),

  manageList: managerProcedure.input(catalogInputSchema.pick({ search: true, groupType: true, page: true, pageSize: true })).query(async ({ ctx, input }) => {
    const from = (input.page - 1) * input.pageSize;
    let query = ctx.supabase.from("plant_species").select("*", { count: "exact" });
    const search = safeSearchValue(input.search);
    if (search) {
      query = query.or(`local_name.ilike.%${search}%,scientific_name.ilike.%${search}%,code.ilike.%${search}%`);
    }
    if (input.groupType !== "all") {
      query = query.eq("group_type", input.groupType);
    }
    const { data, error, count } = await query.order("created_at", { ascending: false }).range(from, from + input.pageSize - 1);
    throwDatabaseError(error, "Data pengelolaan tumbuhan gagal dimuat.");
    return { items: await decoratePlantCards(ctx.supabase, data ?? []), total: count ?? 0, page: input.page, pageSize: input.pageSize };
  }),

  manageSuggestions: managerProcedure
    .input(z.object({ search: z.string().trim().min(2).max(100) }))
    .query(async ({ ctx, input }) => {
      const search = safeSearchValue(input.search);
      if (!search) return [];
      const { data, error } = await ctx.supabase
        .from("plant_species")
        .select("id,local_name,scientific_name,code,group_type,status")
        .or(`local_name.ilike.%${search}%,scientific_name.ilike.%${search}%,code.ilike.%${search}%`)
        .order("local_name")
        .limit(6);
      throwDatabaseError(error, "Saran pencarian tumbuhan gagal dimuat.");
      return data ?? [];
    }),

  manageById: managerProcedure.input(z.object({ id: z.number().int().positive() })).query(async ({ ctx, input }) => {
    const { data: plant, error } = await ctx.supabase.from("plant_species").select("*").eq("id", input.id).maybeSingle();
    throwDatabaseError(error, "Spesimen gagal dimuat.");
    if (!plant) {
      throw new TRPCError({ code: "NOT_FOUND", message: "Spesimen tidak ditemukan." });
    }
    const [morphology, links] = await Promise.all([
      ctx.supabase.from("morphologies").select("*").eq("species_id", plant.id).maybeSingle(),
      ctx.supabase.from("species_taxa").select("taxon_id").eq("species_id", plant.id),
    ]);
    throwDatabaseError(morphology.error, "Morfologi gagal dimuat.");
    throwDatabaseError(links.error, "Taksonomi gagal dimuat.");
    const taxonIds = (links.data ?? []).map((item) => item.taxon_id);
    const taxa = taxonIds.length ? await ctx.supabase.from("taxa").select("*").in("id", taxonIds) : { data: [], error: null };
    throwDatabaseError(taxa.error, "Taksonomi gagal dimuat.");
    const taxonomy = Object.fromEntries((taxa.data ?? []).map((taxon) => [taxon.rank, taxon.name]));
    return { ...plant, morphology: morphology.data, taxonomy };
  }),

  createUploadUrl: managerProcedure.input(z.object({ fileName: z.string().min(1).max(255), contentType: z.enum(["image/jpeg", "image/png", "image/webp"]) })).mutation(async ({ ctx, input }) => {
    const extension = input.fileName.split(".").pop()?.toLowerCase();
    const safeExtension = extension && ["jpg", "jpeg", "png", "webp"].includes(extension) ? extension : "bin";
    const path = `plants/${crypto.randomUUID()}.${safeExtension}`;
    const bucket = getPublicEnv().NEXT_PUBLIC_STORAGE_BUCKET;
    const { data, error } = await ctx.supabase.storage.from(bucket).createSignedUploadUrl(path);
    if (error) {
      throw new TRPCError({ code: "INTERNAL_SERVER_ERROR", message: "URL upload gambar gagal dibuat." });
    }
    return { path, token: data.token, contentType: input.contentType };
  }),

  save: managerProcedure.input(plantInputSchema).mutation(async ({ ctx, input }) => {
    const slugSource = `${input.localName}-${input.code.slice(-4)}`;
    const slug = await createUniqueSlug(ctx.supabase, "plant_species", slugSource, input.id);
    const cotyledonType = input.groupType === "Gymnospermae" ? "Tidak Berlaku" : input.cotyledonType;
    const payload: Json = {
      code: input.code,
      slug,
      local_name: input.localName,
      scientific_name: input.scientificName,
      author_name: input.authorName,
      group_type: input.groupType,
      cotyledon_type: cotyledonType,
      description: input.description,
      habitat: input.habitat,
      benefits: input.benefits,
      image_path: input.imagePath,
      status: input.status,
    };
    const morphology: Json = {
      root: input.morphology.root,
      stem: input.morphology.stem,
      leaf: input.morphology.leaf,
      flower: input.morphology.flower,
      fruit: input.morphology.fruit,
      seed: input.morphology.seed,
      special_characteristics: input.morphology.specialCharacteristics,
    };
    const taxonomy: Json = {
      kingdom: input.taxonomy.kingdom,
      divisi: input.taxonomy.divisi,
      kelas: input.taxonomy.kelas,
      ordo: input.taxonomy.ordo,
      famili: input.taxonomy.famili,
      genus: input.taxonomy.genus,
      spesies: nullIfEmpty(input.taxonomy.spesies) ?? input.scientificName,
    };
    const { data, error } = await ctx.supabase.rpc("save_plant_species", {
      p_plant_id: input.id ?? null,
      p_payload: payload,
      p_morphology: morphology,
      p_taxonomy: taxonomy,
    });
    throwDatabaseError(error, "Spesimen gagal disimpan.");
    return { id: data, slug };
  }),

  delete: managerProcedure.input(z.object({ id: z.number().int().positive() })).mutation(async ({ ctx, input }) => {
    const { data: plant } = await ctx.supabase.from("plant_species").select("image_path").eq("id", input.id).maybeSingle();
    const { error } = await ctx.supabase.from("plant_species").delete().eq("id", input.id);
    throwDatabaseError(error, "Spesimen gagal dihapus.");
    if (plant?.image_path) {
      await ctx.supabase.storage.from(getPublicEnv().NEXT_PUBLIC_STORAGE_BUCKET).remove([plant.image_path]);
    }
    return { success: true };
  }),
});
