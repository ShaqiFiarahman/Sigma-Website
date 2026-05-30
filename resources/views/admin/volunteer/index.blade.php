@extends('layouts.app')
@section('title', 'Daftar Relawan')
@section('subtitle', 'Kelola pendaftaran dan data relawan.')

@section('page-actions')
    <x-ui.back-button :route="route('admin.dashboard')" />
@endsection

@section('content')

    @php
        $countTotal = $volunteers->count();
        $countPending = $volunteers->where('status', 'PENDING')->count();
        $countApproved = $volunteers->where('status', 'APPROVED')->count();
        $countUnassigned = $volunteers->where('status', 'APPROVED')->whereNull('assignment')->count();
    @endphp

    @if(session('msg'))
        <div class="mb-6 p-4 rounded-xl flex items-center gap-3 text-sm font-medium
            {{ session('msg') === 'approved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' }}">
            @if(session('msg') === 'approved')
                <i class="bi bi-check-circle-fill text-emerald-500"></i> Relawan berhasil disetujui.
            @else
                <i class="bi bi-x-circle-fill text-red-500"></i> Relawan berhasil ditolak.
            @endif
            <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif

    {{-- Mini Stats Panel --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 animate-fade-up" style="animation-delay: 0.1s;">
        <button type="button" data-filter="ALL" 
           class="filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer ring-2 ring-blue-500/20 border-blue-500 shadow-blue-500/5 focus:outline-none">
            <div class="text-blue-500 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Total Relawan</p>
                <h4 class="text-xl font-black text-slate-800 mt-0.5">{{ $countTotal }}</h4>
            </div>
        </button>

        <button type="button" data-filter="PENDING" 
           class="filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer border-slate-200/80 focus:outline-none">
            <div class="text-amber-500 shrink-0">
                <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Menunggu Verifikasi</p>
                <h4 class="text-xl font-black text-slate-800 mt-0.5">{{ $countPending }}</h4>
            </div>
        </button>

        <button type="button" data-filter="APPROVED" 
           class="filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer border-slate-200/80 focus:outline-none">
            <div class="text-emerald-500 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="8 11.5 11 14.5 17 8.5" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Disetujui</p>
                <h4 class="text-xl font-black text-slate-800 mt-0.5">{{ $countApproved }}</h4>
            </div>
        </button>

        <button type="button" data-filter="UNASSIGNED" 
           class="filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer border-slate-200/80 focus:outline-none">
            <div class="text-purple-500 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="19" y1="8" x2="19" y2="14" />
                    <line x1="16" y1="11" x2="22" y2="11" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Belum Ditugaskan</p>
                <h4 class="text-xl font-black text-slate-800 mt-0.5">{{ $countUnassigned }}</h4>
            </div>
        </button>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 bg-white border-l-4 border-l-[#3B6FE8]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daftar Relawan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola pendaftaran dan persetujuan relawan</p>
                </div>
                
                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="volunteerSearch" placeholder="Cari nama relawan..."
                           class="w-full pl-9 pr-10 py-1.75 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-slate-50/50 focus:bg-white text-slate-800 placeholder:text-slate-400 transition-all duration-200">
                    <button type="button" id="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors hidden focus:outline-none">
                        <i class="bi bi-x-circle-fill text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100/80 bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-3.5 font-bold text-xs text-slate-500">Nama</th>
                        <th class="px-6 py-3.5 font-bold text-xs text-slate-500">Keahlian</th>
                        <th class="px-6 py-3.5 font-bold text-xs text-slate-500">Telepon</th>
                        <th class="px-6 py-3.5 font-bold text-xs text-slate-500">Status</th>
                        <th class="px-6 py-3.5 font-bold text-xs text-slate-500">Penugasan</th>
                        <th class="px-6 py-3.5 font-bold text-xs text-slate-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($volunteers as $volunteer)
                        <tr class="volunteer-row hover:bg-slate-50/70 transition-colors"
                            data-name="{{ $volunteer->name }}"
                            data-status="{{ $volunteer->status }}"
                            data-assigned="{{ ($volunteer->assignment || $volunteer->disaster_id) ? 'true' : 'false' }}">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">{{ $volunteer->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $volunteer->address }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background: #E4F0F6; color: #3B6FE8;">
                                    {{ $volunteer->skill }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $volunteer->phone_number }}</td>
                            <td class="px-6 py-4">
                                @if($volunteer->status === 'PENDING')
                                    <span class="text-xs font-bold text-amber-600">Pending</span>
                                @elseif($volunteer->status === 'APPROVED')
                                    <span class="text-xs font-bold text-emerald-600">Approved</span>
                                @elseif($volunteer->status === 'FIRED')
                                    <span class="text-xs font-bold text-rose-600">Nonaktif</span>
                                @else
                                    <span class="text-xs font-bold text-slate-500">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($volunteer->disaster_id || $volunteer->assignment)
                                    <div class="flex flex-col gap-1.5 items-start">
                                        @if($volunteer->disaster)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100/50">
                                                <i class="bi bi-geo-alt text-[9px] shrink-0"></i>
                                                <span class="truncate max-w-[120px]">{{ $volunteer->disaster->title }}</span>
                                            </span>
                                        @endif
                                        @if($volunteer->assignment)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-100/50">
                                                <i class="bi bi-house text-[9px] shrink-0"></i>
                                                <span class="truncate max-w-[120px]">{{ $volunteer->assignment }}</span>
                                            </span>
                                        @endif
                                        @if($volunteer->assignment_status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100/50">
                                                <i class="bi bi-hourglass-split text-[9px]"></i> Menunggu Konfirmasi
                                            </span>
                                        @elseif($volunteer->assignment_status === 'accepted')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100/50">
                                                <i class="bi bi-check-circle-fill text-[9px]"></i> Diterima
                                            </span>
                                        @elseif($volunteer->assignment_status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100/50">
                                                <i class="bi bi-x-circle-fill text-[9px]"></i> Ditolak
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 font-semibold bg-slate-50 border border-slate-100/80 px-2 py-0.5 rounded-md">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('volunteer.show', $volunteer->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.25 text-xs font-semibold text-slate-600 hover:text-blue-600 bg-slate-50 hover:bg-blue-50/50 rounded-lg transition-all duration-200 group border border-slate-100/80 hover:border-blue-100">
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-slate-400">
                                <div class="mx-auto mb-4 text-blue-500 flex items-center justify-center shrink-0">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada relawan</p>
                                <p class="text-xs text-slate-400">Belum ada pendaftaran relawan.</p>
                            </td>
                        </tr>
                    @endforelse

                    {{-- Row for empty search/filter results --}}
                    <tr id="emptyRow" class="hidden">
                        <td colspan="6" class="px-6 py-20 text-center text-slate-400">
                            <div class="mx-auto mb-4 text-blue-500 flex items-center justify-center shrink-0">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada relawan yang cocok</p>
                            <p class="text-xs text-slate-400">Coba ubah filter atau kata kunci pencarian Anda.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 bg-slate-50/50">
            <span>Menampilkan <span id="visibleCount" class="font-bold text-slate-700">{{ $volunteers->count() }}</span> dari <span class="font-bold text-slate-700">{{ $volunteers->count() }}</span> relawan</span>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('volunteerSearch');
        const clearSearchBtn = document.getElementById('clearSearch');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const rows = document.querySelectorAll('.volunteer-row');
        const emptyRow = document.getElementById('emptyRow');
        const visibleCountEl = document.getElementById('visibleCount');
        const totalCount = rows.length;

        let currentStatusFilter = 'ALL';
        let currentSearchQuery = '';

        function filterVolunteers() {
            let visibleCount = 0;

            rows.forEach(row => {
                const name = (row.getAttribute('data-name') || '').toLowerCase();
                const status = row.getAttribute('data-status');
                const isAssigned = row.getAttribute('data-assigned') === 'true';

                // Status match logic
                let matchesStatus = false;
                if (currentStatusFilter === 'ALL') {
                    matchesStatus = true;
                } else if (currentStatusFilter === 'PENDING') {
                    matchesStatus = (status === 'PENDING');
                } else if (currentStatusFilter === 'APPROVED') {
                    matchesStatus = (status === 'APPROVED');
                } else if (currentStatusFilter === 'UNASSIGNED') {
                    matchesStatus = (status === 'APPROVED' && !isAssigned);
                }

                // Search match logic (Case-insensitive)
                const matchesSearch = name.includes(currentSearchQuery.toLowerCase());

                if (matchesStatus && matchesSearch) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            // Show empty search fallback if no rows visible
            if (visibleCount === 0 && totalCount > 0) {
                emptyRow.classList.remove('hidden');
            } else {
                emptyRow.classList.add('hidden');
            }

            // Update footer visible count
            if (visibleCountEl) {
                visibleCountEl.textContent = visibleCount;
            }
        }

        // Search Input listener
        searchInput?.addEventListener('input', (e) => {
            currentSearchQuery = e.target.value.trim();
            if (clearSearchBtn) {
                if (currentSearchQuery.length > 0) {
                    clearSearchBtn.classList.remove('hidden');
                } else {
                    clearSearchBtn.classList.add('hidden');
                }
            }
            filterVolunteers();
        });

        // Prevent Enter reloading
        searchInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        // Clear search listener
        clearSearchBtn?.addEventListener('click', () => {
            searchInput.value = '';
            currentSearchQuery = '';
            clearSearchBtn.classList.add('hidden');
            filterVolunteers();
        });

        // Filter button listeners
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const filter = btn.getAttribute('data-filter');
                currentStatusFilter = filter;

                // Reset styles of all filter cards
                filterButtons.forEach(b => {
                    b.className = "filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer border-slate-200/80 focus:outline-none";
                });

                // Apply active glow style to clicked card
                if (filter === 'ALL') {
                    btn.className = "filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer ring-2 ring-blue-500/20 border-blue-500 shadow-blue-500/5 focus:outline-none";
                } else if (filter === 'PENDING') {
                    btn.className = "filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer ring-2 ring-amber-500/20 border-amber-500 shadow-amber-500/5 focus:outline-none";
                } else if (filter === 'APPROVED') {
                    btn.className = "filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer ring-2 ring-emerald-500/20 border-emerald-500 shadow-emerald-500/5 focus:outline-none";
                } else if (filter === 'UNASSIGNED') {
                    btn.className = "filter-btn text-left bg-white border rounded-2xl p-4.5 flex items-center gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer ring-2 ring-purple-500/20 border-purple-500 shadow-purple-500/5 focus:outline-none";
                }

                filterVolunteers();
            });
        });
    });
</script>
@endsection
