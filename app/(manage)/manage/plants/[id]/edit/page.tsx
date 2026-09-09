import { notFound } from "next/navigation";
import { connection } from "next/server";
import { Suspense } from "react";
import { PlantForm } from "@/components/manage/plant-form";
import { PageHeader } from "@/components/ui/page-header";
import { PageContentFallback } from "@/components/ui/runtime-fallbacks";
import { api } from "@/server/api/server";
import { requireRoles } from "@/server/auth";

type Props = { params: Promise<{ id: string }> };

export default function EditPlantPage({ params }: Props) {
  return (
    <Suspense fallback={<PageContentFallback label="Memuat data tumbuhan" maxWidth="5xl" />}>
      <EditPlantContent params={params} />
    </Suspense>
  );
}

async function EditPlantContent({ params }: Props) {
  await connection();
  await requireRoles(["admin", "dosen"]);
  const id = Number((await params).id);
  if (!Number.isInteger(id)) notFound();
  const plant = await (await api()).plants.manageById({ id });
  const morphology = plant.morphology;

  return (
    <div className="mx-auto max-w-5xl">
      <PageHeader
        eyebrow={plant.code}
        title={`Edit ${plant.local_name}`}
        description="Perubahan langsung menggunakan kebijakan akses Supabase untuk admin dan dosen."
      />
      <div className="mt-7">
        <PlantForm
          mode="edit"
          initial={{
            id: plant.id,
            code: plant.code,
            localName: plant.local_name,
            scientificName: plant.scientific_name,
            authorName: plant.author_name ?? "",
            groupType: plant.group_type,
            cotyledonType: plant.cotyledon_type,
            description: plant.description ?? "",
            habitat: plant.habitat ?? "",
            benefits: plant.benefits ?? "",
            imagePath: plant.image_path,
            status: plant.status,
            morphology: {
              root: morphology?.root ?? "",
              stem: morphology?.stem ?? "",
              leaf: morphology?.leaf ?? "",
              flower: morphology?.flower ?? "",
              fruit: morphology?.fruit ?? "",
              seed: morphology?.seed ?? "",
              specialCharacteristics: morphology?.special_characteristics ?? "",
            },
            taxonomy: {
              kingdom: plant.taxonomy.kingdom ?? "Plantae",
              divisi: plant.taxonomy.divisi ?? "",
              kelas: plant.taxonomy.kelas ?? "",
              ordo: plant.taxonomy.ordo ?? "",
              famili: plant.taxonomy.famili ?? "",
              genus: plant.taxonomy.genus ?? "",
              spesies: plant.taxonomy.spesies ?? plant.scientific_name,
            },
          }}
        />
      </div>
    </div>
  );
}
