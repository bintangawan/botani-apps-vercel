<?php

namespace Database\Seeders;

use App\Models\LearningModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LearningModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'title' => 'Pengenalan Botani Phanerogamae (Tumbuhan Berbiji)',
                'slug' => 'pengenalan-botani-phanerogamae',
                'description' => 'Konsep dasar, klasifikasi utama, dan signifikansi evolusioner dari tumbuhan berbiji (Spermatophyta/Phanerogamae) dalam ekosistem terrestrial tropis.',
                'module_order' => 1,
                'content' => "<h3>1. Definisi dan Karakteristik Phanerogamae</h3>
<p><strong>Phanerogamae</strong> atau yang lebih dikenal sebagai <em>Spermatophyta</em> (tumbuhan berbiji) merupakan kelompok tumbuhan tingkat tinggi yang organ reproduksinya tampak jelas (<em>phaneros</em> = jelas/tampak, <em>gamos</em> = perkawinan/reproduksi). Tumbuhan berbiji memegang peranan paling dominan dalam keanekaragaman flora daratan di bumi modern, khususnya di Sumatera Utara.</p>
<h4>Ciri Utama Tumbuhan Berbiji:</h4>
<ul>
  <li>Menghasilkan <strong>biji (semen)</strong> sebagai alat perkembangbiakan generatif utama yang mengandung embrio, cadangan makanan, dan selubung pelindung.</li>
  <li>Memiliki berkas pengangkut sejati yang terdiri dari <strong>xilem</strong> (pembuluh kayu untuk transpor air/hara) dan <strong>floem</strong> (pembuluh tapis untuk transpor hasil fotosintesis).</li>
  <li>Diferensiasi organ vegetatif sejati berupa akar, batang, dan daun yang jelas.</li>
  <li>Mengalami generasi heterospora dan pembuahan melalui buluh serbuk sari (siphonogamy).</li>
</ul>
<hr>
<h3>2. Klasifikasi Utama Phanerogamae</h3>
<p>Secara taksonomi filogenetik modern, Phanerogamae dibagi menjadi dua superkelompok besar berdasarkan letak bakal bijinya terhadap bakal buah (ovarium):</p>
<div class=\"grid grid-cols-1 md:grid-cols-2 gap-4 my-6\">
  <div class=\"p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/20\">
    <h4 class=\"text-emerald-400 font-semibold mb-2\">🌿 Gymnospermae (Biji Terbuka)</h4>
    <p class=\"text-sm text-slate-300\">Bakal biji tidak dilindungi oleh daun buah (karpela) sehingga langsung terpapar pada sisik strobilus. Contoh: Pinus, Pakis Haji, Melinjo, Cemara.</p>
  </div>
  <div class=\"p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/20\">
    <h4 class=\"text-emerald-400 font-semibold mb-2\">🌸 Angiospermae (Biji Tertutup)</h4>
    <p class=\"text-sm text-slate-300\">Bakal biji terlindung sempurna di dalam bakal buah (ovarium) yang kelak berkembang menjadi buah sejati. Dibagi menjadi kelas Monokotil & Dikotil.</p>
  </div>
