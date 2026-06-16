@extends('layouts.app')
@section('title', 'Detail Relawan')
@section('subtitle', 'Tinjau profil relawan, kelola status keaktifan, dan atur penugasan lokasi tugas.')

@section('page-actions')
    <x-ui.back-button :route="route('volunteer.index')" />
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- section detail relawan --}}
    <div class="lg:col-span-2 space-y-6">

        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

            {{-- header profil relawan --}}
            <div class="pl-6 pr-8 py-5 border-l-4 border-l-[#3B6FE8] border-b border-slate-100 bg-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">{{ $volunteer->name }}</h2>
                        <p class="text-slate-500 text-xs font-bold mt-1 uppercase tracking-wide bg-slate-100 text-slate-600 px-2 py-0.5 rounded inline-block">ID Relawan: {{ $volunteer->volunteer_code }}</p>
                    </div>
                    <div>
                        @if($volunteer->status === 'PENDING')
                            <span class="text-sm font-bold text-amber-600">Pending</span>
                        @elseif($volunteer->status === 'APPROVED')
                            <span class="text-sm font-bold text-emerald-600">Approved</span>
                        @elseif($volunteer->status === 'FIRED')
                            <span class="text-sm font-bold text-rose-600">Nonaktif</span>
                        @else
                            <span class="text-sm font-bold text-slate-500">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                {{-- section data diri relawan --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="bi bi-person-vcard" style="color: #3B6FE8;"></i> Data Diri
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Nama Lengkap</span>
                            <span class="text-sm font-medium text-slate-900">{{ $volunteer->name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Nomor Telepon</span>
                            <span class="text-sm font-medium text-slate-900">{{ $volunteer->phone_number }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Alamat Domisili</span>
                            <span class="text-sm font-medium text-slate-900 text-right max-w-xs">{{ $volunteer->address }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Terdaftar</span>
                            <span class="text-sm font-medium text-slate-900">{{ $volunteer->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($volunteer->status === 'APPROVED')
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-sm text-slate-600">Bencana Tugas</span>
                                <span class="text-sm font-medium text-slate-900">{{ $volunteer->disaster->title ?? 'Belum ditugaskan' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-100">
                                <span class="text-sm text-slate-600">Posko Evakuasi</span>
                                <span class="text-sm font-medium text-slate-900">{{ $volunteer->assignment ?? 'Belum ditugaskan' }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-sm text-slate-600">Status Konfirmasi</span>
                                <span class="text-sm font-medium">
                                    @if($volunteer->assignment_status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="bi bi-hourglass-split text-[10px]"></i> Menunggu Konfirmasi
                                        </span>
                                    @elseif($volunteer->assignment_status === 'accepted')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="bi bi-check-circle-fill text-[10px]"></i> Diterima
                                        </span>
                                    @elseif($volunteer->assignment_status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="bi bi-x-circle-fill text-[10px]"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </span>
                            </div>
                            @if($volunteer->assignment_status === 'rejected' && $volunteer->assignment_rejection_reason)
                                <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-100">
                                    <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1">Alasan Penolakan:</p>
                                    <p class="text-xs text-rose-800">{{ $volunteer->assignment_rejection_reason }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- section skill relawan --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="bi bi-star-fill" style="color: #3B6FE8;"></i> Keahlian / Spesialisasi
                    </h3>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold"
                              style="background: #E4F0F6; color: #3B6FE8;">
                            {{ $volunteer->skill }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- sidebar tindakan admin --}}
    @if(strtolower(auth()->user()->role) === 'admin')
        <div class="lg:col-span-1 space-y-6">

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-visible"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#3B6FE8] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                    <h3 class="text-sm font-semibold text-slate-900">Tindakan</h3>
                </div>

                <div class="p-5 space-y-3">

                    @if($volunteer->status === 'PENDING')
                        <p class="text-xs text-slate-500 mb-3">Setujui atau tolak pendaftaran relawan ini.</p>

                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="APPROVED">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 cursor-pointer"
                                    style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: 0 2px 8px rgba(16,185,129,0.25);">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        </form>

                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="REJECTED">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50 transition-all cursor-pointer">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        </form>
                    @elseif($volunteer->status === 'FIRED')
                        <div class="p-3 rounded-xl text-xs font-semibold text-center bg-rose-50 text-rose-700 border border-rose-100 mb-3">
                            Status: Nonaktif
                        </div>

                        {{-- form aktifkan kembali relawan --}}
                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="APPROVED">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 cursor-pointer"
                                    style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: 0 2px 8px rgba(16,185,129,0.25);">
                                <i class="bi bi-check-circle"></i> Aktifkan Kembali
                            </button>
                        </form>

                        {{-- form reset status relawan ke pending --}}
                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="PENDING">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all cursor-pointer">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset ke Pending
                            </button>
                        </form>
                    @elseif($volunteer->status === 'APPROVED')
                        <div class="p-3 rounded-xl text-xs font-semibold text-center bg-emerald-50 text-emerald-700 border border-emerald-100 mb-3">
                            Status: Approved
                        </div>

                        {{-- form reset status relawan ke pending --}}
                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="PENDING">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all cursor-pointer">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset ke Pending
                            </button>
                        </form>

                        {{-- form nonaktifkan relawan --}}
                        <form id="formNonaktifkan" action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="status" value="FIRED">
                            <button type="button" id="btnNonaktifkan"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 bg-rose-50/50 hover:bg-rose-50 border border-rose-100 rounded-lg transition-all cursor-pointer"
                                    onclick="openModal('nonaktifkan', '{{ addslashes($volunteer->name) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 shrink-0"><circle cx="10" cy="7" r="4"/><path d="M10 11c-4.42 0-8 1.79-8 4v1h9"/><line x1="15" y1="17" x2="21" y2="17"/></svg>
                                Nonaktifkan Relawan
                            </button>
                        </form>

                        @if($volunteer->disaster_id || $volunteer->assignment)
                            {{-- form hapus penugasan relawan --}}
                            <form id="formHapusPenugasan" action="{{ route('volunteer.assign', $volunteer->id) }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="disaster_id" value="">
                                <input type="hidden" name="assignment" value="">
                                <button type="button" id="btnHapusPenugasan"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 bg-rose-50/50 hover:bg-rose-50 border border-rose-100 rounded-lg transition-all cursor-pointer"
                                        onclick="openModal('hapus', '{{ addslashes($volunteer->name) }}')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 shrink-0"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9.5" y1="14.5" x2="14.5" y2="19.5"/><line x1="14.5" y1="14.5" x2="9.5" y2="19.5"/></svg>
                                    Hapus Penugasan
                                </button>
                            </form>
                        @endif
                    @elseif($volunteer->status === 'REJECTED')
                        <div class="p-3 rounded-xl text-xs font-semibold text-center bg-slate-50 text-slate-600 border border-slate-200 mb-3">
                            Status: Rejected
                        </div>

                        {{-- form reset status relawan ke pending --}}
                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="PENDING">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all cursor-pointer">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset ke Pending
                            </button>
                        </form>
                    @endif

                    {{-- form penugasan relawan --}}
                    @if($volunteer->status === 'APPROVED')
                        <div class="border-t border-slate-100 pt-4.5 mt-4.5">
                            <form action="{{ route('volunteer.assign', $volunteer->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Penugasan Relawan
                                </label>

                                <div class="space-y-3.5">
                                    {{-- custom select bencana aktif --}}
                                    <div class="relative" id="customDisasterSelect">
                                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Bencana Aktif</label>
                                        <button type="button" class="select-trigger w-full px-3 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white text-slate-750 text-left flex items-center justify-between cursor-pointer transition-all duration-200 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20">
                                            <span class="selected-text truncate {{ !$volunteer->disaster ? 'text-slate-400' : '' }}">
                                                @if($volunteer->disaster)
                                                    @php
                                                        $vCount = \App\Models\Volunteer::where('status', 'APPROVED')->where('disaster_id', $volunteer->disaster->id)->count();
                                                    @endphp
                                                    {{ $volunteer->disaster->title }} — {{ $volunteer->disaster->status_label }} ({{ $vCount }} relawan aktif)
                                                @else
                                                    — Pilih Bencana —
                                                @endif
                                            </span>
                                            <i class="bi bi-chevron-down text-[10px] text-slate-400"></i>
                                        </button>
                                        <input type="hidden" name="disaster_id" id="hiddenDisasterId" value="{{ $volunteer->disaster_id }}">
                                        
                                        <div class="select-dropdown absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 p-2.5 space-y-2 hidden">
                                            <div class="relative">
                                                <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                                <input type="text" class="select-search w-full pl-7 pr-3 py-1.5 text-[11px] border border-slate-150 rounded-lg focus:outline-none focus:border-blue-400 bg-slate-50/30" placeholder="Cari bencana...">
                                            </div>
                                            <div class="select-options max-h-40 overflow-y-auto space-y-0.5 text-xs text-slate-700">
                                                <div class="option-item px-2.5 py-1.75 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors flex items-center justify-between" data-value="">
                                                    <span class="font-medium text-slate-400">— Kosongkan Bencana —</span>
                                                </div>
                                                @foreach(\App\Models\Disaster::whereNotIn('status', ['PENDING', 'DECLINE', 'RESOLVED'])->latest()->get() as $disaster)
                                                    @php
                                                        $volunteerCount = \App\Models\Volunteer::where('status', 'APPROVED')->where('disaster_id', $disaster->id)->count();
                                                    @endphp
                                                    <div class="option-item px-2.5 py-1.75 rounded-lg hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition-colors flex items-center justify-between" 
                                                         data-value="{{ $disaster->id }}" 
                                                         data-search="{{ strtolower($disaster->title . ' ' . $disaster->status_label) }}">
                                                        <div class="min-w-0 flex-1 pr-2">
                                                            <p class="font-semibold text-slate-800 truncate">{{ $disaster->title }} — {{ $disaster->status_label }}</p>
                                                            <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $disaster->location ?? 'Lokasi tidak diketahui' }}</p>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-1.75 py-0.5 rounded-full bg-blue-50 text-blue-700 shrink-0 border border-blue-100/50">
                                                            {{ $volunteerCount }} relawan aktif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- custom select posko evakuasi --}}
                                    <div class="relative" id="customShelterSelect">
                                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Posko Evakuasi</label>
                                        <button type="button" class="select-trigger w-full px-3 py-2 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white text-slate-750 text-left flex items-center justify-between cursor-pointer transition-all duration-200 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20">
                                            <span class="selected-text truncate {{ !$volunteer->assignment ? 'text-slate-400' : '' }}">
                                                @if($volunteer->assignment)
                                                    @php
                                                        $sCount = \App\Models\Volunteer::where('status', 'APPROVED')->where('assignment', $volunteer->assignment)->count();
                                                    @endphp
                                                    {{ $volunteer->assignment }} ({{ $sCount }} relawan aktif)
                                                @else
                                                    — Pilih Posko —
                                                @endif
                                            </span>
                                            <i class="bi bi-chevron-down text-[10px] text-slate-400"></i>
                                        </button>
                                        <input type="hidden" name="assignment" id="hiddenAssignment" value="{{ $volunteer->assignment }}">
                                        
                                        <div class="select-dropdown absolute left-0 right-0 bottom-full mb-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 p-2.5 space-y-2 hidden">
                                            <div class="relative">
                                                <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                                <input type="text" class="select-search w-full pl-7 pr-3 py-1.5 text-[11px] border border-slate-150 rounded-lg focus:outline-none focus:border-blue-400 bg-slate-50/30" placeholder="Cari posko...">
                                            </div>
                                            <div class="select-options max-h-40 overflow-y-auto space-y-0.5 text-xs text-slate-700">
                                                <div class="option-item px-2.5 py-1.75 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors flex items-center justify-between" data-value="">
                                                    <span class="font-medium text-slate-400">— Kosongkan Posko —</span>
                                                </div>
                                                @foreach(\App\Models\Shelter::all() as $shelter)
                                                    @php
                                                        $shelterCount = \App\Models\Volunteer::where('status', 'APPROVED')->where('assignment', $shelter->name)->count();
                                                    @endphp
                                                    <div class="option-item px-2.5 py-1.75 rounded-lg hover:bg-purple-50 hover:text-purple-700 cursor-pointer transition-colors flex items-center justify-between" 
                                                         data-value="{{ $shelter->name }}" 
                                                         data-search="{{ strtolower($shelter->name) }}">
                                                        <div class="min-w-0 flex-1 pr-2">
                                                            <p class="font-semibold text-slate-800 truncate">{{ $shelter->name }}</p>
                                                            <p class="text-[10px] text-slate-405 mt-0.5 truncate">Kapasitas: {{ $shelter->capacity }}</p>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-1.75 py-0.5 rounded-full bg-purple-50 text-purple-700 shrink-0 border border-purple-100/50">
                                                            {{ $shelterCount }} relawan aktif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-1.5">
                                        <button type="submit"
                                                class="w-full py-2.5 text-xs font-semibold text-white rounded-xl transition-all hover:opacity-95 flex items-center justify-center gap-1.5 cursor-pointer shadow-md shadow-blue-500/10 animate-fade-in"
                                                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);">
                                            <i class="bi bi-save text-xs"></i> Simpan Penugasan
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 leading-normal">Pilih bencana aktif dan posko evakuasi tempat relawan akan ditugaskan.</p>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    @endif

</div>

{{-- modal konfirmasi tindakan --}}
<div id="confirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden">
    {{-- backdrop modal --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm sigma-modal-backdrop" onclick="closeModal()"></div>

    {{-- content modal --}}
    <div class="relative bg-white w-full max-w-[380px] rounded-3xl px-7 pt-8 pb-7 sigma-modal-content"
         style="box-shadow: 0 -4px 40px rgba(10,15,30,0.10), 0 16px 50px rgba(10,15,30,0.15);">

        {{-- icon modal --}}
        <div id="modalIconWrap" class="mb-5 text-rose-500 sigma-modal-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                 stroke-linecap="round" stroke-linejoin="round" class="w-9 h-9">
                <circle cx="10" cy="7" r="4"/>
                <path d="M10 11c-4.42 0-8 1.79-8 4v1h9"/>
                <line x1="15" y1="17" x2="21" y2="17"/>
            </svg>
        </div>

        {{-- judul dan deskripsi modal --}}
        <h4 id="modalTitle" class="text-[17px] font-bold text-slate-900 leading-snug mb-1.5">Nonaktifkan Relawan</h4>
        <p id="modalDesc" class="text-sm text-slate-500 leading-relaxed mb-5">Relawan tidak akan bisa bertugas sampai diaktifkan kembali oleh admin.</p>

        {{-- warning text modal --}}
        <div class="border-l-[3px] border-rose-400 pl-3.5 pr-3 py-2.5 mb-7 bg-rose-50/50 rounded-r-xl">
            <p id="modalWarningText" class="text-[12px] text-rose-700 leading-relaxed"></p>
        </div>

        {{-- button aksi modal --}}
        <div class="flex gap-3">
            <button type="button" onclick="closeModal()"
                    class="flex-1 py-3 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-150 rounded-2xl transition-colors duration-150 cursor-pointer">
                Batal
            </button>
            <button type="button" id="modalConfirmBtn"
                    class="flex-1 py-3 text-sm font-semibold text-white rounded-2xl transition-all duration-150 cursor-pointer active:scale-[0.98] hover:brightness-105"
                    style="background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);">
                <span id="modalConfirmLabel">Ya, Nonaktifkan</span>
            </button>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script>
    // pasang config dan handle animasi buka/tutup modal konfirmasi
    let pendingFormId = null;

    const iconSvgs = {
        nonaktifkan: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="w-9 h-9">
            <circle cx="10" cy="7" r="4"/>
            <path d="M10 11c-4.42 0-8 1.79-8 4v1h9"/>
            <line x1="15" y1="17" x2="21" y2="17"/>
        </svg>`,
        hapus: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="w-9 h-9">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <line x1="9.5" y1="14.5" x2="14.5" y2="19.5"/>
            <line x1="14.5" y1="14.5" x2="9.5" y2="19.5"/>
        </svg>`,
    };

    const modalConfig = {
        nonaktifkan: {
            title: 'Nonaktifkan Relawan',
            desc: 'Relawan tidak akan bisa bertugas sampai diaktifkan kembali oleh admin.',
            warning: 'Status relawan berubah menjadi <strong>Nonaktif</strong>. Tindakan ini dapat dibatalkan kapan saja.',
            iconKey: 'nonaktifkan',
            confirmLabel: 'Ya, Nonaktifkan',
            formId: 'formNonaktifkan',
        },
        hapus: {
            title: 'Hapus Penugasan',
            desc: 'Relawan akan dikeluarkan dari bencana dan posko yang sedang ditangani.',
            warning: 'Penugasan bencana dan posko akan <strong>dihapus</strong>. Status relawan tetap aktif, namun perlu ditugaskan ulang.',
            iconKey: 'hapus',
            confirmLabel: 'Ya, Hapus Penugasan',
            formId: 'formHapusPenugasan',
        },
    };

    function openModal(type, name) {
        const cfg = modalConfig[type];
        if (!cfg) return;

        pendingFormId = cfg.formId;

        const displayName = name ? `“${name}”` : 'relawan ini';
        const desc = type === 'nonaktifkan'
            ? `Relawan ${displayName} tidak akan bisa bertugas sampai diaktifkan kembali oleh admin.`
            : `Relawan ${displayName} akan dikeluarkan dari bencana dan posko yang sedang ditangani.`;

        document.getElementById('modalTitle').textContent      = cfg.title;
        document.getElementById('modalDesc').textContent       = desc;
        document.getElementById('modalWarningText').innerHTML  = cfg.warning;
        document.getElementById('modalConfirmLabel').textContent = cfg.confirmLabel;
        document.getElementById('modalIconWrap').innerHTML     = iconSvgs[cfg.iconKey];

        const modal = document.getElementById('confirmModal');
        const backdrop = modal.querySelector('.sigma-modal-backdrop');
        const content = modal.querySelector('.sigma-modal-content');
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.add('is-visible');
            content.classList.add('is-visible');
        });
    }

    function closeModal() {
        const modal = document.getElementById('confirmModal');
        const backdrop = modal.querySelector('.sigma-modal-backdrop');
        const content = modal.querySelector('.sigma-modal-content');
        backdrop.classList.remove('is-visible');
        backdrop.classList.add('is-hiding');
        content.classList.remove('is-visible');
        content.classList.add('is-hiding');
        setTimeout(() => {
            modal.classList.add('hidden');
            backdrop.classList.remove('is-hiding');
            content.classList.remove('is-hiding');
        }, 300);
        pendingFormId = null;
    }

    document.getElementById('modalConfirmBtn')?.addEventListener('click', () => {
        if (pendingFormId) {
            document.getElementById(pendingFormId)?.submit();
        }
        closeModal();
    });

    // tutup modal pas user klik tombol escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    // handling dropdown select kustom (searchable)
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.select-trigger').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const wrapper = trigger.parentElement;
                const dropdown = wrapper.querySelector('.select-dropdown');
                const searchInput = wrapper.querySelector('.select-search');
                
                // tutup dropdown select kustom lainnya
                document.querySelectorAll('.select-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.add('hidden');
                });

                dropdown.classList.toggle('hidden');
                if (!dropdown.classList.contains('hidden')) {
                    searchInput?.focus();
                }
            });
        });

        document.querySelectorAll('.select-search').forEach(search => {
            search.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                const optionsList = search.parentElement.nextElementSibling;
                const options = optionsList.querySelectorAll('.option-item');

                options.forEach(opt => {
                    const searchStr = opt.getAttribute('data-search') || '';
                    if (!query || searchStr.includes(query) || opt.getAttribute('data-value') === '') {
                        opt.style.display = '';
                    } else {
                        opt.style.display = 'none';
                    }
                });
            });

            search.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') e.preventDefault();
            });
        });

        document.querySelectorAll('.option-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const wrapper = item.closest('.relative');
                const hiddenInput = wrapper.querySelector('input[type="hidden"]');
                const selectedText = wrapper.querySelector('.selected-text');
                const dropdown = wrapper.querySelector('.select-dropdown');
                const searchInput = wrapper.querySelector('.select-search');

                const val = item.getAttribute('data-value');
                hiddenInput.value = val;

                // update teks yang kelihatan di tombol trigger select
                if (val === '') {
                    selectedText.textContent = wrapper.id === 'customDisasterSelect' ? '— Pilih Bencana —' : '— Pilih Posko —';
                    selectedText.classList.add('text-slate-400');
                } else {
                    const mainText = item.querySelector('p')?.textContent?.trim() || '';
                    const countText = item.querySelector('span')?.textContent?.trim() || '';
                    selectedText.textContent = countText ? mainText + ' (' + countText + ')' : mainText;
                    selectedText.classList.remove('text-slate-400');
                }

                dropdown.classList.add('hidden');
                if (searchInput) searchInput.value = '';
                
                // reset filter list pencarian di dropdown
                item.parentElement.querySelectorAll('.option-item').forEach(opt => opt.style.display = '');
            });
        });

        // tutup dropdown select pas user klik di luar dropdown
        document.addEventListener('click', () => {
            document.querySelectorAll('.select-dropdown').forEach(d => d.classList.add('hidden'));
        });
    });
</script>
@endsection
