<x-app-layout :title="$plant->local_name . ' (' . $plant->scientific_name . ') — Herbarium Botani Phanerogamae'">
    <div class="py-12 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-hidden min-w-0" x-data="{ activeTab: 'theory' }">
        <!-- Breadcrumb & Back Link -->
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Kembali ke Galeri Tumbuhan</span>
            </a>
        </div>

        <!-- Main Hero Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 items-start mb-16 min-w-0">
            <!-- Left Panel: High Resolution Image & Media -->
            <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-28 min-w-0">
                <div class="rounded-3xl overflow-hidden bg-white border border-emerald-300 shadow-lg relative aspect-[4/3] sm:aspect-square group">
                    @if($plant->image_url)
                        <img src="{{ $plant->image_url }}" alt="{{ $plant->local_name }}" 
                             class="w-full h-full object-cover group- transition-transform duration-700 ease-out">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-emerald-100 text-slate-600 space-y-2">
                            <span class="text-6xl">🌱</span>
                            <span class="text-xs">Gambar Herbarium Belum Tersedia</span>
                        </div>
                    @endif
                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3 rounded-xl bg-white/90 border border-white px-3 py-2 text-xs text-slate-700 shadow-sm">
                        <span>📷 Dokumentasi Herbarium Digital</span>
                        <span class="font-semibold text-emerald-700">{{ $plant->author_name ?? 'L.' }}</span>
                    </div>
                </div>

                <!-- Quick Taxonomy Pills Box -->
                <div class="bg-white border border-emerald-200 rounded-2xl p-6 space-y-4 min-w-0">
                    <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-widest border-b border-emerald-200 pb-2">
                        🏷️ Klasifikasi Cepat
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs min-w-0">
                        <div class="p-3 rounded-xl bg-white border border-emerald-200 break-words">
                            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Divisi / Kelompok</span>
                            <span class="font-bold text-emerald-700 text-sm mt-0.5 block">{{ $plant->group_type }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-emerald-200 break-words">
                            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Kelas Kotiledon</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $plant->cotyledon_type }}</span>
                        </div>
                        @php
                            $family = $plant->taxa->where('rank', 'famili')->first();
                            $order = $plant->taxa->where('rank', 'ordo')->first();
                        @endphp
                        <div class="p-3 rounded-xl bg-white border border-emerald-200 break-words">
                            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Ordo</span>
                            <span class="font-bold text-slate-700 text-sm mt-0.5 block">{{ $order ? $order->name : '-' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-emerald-200 break-words">
                            <span class="text-slate-500 block text-[10px] uppercase font-semibold">Famili</span>
                            <span class="font-bold text-amber-700 text-sm mt-0.5 block">{{ $family ? $family->name : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Comprehensive Botanical Details -->
            <div class="lg:col-span-7 space-y-8 min-w-0 break-words">
                <!-- Title & Author -->
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ $plant->group_type === 'Gymnospermae' ? 'bg-amber-100 text-amber-700 border border-amber-300' : 'bg-emerald-100 text-emerald-700 border border-emerald-300' }}">
                            {{ $plant->group_type }}
                        </span>
                        @if($plant->cotyledon_type !== 'Tidak Berlaku')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-slate-700 border border-emerald-200">
                                {{ $plant->cotyledon_type }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                        {{ $plant->local_name }}
                    </h1>
                    <p class="text-xl sm:text-2xl italic font-serif text-emerald-700 mt-1">
                        {{ $plant->scientific_name }} <span class="not-italic text-base text-slate-600">{{ $plant->author_name ?? '' }}</span>
                    </p>
                </div>

                <!-- Navigation Tabs -->
                <div class="border-b border-emerald-200 flex gap-4 sm:gap-6 text-xs sm:text-sm font-semibold overflow-x-auto pb-1 max-w-full">
                    <button @click="activeTab = 'theory'"
                            :class="activeTab === 'theory' ? 'text-emerald-700 border-b-2 border-emerald-400 pb-3 shrink-0' : 'text-slate-600 hover:text-emerald-800 pb-3 transition-colors shrink-0'">
                        📖 Deskripsi & Teori Botani
                    </button>
                    <button @click="activeTab = 'taxonomy'"
                            :class="activeTab === 'taxonomy' ? 'text-emerald-700 border-b-2 border-emerald-400 pb-3 shrink-0' : 'text-slate-600 hover:text-emerald-800 pb-3 transition-colors shrink-0'">
                        🌿 Hierarki Taksonomi
                    </button>
                    <button @click="activeTab = 'morphology'"
                            :class="activeTab === 'morphology' ? 'text-emerald-700 border-b-2 border-emerald-400 pb-3 shrink-0' : 'text-slate-600 hover:text-emerald-800 pb-3 transition-colors shrink-0'">
                        🔬 Morfologi Organ
                    </button>
                    <button @click="activeTab = 'observation'"
                            :class="activeTab === 'observation' ? 'text-emerald-700 border-b-2 border-emerald-400 pb-3 shrink-0' : 'text-slate-600 hover:text-emerald-800 pb-3 transition-colors shrink-0'">
                        📍 Observasi Lapangan
                    </button>
                </div>

                <!-- Tab 1: Theory & General Description -->
                <div x-show="activeTab === 'theory'" x-transition class="space-y-6">
                    <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-6 leading-relaxed text-slate-700">
                        <div>
                            <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wider mb-3">Deskripsi Ilmiah & Landasan Teori</h3>
                            <p class="text-base leading-relaxed text-slate-700">{{ $plant->description }}</p>
                        </div>

                        @if($plant->habitat)
                            <div class="pt-4 border-t border-emerald-200">
                                <h4 class="text-xs font-bold text-amber-700 uppercase tracking-widest mb-2">🌱 Habitat & Ekologi Tropis</h4>
                                <p class="text-sm text-slate-700">{{ $plant->habitat }}</p>
                            </div>
                        @endif

                        @if($plant->benefits)
                            <div class="pt-4 border-t border-emerald-200">
                                <h4 class="text-xs font-bold text-teal-700 uppercase tracking-widest mb-2">💎 Nilai Manfaat & Kegunaan</h4>
                                <p class="text-sm text-slate-700">{{ $plant->benefits }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 2: Full Taxonomy Tree -->
                <div x-show="activeTab === 'taxonomy'" x-transition class="space-y-4">
                    <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-4">
                        <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wider mb-4">Hierarki Taksonomi Lengkap</h3>
                        @php
                            $rankOrder = ['kingdom', 'divisi', 'kelas', 'ordo', 'famili', 'genus', 'spesies'];
                            $sortedTaxa = $plant->taxa->sortBy(function($t) use ($rankOrder) {
                                return array_search($t->rank, $rankOrder);
                            });
                        @endphp

                        <div class="space-y-3 relative before:absolute before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-emerald-300">
                            @foreach($sortedTaxa as $tax)
                                <div class="flex items-center gap-4 relative pl-8">
                                    <span class="absolute left-2.5 top-1.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white"></span>
                                    <div class="p-3.5 rounded-2xl bg-white border border-emerald-200 flex-grow flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">{{ $tax->rank }}</span>
                                            <span class="text-sm font-bold text-slate-900 {{ in_array($tax->rank, ['genus', 'spesies']) ? 'italic font-serif' : '' }}">{{ $tax->name }}</span>
                                        </div>
                                        <span class="text-xs text-slate-500">Phanerogamae</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Morphology Details -->
                <div x-show="activeTab === 'morphology'" x-transition class="space-y-4">
                    <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-6">
                        <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wider">Karakteristik Anatomi & Morfologi Organ</h3>
                        @if($plant->morphology)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                                <div class="p-4 rounded-2xl bg-white border border-emerald-200">
                                    <span class="text-xs font-bold text-emerald-700 uppercase block mb-1">🌱 Akar (Radix)</span>
                                    <p class="text-slate-700 leading-relaxed">{{ $plant->morphology->root ?? 'Data dalam proses verifikasi.' }}</p>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-emerald-200">
                                    <span class="text-xs font-bold text-emerald-700 uppercase block mb-1">🪵 Batang (Caulis)</span>
                                    <p class="text-slate-700 leading-relaxed">{{ $plant->morphology->stem ?? 'Data dalam proses verifikasi.' }}</p>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-emerald-200">
                                    <span class="text-xs font-bold text-emerald-700 uppercase block mb-1">🍃 Daun (Folium)</span>
                                    <p class="text-slate-700 leading-relaxed">{{ $plant->morphology->leaf ?? 'Data dalam proses verifikasi.' }}</p>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-emerald-200">
                                    <span class="text-xs font-bold text-emerald-700 uppercase block mb-1">🌸 Bunga (Flos / Strobilus)</span>
                                    <p class="text-slate-700 leading-relaxed">{{ $plant->morphology->flower ?? 'Data dalam proses verifikasi.' }}</p>
                                </div>
                                <div class="p-4 rounded-2xl bg-white border border-emerald-200 sm:col-span-2">
                                    <span class="text-xs font-bold text-emerald-700 uppercase block mb-1">🍎 Buah & Biji (Fructus & Semen)</span>
                                    <p class="text-slate-700 leading-relaxed">{{ $plant->morphology->fruit ?? '' }} — {{ $plant->morphology->seed ?? '' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-slate-600 text-sm italic">Data morfologi mendetail belum dikonfigurasi oleh Dosen pengampu.</p>
                        @endif
                    </div>
                </div>

                <!-- Tab 4: Field Observation Details -->
                <div x-show="activeTab === 'observation'" x-transition class="space-y-4">
                    <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 space-y-6">
                        <h3 class="text-sm font-bold text-emerald-700 uppercase tracking-wider">Catatan Sampel Lapangan Sumatera Utara</h3>
                        @if($plant->observations->count() > 0)
                            <div class="space-y-4">
                                @foreach($plant->observations as $obs)
                                    <div class="p-5 rounded-2xl bg-white border border-emerald-200 flex flex-col sm:flex-row justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700">
                                                <span>📍 Lokasi: {{ $obs->location ? $obs->location->location_name . ', ' . $obs->location->province : 'Sumatera Utara' }}</span>
                                            </div>
                                            <p class="text-sm text-slate-700 leading-relaxed">{{ $obs->notes }}</p>
                                            @if($obs->observer)
                                                <span class="text-xs text-slate-500 block pt-1">Pengamat: {{ $obs->observer->name }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 shrink-0">
                                            Tanggal: {{ $obs->observation_date ? \Carbon\Carbon::parse($obs->observation_date)->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-slate-600 text-sm italic">Belum ada catatan observasi lapangan yang ditautkan.</p>
                        @endif
                    </div>
                </div>

                <!-- Related Learning Modules Link Box -->
                @if($plant->learningModules->count() > 0)
                    <div class="p-6 rounded-3xl bg-emerald-100 border border-emerald-300 flex items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-700 uppercase block">📚 Terintegrasi Kurikulum</span>
                            <h4 class="font-bold text-slate-900 text-base">Spesimen Ini Dipelajari dalam Modul Botani</h4>
                        </div>
                        <a href="{{ route('modules.detail', $plant->learningModules->first()->slug) }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-slate-900 text-xs font-semibold hover:bg-emerald-500 transition-all shrink-0">
                            Buka Modul Teori →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
