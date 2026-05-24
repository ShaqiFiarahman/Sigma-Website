@extends('layouts.app')
@section('title', 'Laporan Tugas Relawan')
@section('subtitle', 'Monitoring laporan yang dikirim relawan di lapangan.')

@section('page-actions')
    <a href="{{ route('volunteer.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
        <i class="bi bi-people text-xs"></i> Daftar Relawan
    </a>
@endsection

@section('content')

{{-- Filters & Search --}}
<div class="bg-white rounded-2xl border border-slate-200/80 p-4.5 mb-6 shadow-sm"
     style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
    <div class="flex flex-col md:flex-row md:items-end gap-3.5">
        {{-- Search Input --}}
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Cari Laporan</label>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="reportSearch" placeholder="Cari nama relawan, bencana, catatan..." 
                       class="w-full pl-8.5 pr-3 py-1.75 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-slate-50 focus:bg-white text-slate-800 placeholder:text-slate-400 transition-all duration-200">
            </div>
        </div>
        {{-- Skill filter --}}
        <div class="w-full md:w-44 shrink-0">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Keahlian</label>
            <select id="filterSkill" class="w-full px-3 py-1.75 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 text-slate-700 cursor-pointer transition-all duration-200">
                <option value="">Semua Keahlian</option>
                @foreach($skills as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        {{-- Disaster filter --}}
        <div class="w-full md:w-56 shrink-0">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Bencana</label>
            <select id="filterDisaster" class="w-full px-3 py-1.75 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 text-slate-700 cursor-pointer transition-all duration-200">
                <option value="">Semua Bencana</option>
                @foreach($disasters as $d)
                    <option value="{{ $d->id }}">
                        {{ \Illuminate\Support\Str::limit($d->title, 28) }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- Volunteer filter --}}
        <div class="w-full md:w-52 shrink-0">
            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Relawan</label>
            <select id="filterVolunteer" class="w-full px-3 py-1.75 text-xs border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 text-slate-700 cursor-pointer transition-all duration-200">
                <option value="">Semua Relawan</option>
                @foreach($volunteers as $v)
                    <option value="{{ $v->id }}">
                        {{ $v->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="button" id="resetBtn" class="px-4.5 py-1.75 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200/80 rounded-xl transition-all duration-200 cursor-pointer w-full sm:w-auto h-[32px] flex items-center justify-center gap-1.5 shadow-sm border border-slate-150">
                <i class="bi bi-x-circle text-[10px]"></i> Reset
            </button>
        </div>
    </div>
</div>

{{-- Reports --}}
<div class="space-y-4" id="reportsContainer">
    @forelse($reports as $report)
        <div class="report-card bg-white rounded-2xl border border-slate-200/80 overflow-hidden transition-all duration-200"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.04);"
             data-skill="{{ $report->skill_type }}"
             data-disaster="{{ $report->disaster_id }}"
             data-volunteer="{{ $report->volunteer_id }}"
             data-search-text="{{ strtolower($report->volunteer->name ?? '') }} {{ strtolower($report->disaster->title ?? '') }} {{ strtolower($report->notes ?? '') }} {{ strtolower($report->skill_type ?? '') }}">
            
            {{-- Header --}}
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                        {{ substr($report->volunteer->name ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $report->volunteer->name ?? 'Unknown' }}</p>
                        <p class="text-[11px] text-slate-400">{{ $report->skill_type }} · {{ $report->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @if($report->disaster)
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100 hidden sm:inline-flex items-center gap-1">
                        <i class="bi bi-geo-alt text-[9px]"></i> {{ \Illuminate\Support\Str::limit($report->disaster->title, 25) }}
                    </span>
                @endif
            </div>

            {{-- Data --}}
            <div class="p-5">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($report->report_data ?? [] as $key => $value)
                        <div class="bg-slate-50 rounded-xl px-3 py-2.5 border border-slate-100">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-0.5">
                                {{ str_replace('_', ' ', ucfirst($key)) }}
                            </p>
                            <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>

                @if($report->notes)
                    <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-xs text-slate-600">{{ $report->notes }}</p>
                    </div>
                @endif

                @if($report->photo_urls && count($report->photo_urls) > 0)
                    <div class="mt-3 flex gap-2 flex-wrap">
                        @foreach($report->photo_urls as $photo)
                            <a href="{{ $photo }}" target="_blank" class="block w-16 h-16 rounded-lg overflow-hidden border border-slate-200 hover:border-blue-300 transition-colors">
                                <img src="{{ $photo }}" alt="Foto laporan" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($report->disaster)
                    <p class="text-[11px] text-slate-400 mt-3 sm:hidden">
                        <i class="bi bi-geo-alt"></i> {{ $report->disaster->title }}
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-sm">
            <i class="bi bi-clipboard-data text-3xl text-slate-350"></i>
            <p class="text-sm font-semibold text-slate-700 mt-3">Belum ada laporan tugas</p>
            <p class="text-xs text-slate-400 mt-1">Laporan akan muncul setelah relawan mengirim dari lapangan.</p>
        </div>
    @endforelse

    {{-- Fallback empty state for JS filtering --}}
    <div id="noReportsFound" class="hidden bg-white rounded-2xl border border-slate-200/80 p-14 text-center shadow-sm">
        <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center bg-blue-50/60">
            <i class="bi bi-search text-2xl text-blue-600"></i>
        </div>
        <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada laporan yang cocok</p>
        <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau pengaturan filter Anda.</p>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reportSearch = document.getElementById('reportSearch');
        const filterSkill = document.getElementById('filterSkill');
        const filterDisaster = document.getElementById('filterDisaster');
        const filterVolunteer = document.getElementById('filterVolunteer');
        const resetBtn = document.getElementById('resetBtn');
        const cards = document.querySelectorAll('.report-card');
        const noReportsFound = document.getElementById('noReportsFound');

        function filterReports() {
            const query = (reportSearch.value || '').toLowerCase().trim();
            const skill = filterSkill.value;
            const disaster = filterDisaster.value;
            const volunteer = filterVolunteer.value;

            let visibleCount = 0;

            cards.forEach(card => {
                const cardSkill = card.getAttribute('data-skill') || '';
                const cardDisaster = card.getAttribute('data-disaster') || '';
                const cardVolunteer = card.getAttribute('data-volunteer') || '';
                const cardText = card.getAttribute('data-search-text') || '';

                const matchesSearch = !query || cardText.includes(query);
                const matchesSkill = !skill || cardSkill === skill;
                const matchesDisaster = !disaster || cardDisaster === disaster;
                const matchesVolunteer = !volunteer || cardVolunteer === volunteer;

                if (matchesSearch && matchesSkill && matchesDisaster && matchesVolunteer) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0 && cards.length > 0) {
                noReportsFound.classList.remove('hidden');
            } else {
                noReportsFound.classList.add('hidden');
            }
        }

        reportSearch?.addEventListener('input', filterReports);
        filterSkill?.addEventListener('change', filterReports);
        filterDisaster?.addEventListener('change', filterReports);
        filterVolunteer?.addEventListener('change', filterReports);

        reportSearch?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') e.preventDefault();
        });

        resetBtn?.addEventListener('click', () => {
            if (reportSearch) reportSearch.value = '';
            if (filterSkill) filterSkill.value = '';
            if (filterDisaster) filterDisaster.value = '';
            if (filterVolunteer) filterVolunteer.value = '';
            filterReports();
        });
    });
</script>
@endsection
