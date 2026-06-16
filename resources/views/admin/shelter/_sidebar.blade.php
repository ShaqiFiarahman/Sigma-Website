{{-- section sidebar info posko --}}
<div class="lg:col-span-2 space-y-5">

    {{-- stats dan detail posko --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">

        {{-- status operasional posko --}}
        <div class="p-5">
            <p class="text-xs font-bold text-slate-700 mb-3">Status Posko</p>
            <div class="space-y-2.5">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Status</span>
                    <span class="font-semibold {{ $shelter->status === 'Tersedia' ? 'text-emerald-600' : 'text-red-600' }}">{{ $shelter->status }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Kapasitas</span>
                    <span class="font-semibold text-slate-800">{{ $shelter->capacity_current }}/{{ $shelter->capacity_max }} orang</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Koordinat</span>
                    <span class="font-mono text-slate-600 text-[11px]" id="coordStatusText">{{ $shelter->latitude }}, {{ $shelter->longitude }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Terakhir update</span>
                    <span class="text-slate-600">{{ $shelter->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- divider --}}
        <div class="border-t border-slate-100"></div>

        {{-- daftar relawan yang lagi bertugas --}}
        <div class="px-5 py-4 flex items-center justify-between">
            <p class="text-xs font-bold text-slate-700">Relawan Bertugas</p>
            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $assignedVolunteers->count() }}</span>
        </div>
        <div class="px-5 pb-5">
            @if($assignedVolunteers->count() > 0)
                <div class="space-y-3">
                    @foreach($assignedVolunteers as $vol)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                 style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%);">
                                {{ strtoupper(substr($vol->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-800 truncate">{{ $vol->name }}</p>
                                <p class="text-[10px] text-slate-500">{{ $vol->skill }} · {{ $vol->phone_number }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 text-center py-2">Belum ada relawan ditugaskan.</p>
            @endif
        </div>

        {{-- daftar kebutuhan logistik posko --}}
        @php($dynamicLogistics = $shelter->getDynamicLogistics())
        @if(!empty($dynamicLogistics))
            <div class="border-t border-slate-100"></div>
            <div class="p-5">
                <p class="text-xs font-bold text-slate-700 mb-3">Kebutuhan Logistik</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($dynamicLogistics as $item)
                        <span class="text-[11px] font-medium px-2.5 py-1 rounded-lg border border-teal-100 text-teal-700" style="background: #F0FDFA;">{{ $item }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- section bahaya: hapus posko --}}
    <div class="bg-white border border-red-100/50 rounded-2xl p-5" style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);">
        <p class="text-xs font-semibold text-slate-800 mb-2">Hapus Posko</p>
        <p class="text-[11px] text-slate-500 mb-3">Tindakan ini tidak bisa dibatalkan. Semua data posko akan dihapus permanen.</p>
        <button type="button" onclick="document.getElementById('deleteSection').classList.toggle('hidden')"
                class="w-full py-2 text-xs font-semibold text-red-600 bg-red-50/30 border border-red-100/60 rounded-xl hover:bg-red-50/70 transition-all duration-200 cursor-pointer">
            Hapus Posko Ini
        </button>

        {{-- input konfirmasi ketik nama posko --}}
        <div id="deleteSection" class="hidden mt-4 pt-4 border-t border-red-100">
            <p class="text-[11px] text-slate-500 mb-2">Ketik <strong class="text-slate-800">{{ $shelter->name }}</strong> untuk konfirmasi:</p>
            <input type="text" id="deleteConfirmInput" placeholder="Ketik nama posko..."
                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-500/20 bg-white text-slate-800 mb-3">
            <form action="{{ route('admin.shelter.delete', $shelter->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" id="deleteBtn" disabled
                        class="w-full py-2 text-xs font-semibold text-white bg-red-400 rounded-lg cursor-not-allowed opacity-50">
                    Konfirmasi Hapus
                </button>
            </form>
        </div>
    </div>
</div>
