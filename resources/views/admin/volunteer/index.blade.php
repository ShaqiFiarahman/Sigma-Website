@extends('layouts.app')
@section('title', 'Daftar Relawan')
@section('subtitle', 'Kelola pendaftaran dan data relawan.')

@section('content')

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

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-200/80"
             style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);">
            <h3 class="text-sm font-semibold text-white">Daftar Relawan</h3>
            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">Kelola pendaftaran dan persetujuan relawan</p>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100 text-slate-500" style="background: #F8FAFC;">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Nama</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Keahlian</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Telepon</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Status</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400">Penugasan</th>
                        <th class="px-6 py-3.5 font-semibold text-xs uppercase tracking-wider text-slate-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($volunteers as $volunteer)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">{{ $volunteer->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $volunteer->address }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background: #E4F0F6; color: #3B6FE8;">
                                    {{ $volunteer->skill }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $volunteer->phone_number }}</td>
                            <td class="px-6 py-4">
                                @if($volunteer->status === 'PENDING')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending
                                    </span>
                                @elseif($volunteer->status === 'APPROVED')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($volunteer->assignment)
                                    <span class="text-slate-600">{{ $volunteer->assignment }}</span>
                                @else
                                    <span class="text-slate-400 italic">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('volunteer.show', $volunteer->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:border-purple-300 hover:text-purple-700 hover:bg-purple-50 transition-all shadow-sm">
                                    Detail <i class="bi bi-arrow-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-slate-400">
                                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: #E4F0F6;">
                                    <i class="bi bi-people text-2xl" style="color: #3B6FE8;"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-800 mb-1">Tidak ada relawan</p>
                                <p class="text-xs text-slate-400">Belum ada pendaftaran relawan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($volunteers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500"
                 style="background: #FAFBFD;">
                <span>Menampilkan {{ $volunteers->count() }} dari {{ $volunteers->total() }} relawan</span>
                <div>{{ $volunteers->links() }}</div>
            </div>
        @endif
    </div>

@endsection
