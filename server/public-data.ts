import "server-only";
import { cacheLife, cacheTag } from "next/cache";
import { PUBLIC_DATA_CACHE_TAG } from "@/server/cache-tags";
import { publicApi } from "@/server/api/server";

type CatalogInput = {
  search: string;
  groupType: "all" | "Gymnospermae" | "Angiospermae";
  cotyledonType: "all" | "Monokotil" | "Dikotil" | "Tidak Berlaku";
  family: string;
  regency: string;
  page: number;
  pageSize: number;
};

function configurePublicDataCache(): void {
  cacheLife({ stale: 300, revalidate: 3600, expire: 86400 });
  cacheTag(PUBLIC_DATA_CACHE_TAG);
}

export async function getHomePageData() {
  "use cache";
  configurePublicDataCache();
  const caller = await publicApi();
  const [featured, stats, modules] = await Promise.all([
    caller.plants.featured({ limit: 6 }),
    caller.plants.statistics(),
    caller.modules.listPublished(),
  ]);

  return { featured, stats, modules };
}

export async function getPublishedModules() {
  "use cache";
  configurePublicDataCache();
  return (await publicApi()).modules.listPublished();
}

export async function getPlantCatalog(input: CatalogInput) {
  "use cache";
  configurePublicDataCache();
  return (await publicApi()).plants.catalog(input);
}

export async function getPlantBySlug(slug: string) {
  "use cache";
  configurePublicDataCache();
  return (await publicApi()).plants.bySlug({ slug });
}

export async function getModuleBySlug(slug: string, lessonSlug?: string) {
  "use cache";
  configurePublicDataCache();
  return (await publicApi()).modules.bySlug({ slug, lessonSlug });
}
