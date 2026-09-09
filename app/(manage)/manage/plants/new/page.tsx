import { PlantForm } from "@/components/manage/plant-form";
import { PageHeader } from "@/components/ui/page-header";

export default function NewPlantPage() {
  return (
    <div className="mx-auto max-w-5xl">
      <PageHeader
        eyebrow="Spesimen baru"
        title="Tambah Tumbuhan"
        description="Isi identitas ilmiah, uraian morfologi, dan klasifikasi taksonomi."
      />
      <div className="mt-7">
        <PlantForm
          mode="create"
          initial={{
            code: "",
            localName: "",
            scientificName: "",
            authorName: "",
            groupType: "Angiospermae",
            cotyledonType: "Dikotil",
            description: "",
            habitat: "",
            benefits: "",
            imagePath: null,
            status: "published",
            morphology: {
              root: "",
              stem: "",
              leaf: "",
              flower: "",
              fruit: "",
              seed: "",
              specialCharacteristics: "",
            },
            taxonomy: {
              kingdom: "Plantae",
              divisi: "",
              kelas: "",
              ordo: "",
              famili: "",
              genus: "",
              spesies: "",
            },
          }}
        />
      </div>
    </div>
  );
}