</div>",
            ],
            [
                'title' => 'Gymnospermae: Morfologi, Anatomi & Klasifikasi',
                'slug' => 'gymnospermae-morfologi-dan-klasifikasi',
                'description' => 'Materi mendalam mengenai struktur strobilus, anatomi jarum pinus, serta empat kelas utama tumbuhan berbiji terbuka.',
                'module_order' => 2,
                'content' => "<h3>1. Karakteristik Umum Gymnospermae</h3>
<p><strong>Gymnospermae</strong> (dari bahasa Yunani: <em>gymnos</em> = telanjang/terbuka, <em>sperma</em> = biji) adalah tumbuhan berkayu dengan bentuk habitus berupa pohon atau perdu. Kelompok ini memiliki sejarah evolusi yang sangat tua semenjak era Paleozoikum.</p>
<h4>Struktur Anatomi & Morfologi:</h4>
<ul>
  <li><strong>Batang:</strong> Berkambium kuat dengan pertumbuhan sekunder menghasilkan kayu berlingkaran tahun. Xilem didominasi oleh trakeida dengan noktah berhalaman.</li>
  <li><strong>Daun:</strong> Adaptasi xerofitik berupa bentuk jarum (pada conifer) atau helaian menyerupai palem (pada cycads) dengan kutikula tebal dan stomata terbenam (<em>sunken stomata</em>).</li>
  <li><strong>Alat Reproduksi:</strong> Berupa strobilus (runjung/kerucut). Strobilus jantan menghasilkan mikrospora (pollen), sedangkan strobilus betina menghasilkan megaspora (sel telur).</li>
</ul>
<hr>
<h3>2. Klasifikasi Empat Kelas Gymnospermae</h3>
<p>Dalam kurikulum botani Phanerogamae, Gymnospermae dikelompokkan menjadi 4 kelas utama:</p>
<ol>
  <li><strong>Cycadopsida (Pakis Haji):</strong> Habitus menyerupai kelapa/palem berdaun menyirip tunggal. Contoh: <em>Cycas rumphii</em>.</li>
  <li><strong>Coniferopsida / Pinopsida (Konifer):</strong> Daun berjarum, selalu hijau (<em>evergreen</em>), penghasil resin ekstensif. Contoh: <em>Pinus merkusii</em> (Pinus Sumatera), <em>Araucaria heterophylla</em>.</li>
  <li><strong>Gnetopsida:</strong> Tumbuhan perantara yang memiliki pembuluh trakea sejati pada xilemnya. Contoh: <em>Gnetum gnemon</em> (Melinjo).</li>
  <li><strong>Ginkgoopsida:</strong> Tumbuhan fosil hidup berdaun bentuk kipas dengan pertulangan menggarpu. Contoh: <em>Ginkgo biloba</em>.</li>
</ol>",
            ],
            [
                'title' => 'Angiospermae: Kelas Monokotil (Liliopsida)',
                'slug' => 'angiospermae-kelas-monokotil',
                'description' => 'Mempelajari karakteristik anatomi berikatan kolateral tertutup, akar serabut, perhiasan bunga trimerous, dan famili penting monokotil tropis.',
                'module_order' => 3,
                'content' => "<h3>1. Ciri Utama Monokotil (Liliopsida)</h3>
<p><strong>Monokotiledon</strong> atau <em>Liliopsida</em> mewakili sepertiga dari total keanekaragaman Angiospermae dunia. Kelompok ini memiliki adaptasi fisiologis yang luar biasa pada berbagai ekosistem, dari lahan basah hingga hutan kanopi tropis.</p>
<h4>Sifat Diagnostik Monokotil:</h4>
<ul>
  <li><strong>Kotiledon:</strong> Memiliki tepat 1 daun lembaga pada embrio bijinya.</li>
  <li><strong>Perakaran:</strong> Sistem akar serabut (radix adventicia) yang tumbuh dari pangkal batang.</li>
  <li><strong>Pertulangan Daun:</strong> Sejajar (rectinervis) atau melengkung (curvinervis), dengan pelepah yang memeluk batang.</li>
  <li><strong>Anatomi Batang:</strong> Ikatan pembuluh bertipe kolateral tertutup (tersebar merata/<em>scattered</em>) tanpa lapisan kambium vaskuler.</li>
  <li><strong>Bunga:</strong> Bagian perhiasan bunga berkelipatan 3 (trimerous).</li>
</ul>
<hr>
<h3>2. Famili Ekonomis Penting Monokotil di Sumatera Utara</h3>
<p>Dalam pengamatan lapangan di Sumatera Utara, famili berikut mendominasi dan bernilai tinggi:</p>
<ul>
  <li><strong>Arecaceae (Palem-paleman):</strong> Salak (<em>Salacca zalacca</em>), Kelapa Sawit (<em>Elaeis guineensis</em>), Kelapa (<em>Cocos nucifera</em>).</li>
  <li><strong>Poaceae (Rumput-rumputan):</strong> Bambu, Jagung, Padi, Tebu.</li>
  <li><strong>Zingiberaceae (Jahe-jahean):</strong> Rimpang aromatik seperti Lengkuas, Kunyit, Jahe.</li>
  <li><strong>Araceae (Talas-talasan):</strong> Keladi, Eceng Gondok, Monstera.</li>
</ul>",
            ],
            [
                'title' => 'Angiospermae: Kelas Dikotil (Magnoliopsida)',
                'slug' => 'angiospermae-kelas-dikotil',
                'description' => 'Eksplorasi struktur pembuluh melingkar dengan kambium aktif, sistem akar tunggang, serta keragaman famili tanaman buah tropis Sumatera.',
                'module_order' => 4,
                'content' => "<h3>1. Karakteristik Dikotil (Magnoliopsida)</h3>
<p><strong>Dikotiledon</strong> atau <em>Magnoliopsida</em> adalah kelompok tumbuhan berbiji tertutup terbesar yang membentuk sebagian besar struktur pohon kanopi hutan hujan tropis dan tanaman kebun buah di Indonesia.</p>
<h4>Ciri Anatomi & Morfologi Utama:</h4>
<ul>
  <li><strong>Embrio:</strong> Memiliki 2 daun lembaga (kotiledon) yang tebal sebagai cadangan makanan perkecambahan.</li>
  <li><strong>Sistem Akar:</strong> Akar tunggang (radix primaria) yang kokoh dan memiliki banyak cabang lateral.</li>
  <li><strong>Tulang Daun:</strong> Menyirip (penninervis) atau menjari (palmatinervis) membentuk jaringan retikulat.</li>
  <li><strong>Batang & Kambium:</strong> Berikatan pembuluh kolateral terbuka (tersusun teratur dalam lingkaran melingkari empulur), dengan kambium yang aktif membelah membentuk kayu sekunder.</li>
  <li><strong>Bunga:</strong> Bagian kelopak dan mahkota berbilangan kelipatan 4 atau 5 (tetramerous/pentamerous).</li>
</ul>
<hr>
<h3>2. Famili Unggulan Tanaman Buah Dikotil</h3>
<p>Katalog observasi kita mencatat berbagai tanaman buah tropis populer dari kelas ini:</p>
<ul>
  <li><strong>Malvaceae / Bombacaceae:</strong> Durian (<em>Durio zibethinus</em>), Kapuk Randu (<em>Ceiba pentandra</em>).</li>
  <li><strong>Sapindaceae:</strong> Rambutan (<em>Nephelium lappaceum</em>), Kelengkeng (<em>Dimocarpus longan</em>).</li>
  <li><strong>Anacardiaceae:</strong> Mangga (<em>Mangifera indica</em>).</li>
  <li><strong>Moraceae:</strong> Nangka (<em>Artocarpus heterophyllus</em>), Beringin.</li>
  <li><strong>Clusiaceae:</strong> Manggis (<em>Garcinia mangostana</em>).</li>
</ul>",
            ],
            [
                'title' => 'Morfologi & Fisiologi Adaptasi Tumbuhan Berbiji',
                'slug' => 'morfologi-dan-fisiologi-adaptasi',
                'description' => 'Pembahasan sintesis organ organ vegetatif, reproduksi generatif, pembuahan ganda, serta strategi adaptasi ekofisiologis tumbuhan terhadap lingkungan tropis.',
                'module_order' => 5,
                'content' => "<h3>1. Sintesis Organologi Vegetatif</h3>
<p>Keberhasilan evolusi tumbuhan berbiji sangat bergantung pada kemampuannya mengintegrasikan fungsi ketiga organ pokok:</p>
<ul>
  <li><strong>Akar (Radix):</strong> Penyerapan air, osmosis hara mineral, serta penopang mekanis pohon di berbagai topografi tanah tropis.</li>
  <li><strong>Batang (Caulis):</strong> Distribusi hidrolik air melalui xilem trakea/trakeida menuju tajuk daun, dan pengangkutan asimilat floem ke organ sink (buah/biji).</li>
  <li><strong>Daun (Folium):</strong> Pabrik fotosintesis utama yang dilengkapi dengan jaringan mesofil palisade (tiang) dan spons (bunga karang) untuk efisiensi penangkapan foton matahari.</li>
</ul>
<hr>
<h3>2. Fisiologi Pembuahan Ganda Angiospermae</h3>
<p>Salah satu keajaiban fisiologis Angiospermae yang membedakannya dari Gymnospermae adalah fenomena <strong>Pembuahan Ganda (Double Fertilization)</strong>:</p>
<ol>
  <li><strong>Inti Sperma I (n)</strong> membuahi <strong>Sel Telur (n)</strong> menghasilkan <strong>Zigot (2n)</strong> yang akan berkembang menjadi embrio tumbuhan baru.</li>
  <li><strong>Inti Sperma II (n)</strong> membuahi <strong>Inti Kandung Lembaga Sekunder (2n)</strong> menghasilkan jaringan <strong>Endosperm (3n)</strong> yang kaya nutrisi sebagai bekal pertumbuhan embrio dalam biji.</li>
</ol>",
            ],
        ];

        foreach ($modules as $mod) {
            LearningModule::firstOrCreate(
                ['slug' => $mod['slug']],
                [
                    'title' => $mod['title'],
                    'description' => $mod['description'],
                    'content' => $mod['content'],
                    'module_order' => $mod['module_order'],
                    'status' => 'published',
                ]
            );
        }
    }
}
