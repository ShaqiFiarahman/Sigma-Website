@extends('layouts.app')
@section('title', 'Detail Relawan')

@section('page-actions')
    <a href="{{ route('volunteer.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
        <i class="bi bi-arrow-left text-xs"></i> Kembali
    </a>
@endsection

@section('content')

@if(session('msg'))
    <div class="mb-5 p-4 rounded-xl flex items-center gap-3 text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
        <i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('msg') }}
        <button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri: Detail --}}
    <div class="lg:col-span-2 space-y-6">

        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
             style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

            {{-- Header --}}
            <div class="px-8 py-6 border-b border-slate-100"
                 style="background: linear-gradient(135deg, #6650a4 0%, #533f8a 100%);">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $volunteer->name }}</h2>
                        <p class="text-white/70 text-sm mt-1">{{ $volunteer->phone_number }}</p>
                    </div>
                    <div>
                        @if($volunteer->status === 'PENDING')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending
                            </span>
                        @elseif($volunteer->status === 'APPROVED')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Approved
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-400/20 text-red-300 border border-red-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Rejected
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Data Diri --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="bi bi-person-vcard" style="color: #6650a4;"></i> Data Diri
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
                        <div class="flex justify-between py-2">
                            <span class="text-sm text-slate-600">Terdaftar</span>
                            <span class="text-sm font-medium text-slate-900">{{ $volunteer->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Keahlian --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="bi bi-star-fill" style="color: #6650a4;"></i> Keahlian / Spesialisasi
                    </h3>
                    <div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold"
                              style="background: #EADDFF; color: #6650a4;">
                            {{ $volunteer->skill }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Kanan: Panel Aksi --}}
    @if(in_array(auth()->user()->role, ['admin', 'BNPB']))
        <div class="lg:col-span-1 space-y-6">

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #EADDFF;">
                        <i class="bi bi-shield-check text-xs" style="color: #6650a4;"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900">Tindakan</h3>
                </div>

                <div class="p-5 space-y-3">

                    @if($volunteer->status === 'PENDING')
                        <p class="text-xs text-slate-500 mb-3">Setujui atau tolak pendaftaran relawan ini.</p>

                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="status" value="APPROVED">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5"
                                    style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: 0 2px 8px rgba(16,185,129,0.25);">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        </form>

                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="REJECTED">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50 transition-all">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        </form>
                    @else
                        <div class="p-3 rounded-xl text-xs font-medium text-center"
                             style="{{ $volunteer->status === 'APPROVED' ? 'background: #E8F5E9; color: #1B5E20; border: 1px solid #C8E6C9;' : 'background: #FCE4EC; color: #880E4F; border: 1px solid #F8BBD0;' }}">
                            Status: <span class="font-bold">{{ $volunteer->status_label }}</span>
                        </div>

                        {{-- Reset to Pending --}}
                        <form action="{{ route('volunteer.update_status', $volunteer->id) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="status" value="PENDING">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset ke Pending
                            </button>
                        </form>
                    @endif

                    {{-- Penugasan --}}
                    @if($volunteer->status === 'APPROVED')
                        <div class="border-t border-slate-100 pt-3 mt-3">
                            <form action="{{ route('volunteer.assign', $volunteer->id) }}" method="POST">
                                @csrf
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Penugasan Lokasi
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="assignment" placeholder="Contoh: Posko Bantul"
                                           value="{{ $volunteer->assignment }}"
                                           class="flex-1 px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all">
                                    <button type="submit"
                                            class="px-3 py-2 text-xs font-semibold text-white rounded-lg transition-all hover:opacity-90"
                                            style="background: #6650a4;">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    @endif

</div>

@endsection
