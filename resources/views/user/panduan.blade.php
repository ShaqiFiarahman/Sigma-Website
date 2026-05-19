@extends('layouts.app')
@section('title', 'Panduan Bencana')
@section('subtitle', 'Pelajari panduan mitigasi dan kesiapsiagaan bencana.')

@section('page-actions')
    <button type="button" onclick="window.location.href='{{ route('dashboard') }}'" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 shadow-sm cursor-pointer">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </button>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:items-stretch">

        {{-- KOLOM KIRI: PDF Viewer (3/4) --}}
        <div class="lg:col-span-3 flex flex-col gap-3">

            {{-- PDF Card --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden flex-1 flex flex-col"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                {{-- Header --}}
                <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                     style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                             style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                            <i class="bi bi-book-fill text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white leading-tight">Buku Saku Siaga Bencana</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(228,240,246,0.6);">BNPB 2019 &middot; Panduan Mitigasi Bencana</p>
                        </div>
                    </div>
                    <button type="button"
                            onclick="window.open('/panduan-bencana.pdf', '_blank')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 cursor-pointer shrink-0"
                            style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                        <i class="bi bi-download text-[11px]"></i> Unduh PDF &middot; 86 Halaman
                    </button>
                </div>

                {{-- PDF Viewer --}}
                <div class="flex-1 bg-white p-2" style="min-height: 600px;">
                    <iframe
                        src="/panduan-bencana.pdf#toolbar=0&navpanes=0&view=FitH"
                        class="w-full h-full rounded-lg"
                        style="border: 1px solid #e2e8f0; min-height: 580px; background: white;"
                        title="Buku Saku Siaga Bencana BNPB 2019">
                    </iframe>
                </div>
            </div>

            {{-- Fallback --}}
            <p class="text-center text-xs text-slate-400">
                PDF tidak tampil?
                <button type="button" onclick="window.open('/panduan-bencana.pdf', '_blank')" class="font-semibold hover:underline cursor-pointer" style="color: #3B6FE8;">
                    Buka di tab baru
                </button>
            </p>
        </div>

        {{-- KOLOM KANAN: Panduan Cepat --}}
        <div class="flex flex-col">
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden flex-1"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                        <i class="bi bi-lightning-fill text-amber-500"></i>
                        Panduan Cepat
                    </h3>
                </div>
                <div class="p-5 space-y-5">

                    {{-- Banjir --}}
                    <div>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-1.5">
                            <i class="bi bi-water text-blue-500 text-[11px]"></i> Saat Banjir
                        </p>
                        <ul class="space-y-1 text-[11px] text-slate-600 ml-5">
                            <li class="flex items-start gap-1.5"><span class="text-blue-400 mt-0.5">•</span> Matikan aliran listrik</li>
                            <li class="flex items-start gap-1.5"><span class="text-blue-400 mt-0.5">•</span> Hindari arus deras</li>
                            <li class="flex items-start gap-1.5"><span class="text-blue-400 mt-0.5">•</span> Pindah ke tempat tinggi</li>
                        </ul>
                    </div>

                    {{-- Kebakaran --}}
                    <div>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-1.5">
                            <i class="bi bi-fire text-red-500 text-[11px]"></i> Saat Kebakaran
                        </p>
                        <ul class="space-y-1 text-[11px] text-slate-600 ml-5">
                            <li class="flex items-start gap-1.5"><span class="text-red-400 mt-0.5">•</span> Gunakan jalur evakuasi</li>
                            <li class="flex items-start gap-1.5"><span class="text-red-400 mt-0.5">•</span> Jangan gunakan lift</li>
                            <li class="flex items-start gap-1.5"><span class="text-red-400 mt-0.5">•</span> Merangkak jika banyak asap</li>
                        </ul>
                    </div>

                    {{-- Gempa --}}
                    <div>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-1.5">
                            <i class="bi bi-globe-americas text-emerald-500 text-[11px]"></i> Saat Gempa
                        </p>
                        <ul class="space-y-1 text-[11px] text-slate-600 ml-5">
                            <li class="flex items-start gap-1.5"><span class="text-emerald-400 mt-0.5">•</span> Berlindung di bawah meja</li>
                            <li class="flex items-start gap-1.5"><span class="text-emerald-400 mt-0.5">•</span> Jauhi kaca dan benda berat</li>
                            <li class="flex items-start gap-1.5"><span class="text-emerald-400 mt-0.5">•</span> Keluar setelah guncangan berhenti</li>
                        </ul>
                    </div>

                    {{-- Tsunami --}}
                    <div>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-1.5">
                            <i class="bi bi-tsunami text-cyan-500 text-[11px]"></i> Saat Tsunami
                        </p>
                        <ul class="space-y-1 text-[11px] text-slate-600 ml-5">
                            <li class="flex items-start gap-1.5"><span class="text-cyan-400 mt-0.5">•</span> Segera ke dataran tinggi</li>
                            <li class="flex items-start gap-1.5"><span class="text-cyan-400 mt-0.5">•</span> Jauhi pantai dan sungai</li>
                            <li class="flex items-start gap-1.5"><span class="text-cyan-400 mt-0.5">•</span> Ikuti jalur evakuasi tsunami</li>
                        </ul>
                    </div>

                    {{-- Longsor --}}
                    <div>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-1.5">
                            <i class="bi bi-layers text-amber-600 text-[11px]"></i> Saat Longsor
                        </p>
                        <ul class="space-y-1 text-[11px] text-slate-600 ml-5">
                            <li class="flex items-start gap-1.5"><span class="text-amber-400 mt-0.5">•</span> Jauhi lereng dan tebing</li>
                            <li class="flex items-start gap-1.5"><span class="text-amber-400 mt-0.5">•</span> Perhatikan tanda retakan tanah</li>
                            <li class="flex items-start gap-1.5"><span class="text-amber-400 mt-0.5">•</span> Evakuasi ke tempat datar</li>
                        </ul>
                    </div>

                    {{-- Gunung Meletus --}}
                    <div>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-1.5">
                            <i class="bi bi-triangle text-orange-500 text-[11px]"></i> Saat Gunung Meletus
                        </p>
                        <ul class="space-y-1 text-[11px] text-slate-600 ml-5">
                            <li class="flex items-start gap-1.5"><span class="text-orange-400 mt-0.5">•</span> Jauhi radius zona bahaya</li>
                            <li class="flex items-start gap-1.5"><span class="text-orange-400 mt-0.5">•</span> Gunakan masker dari abu vulkanik</li>
                            <li class="flex items-start gap-1.5"><span class="text-orange-400 mt-0.5">•</span> Ikuti arahan BPBD setempat</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
