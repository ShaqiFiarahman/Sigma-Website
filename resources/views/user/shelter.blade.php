@extends('layouts.app')
@section('title', 'Informasi Posko & Pengungsian')

@section('content')

{{-- Back link --}}
<div class="mb-4">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-70" style="color: #6650a4;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<h1 class="text-2xl font-bold mb-5" style="color: #1D1B20;">Informasi Posko & Pengungsian</h1>

{{-- Shelter Cards --}}
<div class="space-y-3" id="shelterList">
    @foreach($shelters as $shelter)
        <div class="bg-white border border-slate-200/60 rounded-2xl p-5 transition-all hover:shadow-md"
             style="background: rgba(231, 224, 236, 0.3);">

            {{-- Header: Name + Status --}}
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-extrabold" style="color: #1D1B20;">{{ $shelter['name'] }}</h3>
                @if($shelter['status'] === 'Penuh')
                    <span class="text-xs font-bold px-2.5 py-1 rounded-md"
                          style="background: #FCE4EC; color: #C62828;">
                        {{ $shelter['status'] }}
                    </span>
                @else
                    <span class="text-xs font-bold px-2.5 py-1 rounded-md"
                          style="background: #E8F5E9; color: #2E7D32;">
                        {{ $shelter['status'] }}
                    </span>
                @endif
            </div>

            {{-- Info --}}
            <p class="text-sm text-slate-700 mb-1">Jarak: {{ $shelter['distance'] }}</p>
            <p class="text-sm text-slate-700 mb-4">Kapasitas: {{ $shelter['capacity'] }} orang</p>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $shelter['lat'] }},{{ $shelter['lng'] }}"
                   target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white rounded-xl transition-all hover:opacity-90"
                   style="background: #6650a4;">
                    <i class="bi bi-signpost-2-fill"></i> Petunjuk Arah
                </a>
                <button type="button"
                        class="w-11 h-11 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-purple-50 transition-colors"
                        onclick="showLogistics('{{ $shelter['name'] }}', {{ json_encode($shelter['logistics']) }})"
                        title="Kebutuhan Logistik">
                    <i class="bi bi-info-circle-fill text-lg" style="color: #6650a4;"></i>
                </button>
            </div>
        </div>
    @endforeach
</div>

{{-- Logistics Modal --}}
<div id="logisticsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.4); backdrop-filter: blur(8px);">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl">

        <h3 class="text-xl font-extrabold mb-1" style="color: #1D1B20;">Kebutuhan Logistik</h3>
        <p id="modalShelterName" class="text-base font-medium mb-3" style="color: #6650a4;"></p>

        <p class="text-sm text-slate-600 mb-4">
            Masyarakat dapat mengirimkan bantuan mendesak berikut:
        </p>

        <div id="modalLogistics" class="flex flex-wrap gap-2 mb-5"></div>

        <p class="text-xs text-slate-400 mb-5 italic">
            *Bantuan Anda sangat berarti bagi warga di pengungsian.
        </p>

        {{-- WhatsApp Button --}}
        <a id="waButton" href="#" target="_blank"
           class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold text-white rounded-xl transition-all hover:opacity-90 mb-2"
           style="background: #25D366;">
            <i class="bi bi-whatsapp"></i> Hubungi via WhatsApp
        </a>

        <button type="button" onclick="closeLogistics()"
                class="w-full py-3 text-sm font-bold text-red-600 hover:text-red-800 transition-colors">
            Tutup
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const waPhone = '6285934415914'; // Sesuai Android source

    function showLogistics(name, logistics) {
        document.getElementById('modalShelterName').textContent = name;

        const container = document.getElementById('modalLogistics');
        container.innerHTML = logistics.map(item =>
            `<span style="background:#EADDFF; color:#6650a4; font-size:13px; font-weight:600; padding:6px 12px; border-radius:10px;">${item}</span>`
        ).join('');

        // WhatsApp link
        const message = `Halo, saya ingin mengirimkan bantuan logistik ke ${name} berupa: ${logistics.join(', ')}`;
        document.getElementById('waButton').href =
            `https://api.whatsapp.com/send?phone=${waPhone}&text=${encodeURIComponent(message)}`;

        document.getElementById('logisticsModal').classList.remove('hidden');
    }

    function closeLogistics() {
        document.getElementById('logisticsModal').classList.add('hidden');
    }

    // Close on background click
    document.getElementById('logisticsModal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('logisticsModal')) closeLogistics();
    });
</script>
@endsection
