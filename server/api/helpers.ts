import { TRPCError } from "@trpc/server";
import type { SupabaseClient } from "@supabase/supabase-js";
import { getStoragePublicUrl } from "@/lib/supabase/storage";
import { slugify } from "@/lib/utils";
import type { Database, Tables } from "@/types/database";

type ErrorLike = { message: string; code?: string };

export function throwDatabaseError(error: ErrorLike | null, fallback: string): void {
  if (!error) {
    return;
  }

  if (error.code === "23505") {
    throw new TRPCError({ code: "CONFLICT", message: "Data dengan nilai unik tersebut sudah tersedia." });
  }

  throw new TRPCError({ code: "INTERNAL_SERVER_ERROR", message: fallback, cause: error });
}

export async function createUniqueSlug(
  supabase: SupabaseClient<Database>,
  table: "plant_species" | "learning_modules",
  source: string,
  ignoreId?: number,
): Promise<string> {
  const base = slugify(source) || (table === "plant_species" ? "spesimen" : "bab-pembelajaran");
  let candidate = base;
  let suffix = 2;

  while (true) {
    let query = supabase.from(table).select("id").eq("slug", candidate);
    if (ignoreId !== undefined) {
      query = query.neq("id", ignoreId);
    }

    const { data, error } = await query.maybeSingle();
    throwDatabaseError(error, "Gagal memeriksa slug.");
    if (!data) {
      return candidate;
    }

    candidate = `${base}-${suffix}`;
    suffix += 1;
  }
}

export type PlantCardData = Tables<"plant_species"> & {
  family: string | null;
  imageUrl: string | null;
  regency: string | null;
};

export async function decoratePlantCards(
  supabase: SupabaseClient<Database>,
  plants: Tables<"plant_species">[],
): Promise<PlantCardData[]> {
  const speciesIds = plants.map((plant) => plant.id);
  if (speciesIds.length === 0) {
    return [];
  }

  const [linksResult, mediaResult, observationResult] = await Promise.all([
    supabase.from("species_taxa").select("species_id,taxon_id").in("species_id", speciesIds),
    supabase.from("media").select("species_id,file_path,is_primary").in("species_id", speciesIds),
    supabase.from("plant_observations").select("species_id,location_id").in("species_id", speciesIds),
  ]);

  throwDatabaseError(linksResult.error, "Gagal memuat taksonomi tumbuhan.");
  throwDatabaseError(mediaResult.error, "Gagal memuat media tumbuhan.");
  throwDatabaseError(observationResult.error, "Gagal memuat lokasi observasi.");

  const taxonIds = [...new Set((linksResult.data ?? []).map((link) => link.taxon_id))];
  const locationIds = [...new Set((observationResult.data ?? []).map((observation) => observation.location_id))];

  const [taxaResult, locationsResult] = await Promise.all([
    taxonIds.length > 0
      ? supabase.from("taxa").select("id,name,rank").in("id", taxonIds)
      : Promise.resolve({ data: [], error: null }),
    locationIds.length > 0
      ? supabase.from("locations").select("id,regency").in("id", locationIds)
      : Promise.resolve({ data: [], error: null }),
  ]);

  throwDatabaseError(taxaResult.error, "Gagal memuat nama takson.");
  throwDatabaseError(locationsResult.error, "Gagal memuat nama wilayah.");

  const taxaById = new Map((taxaResult.data ?? []).map((taxon) => [taxon.id, taxon]));
  const locationsById = new Map((locationsResult.data ?? []).map((location) => [location.id, location]));

  return plants.map((plant) => {
    const familyLink = (linksResult.data ?? []).find((link) => {
      const taxon = taxaById.get(link.taxon_id);
      return link.species_id === plant.id && taxon?.rank === "famili";
    });
    const media = (mediaResult.data ?? []).find(
      (item) => item.species_id === plant.id && item.is_primary,
    ) ?? (mediaResult.data ?? []).find((item) => item.species_id === plant.id);
    const observation = (observationResult.data ?? []).find(
      (item) => item.species_id === plant.id,
    );

    return {
      ...plant,
      family: familyLink ? (taxaById.get(familyLink.taxon_id)?.name ?? null) : null,
      imageUrl: getStoragePublicUrl(supabase, media?.file_path ?? plant.image_path),
      regency: observation ? (locationsById.get(observation.location_id)?.regency ?? null) : null,
    };
  });
}
