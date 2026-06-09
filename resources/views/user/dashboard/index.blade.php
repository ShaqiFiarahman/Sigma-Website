@extends('layouts.app')
@section('title', 'Peta Bencana Real-Time & Informasi Mitigasi')

@section('content')

    {{-- WELCOME BANNER --}}
    @include('partials._welcome-banner')

    <div class="space-y-8 pb-6">

        {{-- Warning Banner --}}
        @include('partials._warning-banner')

        {{-- VOLUNTEER SECTION --}}
        @if($volunteerData)
            @if($volunteerData->status === 'PENDING')
                <section class="animate-fade-up" style="animation-delay: 0.05s;">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 relative">
                        <button onclick="this.parentElement.parentElement.remove()" class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                <i class="bi bi-hourglass-split text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Pendaftaran relawan sedang diproses</p>
                                <p class="text-xs text-slate-500 mt-1">Menunggu verifikasi admin. Estimasi 1–3 hari kerja.</p>
                                <p class="text-[11px] text-slate-400 mt-2">Didaftarkan {{ $volunteerData->created_at?->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

            @elseif($volunteerData->status === 'FIRED')
                <section class="animate-fade-up" style="animation-delay: 0.05s;">
                    <div class="bg-white rounded-2xl border border-slate-100 p-6 relative">
                        <button onclick="this.parentElement.parentElement.remove()" class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                <i class="bi bi-person-slash text-slate-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 uppercase">Status relawan dinonaktifkan</p>
                                <p class="text-xs text-slate-500 mt-1">Akun relawan Anda telah dinonaktifkan oleh admin. Hubungi admin untuk informasi lebih lanjut.</p>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endif

        {{-- News Section --}}
        @include('partials._news-section')

        {{-- Menu Layanan --}}
        @include('partials._menu-section')

        {{-- Peta Bencana Section --}}
        <x-disaster-map />
    </div>

@section('footer')
    <x-footer />
@endsection



@endsection
