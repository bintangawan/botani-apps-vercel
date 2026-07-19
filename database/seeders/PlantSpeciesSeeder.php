<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Media;
use App\Models\Morphology;
use App\Models\Physiology;
use App\Models\PlantObservation;
use App\Models\PlantSpecies;
use App\Models\Taxa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PlantSpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        
        $jsonFiles = [
            'dataset_botani_observasi.json',
            'dataset_botani2_observasi.json',
            'dataset_botani3_observasi.json',
            'dataset_botani4_observasi.json',
            'dataset_botani5_observasi.json',
        ];

        foreach ($jsonFiles as $file) {
            $filePath = base_path('json-data/' . $file);
            if (!File::exists($filePath)) {
                continue;
            }

            $jsonData = json_decode(File::get($filePath), true);
            if (!is_array($jsonData)) {
                continue;
            }

            foreach ($jsonData as $item) {
                // Determine group_type and cotyledon_type
                $taksonomi = $item['taksonomi'] ?? [];
                $divisi = $taksonomi['divisi'] ?? '';
                $kelas = $taksonomi['kelas'] ?? '';
                $famili = $taksonomi['famili'] ?? '';

                $groupType = 'Angiospermae';
                $cotyledonType = 'Monokotil';

                if (in_array(strtolower($divisi), ['pinophyta', 'cycadophyta', 'gnetophyta', 'coniferophyta', 'ginkgophyta']) ||
                    in_array(strtolower($kelas), ['pinopsida', 'cycadopsida', 'gnetopsida', 'gymnospermae'])) {
                    $groupType = 'Gymnospermae';
                    $cotyledonType = 'Tidak Berlaku';
                } else {
                    $groupType = 'Angiospermae';
                    if (in_array(strtolower($kelas), ['liliopsida', 'monocotyledoneae'])) {
                        $cotyledonType = 'Monokotil';
                    } elseif (in_array(strtolower($kelas), ['magnoliopsida', 'dicotyledoneae', 'rosopsida'])) {
                        $cotyledonType = 'Dikotil';
                    } else {
                        // Fallback check based on common families
                        $monoFamilies = ['arecaceae', 'poaceae', 'musaceae', 'zingiberaceae', 'orchidaceae', 'araceae', 'cyperaceae', 'pontederiaceae', 'bromeliaceae'];
                        if (in_array(strtolower($famili), $monoFamilies)) {
                            $cotyledonType = 'Monokotil';
                        } else {
                            $cotyledonType = 'Dikotil';
                        }
                    }
                }

                $localName = $item['nama'] ?? 'Tanaman Tidak Dikenal';
                $scientificName = $item['nama_ilmiah'] ?? 'Species incertae';
                $code = $item['id'] ?? ('flora_' . uniqid());
                $slug = Str::slug($localName . '-' . $code);

                // Generate Rich Academic Placeholder Theory
                $theoryData = $this->generateTheoryData($localName, $scientificName, $groupType, $cotyledonType, $famili);

                // Create PlantSpecies
                $species = PlantSpecies::firstOrCreate(
                    ['code' => $code],
                    [
                        'slug' => $slug,
                        'local_name' => $localName,
                        'scientific_name' => $scientificName,
                        'author_name' => 'L.',
                        'group_type' => $groupType,
                        'cotyledon_type' => $cotyledonType,
                        'description' => $theoryData['description'],
                        'habitat' => $theoryData['habitat'],
                        'benefits' => $theoryData['benefits'],
                        'image_path' => $item['image']['link'] ?? null,
                        'status' => 'published',
                    ]
                );

                // Create Morphology & Physiology
                Morphology::firstOrCreate(
                    ['species_id' => $species->id],
                    $theoryData['morphology']
                );

                Physiology::firstOrCreate(
                    ['species_id' => $species->id],
                    $theoryData['physiology']
                );

                // Process Taxonomy Hierarchy
                $ranks = ['kingdom', 'divisi', 'kelas', 'ordo', 'famili', 'genus', 'spesies'];
                $parentTaxon = null;

                foreach ($ranks as $rank) {
                    if (!empty($taksonomi[$rank])) {
                        $taxonName = $taksonomi[$rank];
                        $taxon = Taxa::firstOrCreate(
                            ['name' => $taxonName, 'rank' => $rank],
                            ['parent_id' => $parentTaxon ? $parentTaxon->id : null]
                        );

                        // Attach to species pivot if not attached
                        if (!$species->taxa()->where('taxon_id', $taxon->id)->exists()) {
                            $species->taxa()->attach($taxon->id);
                        }

                        $parentTaxon = $taxon;
                    }
                }

                // Process Location & Observation
                $lokasiData = $item['lokasi'] ?? [];
                $district = $lokasiData['kecamatan'] ?? 'Medan';
                $regency = $lokasiData['kabupaten'] ?? ($lokasiData['kota'] ?? 'Kota Medan');
                $village = $lokasiData['tempat'] ?? ($lokasiData['desa'] ?? null);

                $location = Location::firstOrCreate(
                    ['district' => $district, 'regency' => $regency],
                    [
                        'location_name' => $village ? "$village, $district" : "$district, $regency",
                        'village' => $village,
                        'province' => $lokasiData['provinsi'] ?? 'Sumatera Utara',
                    ]
                );

                $observation = PlantObservation::create([
                    'species_id' => $species->id,
                    'location_id' => $location->id,
                    'observer_id' => $admin ? $admin->id : null,
                    'observation_date' => now()->subDays(rand(1, 180)),
                    'notes' => "Spesimen {$localName} ({$scientificName}) diamati dalam kondisi sehat di habitat tropis {$location->location_name}, Sumatera Utara.",
                ]);

                // Process Media
                if (!empty($item['image'])) {
                    Media::create([
                        'species_id' => $species->id,
                        'observation_id' => $observation->id,
                        'filename' => $item['image']['file_name'] ?? 'specimen.png',
                        'file_path' => $item['image']['link'] ?? '/storage/images/specimen.png',
                        'media_type' => 'image',
                        'caption' => "Dokumentasi herbarium digital untuk {$localName} ({$scientificName})",
                        'is_primary' => true,
                    ]);
                }
            }
        }
    }

    /**
     * Generate structured academic placeholder descriptions based on classification.
     */
    private function generateTheoryData(string $localName, string $scientificName, string $groupType, string $cotyledonType, string $family): array
    {
        if ($groupType === 'Gymnospermae') {
            return [
                'description' => "Spesies {$localName} ({$scientificName}) merupakan tumbuhan berbiji terbuka (Gymnospermae) yang termasuk dalam famili {$family}. Ciri khas dari kelompok ini adalah bijinya yang tidak terbungkus oleh bakal buah (ovarium), melainkan tersusun dalam struktur kerucut atau strobilus. Spesimen ini menunjukkan adaptasi fisiologis yang kuat terhadap kondisi iklim tropis maupun subtropis, serta memiliki sistem ikatan pembuluh sekunder yang berkembang sempurna.",
                'habitat' => "Umumnya dijumpai pada wilayah hutan dataran tinggi, kawasan pegunungan, atau sebagai tanaman peneduh dan konservasi tanah di Sumatera Utara.",
                'benefits' => "Memiliki nilai ekologis sebagai penahan erosi dan penghasil oksigen, serta nilai ekonomis sebagai bahan industri kayu, resin, maupun pertamanan estetis.",
                'morphology' => [
                    'root' => "Sistem perakaran tunggang (radix primaria) yang kuat, menghujam dalam ke tanah untuk penyerapan nutrisi optimal dan stabilitas mekanis.",
                    'stem' => "Batang berkayu keras (lignosus) dengan percabangan simpodial atau monopodial, memperlihatkan pertumbuhan sekunder akibat aktivitas kambium vaskuler.",
                    'leaf' => "Daun beradaptasi khusus berupa jarum (acerosa), sisik, atau helaian tebal bermeristem dengan lapisan kutikula tebal untuk mereduksi transpirasi.",
                    'flower' => "Tidak memiliki bunga sejati; organ reproduksi tersusun dalam strobilus jantan (mikrosporangiat) dan strobilus betina (megasporangiat).",
                    'fruit' => "Tidak menghasilkan buah sejati berstruktur ovarium tertutup.",
                    'seed' => "Biji terbuka (naked seed) yang terletak langsung pada sisik megasporofil, seringkali dilengkapi sayap tipis untuk anemokori (penyebaran dibantu angin).",
                    'special_characteristics' => "Keberadaan saluran resin atau getah khas pada jaringan anatomi batang dan daun.",
                ],
                'physiology' => [
                    'reproduction' => "Reproduksi seksual terjadi melalui penyerbukan anemogami (bantuan angin), di mana serbuk sari langsung membuahi sel telur pada mikrofil.",
                    'growth' => "Menunjukkan pertumbuhan kambium sekunder yang menghasilkan lingkaran tahun dan xilem dengan trakeida sebagai unsur pengangkut utama.",
                    'adaptation' => "Toleran terhadap radiasi matahari tinggi serta variasi kelembaban lingkungan tropis.",
                    'additional_information' => "Materi teori ini disusun berdasarkan observasi herbarium digital Botani Phanerogamae dan dapat dikustomisasi lebih lanjut oleh Dosen.",
                ]
            ];
        } elseif ($cotyledonType === 'Monokotil') {
            return [
                'description' => "Spesies {$localName} ({$scientificName}) adalah anggota tumbuhan berbiji tertutup (Angiospermae) dari kelas Monokotil (Liliopsida), famili {$family}. Tumbuhan monokotil dicirikan oleh embrio yang memiliki satu daun lembaga (kotiledon), bagian bunga kelipatan tiga (trimerous), serta sistem jaringan pembuluh yang tersebar (kolateral tertutup) pada anatomi batangnya. {$localName} memiliki peran penting baik dalam ekosistem maupun dalam kehidupan masyarakat tropis Sumatera Utara.",
                'habitat' => "Tumbuh subur di daratan rendah hingga dataran menengah beriklim tropis basah, tanah gembur berhumus, hingga tepian perairan sesuai preferensi famili {$family}.",
                'benefits' => "Sangat bernilai sebagai pangan pokok, buah konsumsi, bahan baku industri kerajinan, minyak nabati, serta obat herbal alami.",
                'morphology' => [
                    'root' => "Sistem perakaran serabut (radix adventicia) yang berkembang ekstensif di lapisan atas tanah untuk penyerapan air dan hara secara cepat.",
                    'stem' => "Batang umumnya tidak bercabang atau bercabang terbatas, memiliki ruas-ruas nyata (nodicum) dengan ikatan pembuluh tersebar tanpa kambium.",
                    'leaf' => "Daun tunggal dengan susunan tulang daun sejajar (rectinervis) atau melengkung (curvinervis), berpelepah memeluk batang.",
                    'flower' => "Bunga bertipe trimerous (kelipatan 3), perhiasan bunga sering berupa tenda bunga (perigonium) yang sukar dibedakan kelopak dan mahkotanya.",
                    'fruit' => "Buah bertipe bervariasi mulai dari buah buni (bacca), batu (drupa), hingga kapsul ganda yang melindungi biji di dalamnya.",
                    'seed' => "Biji berkotiledon satu dengan endosperm sebagai jaringan cadangan makanan utama bagi perkembangan embrio.",
                    'special_characteristics' => "Akar serabut tebal dan formasi daun berpelepah yang khas pada famili {$family}.",
                ],
                'physiology' => [
                    'reproduction' => "Penyerbukan dapat terjadi via entomogami (serangga), anemogami (angin), maupun perbanyakan vegetatif melalui rimpang, tunas adventif, atau stolon.",
                    'growth' => "Pertumbuhan terutama ditekankan pada pertumbuhan primer vertikal (meristem apikal) tanpa penambahan diameter batang sekunder yang ekstensif.",
                    'adaptation' => "Memiliki sistem stomata yang efisien mengatur transpirasi serta toleransi tinggi terhadap fluktuasi air tanah.",
                    'additional_information' => "Materi teori ini disusun berdasarkan observasi herbarium digital Botani Phanerogamae dan dapat dikustomisasi lebih lanjut oleh Dosen.",
                ]
            ];
        } else {
            // Dikotil (Default Angiospermae)
            return [
                'description' => "Spesies {$localName} ({$scientificName}) merupakan tumbuhan berbiji tertutup (Angiospermae) yang tergolong dalam kelas Dikotil (Magnoliopsida), famili {$family}. Kelompok dikotil ditandai dengan kehadiran dua daun lembaga (kotiledon) pada embrionya, ikatan pembuluh yang tersusun melingkar (kolateral terbuka) disertai kambium aktif, serta perhiasan bunga kelipatan empat atau lima (tetramerous/pentamerous). {$localName} adalah komponen hayati berharga dalam keanekaragaman botani Sumatera Utara.",
                'habitat' => "Ditemukan secara luas di hutan hujan tropis, kebun rakyat, serta perkebunan dataran rendah hingga ketinggian menengah di Sumatera Utara.",
                'benefits' => "Menghasilkan buah konsumsi bermutu tinggi, bahan kayu pertukangan kuat, senyawa bioaktif untuk farmakologi herbal, dan penyeimbang mikroklimat lingkungan.",
                'morphology' => [
                    'root' => "Sistem perakaran tunggang (radix primaria) kokoh dengan banyak cabang lateral (radix lateralis) yang menembus dalam ke substruktur tanah.",
                    'stem' => "Batang berkayu bercabang banyak dengan bentuk tajuk membulat atau simetris, berstruktur kambium aktif yang menghasilkan pertumbuhan sekunder diameter.",
                    'leaf' => "Daun bertulang menyirip (penninervis) atau menjari (palmatinervis), dengan tangkai daun yang jelas dan helaian berpori stomata tipe dikotil.",
                    'flower' => "Bunga berbilangan kelipatan 4 atau 5 (pentamerous), dengan diferensiasi kelopak (calyx) dan mahkota (corolla) yang sangat jelas serta estetik.",
                    'fruit' => "Buah sejati berdaging atau kering berstruktur kompleks (eksokarp, mesokarp, endokarp) yang melindungi biji dengan sempurna.",
                    'seed' => "Biji berkotiledon dua yang menyimpan cukup cadangan makanan perkecambahan di dalam daun lembanganya.",
                    'special_characteristics' => "Kehadiran kambium pembuluh dan struktur percabangan tajuk ekstensif yang memperkuat daya hidup tumbuhan.",
                ],
                'physiology' => [
                    'reproduction' => "Penyerbukan dominan melalui entomogami (dibantu lebah/serangga) atau zoidiogami, menghasilkan pembuahan ganda khas Angiospermae.",
                    'growth' => "Pertumbuhan sekunder kambium vaskuler memungkinkan diferensiasi xilem dan floem baru secara periodik sepanjang masa hidup pohon.",
                    'adaptation' => "Sistem fotosintesis C3 atau C4 yang adaptif terhadap paparan matahari tropis, serta kemampuan penyesuaian osmotik saat kemarau.",
                    'additional_information' => "Materi teori ini disusun berdasarkan observasi herbarium digital Botani Phanerogamae dan dapat dikustomisasi lebih lanjut oleh Dosen.",
                ]
            ];
        }
    }
}
