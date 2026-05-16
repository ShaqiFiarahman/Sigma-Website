@extends('layouts.app')
@section('title', 'Pendaftaran Relawan')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Back link --}}
    <div class="mb-5">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold transition-colors hover:opacity-70" style="color: #6650a4;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Page Title --}}
    <h1 class="text-3xl font-bold mb-6" style="color: #1D1B20;">Pendaftaran Relawan</h1>

    {{-- Section Title --}}
    <h2 class="text-lg font-bold mb-4" style="color: #1D1B20;">Lengkapi Data Relawan</h2>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i> Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Flash error (already registered, etc.) --}}
    @if(session('error'))
        <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('volunteer.store') }}" method="POST" id="volunteerForm" class="space-y-4">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <input type="text" name="name" id="name" required
                   value="{{ old('name') }}"
                   placeholder="Nama Lengkap"
                   class="w-full px-4 py-4 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all placeholder:text-slate-500 text-slate-900 bg-white">
        </div>

        {{-- Keahlian / Spesialisasi (single select dropdown) --}}
        <div class="relative">
            <label for="skill" class="absolute -top-2 left-3 px-1 text-xs font-medium bg-white"
                   style="color: #6650a4; z-index: 1;">
                Keahlian / Spesialisasi
            </label>
            <select name="skill" id="skill" required
                    class="w-full px-4 py-4 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all text-slate-900 bg-white appearance-none pr-10">
                @foreach($skills as $value => $label)
                    <option value="{{ $value }}" {{ old('skill', 'MEDIS') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
        </div>

        {{-- Alamat Domisili --}}
        <div>
            <input type="text" name="address" id="address" required
                   value="{{ old('address') }}"
                   placeholder="Alamat Domisili"
                   class="w-full px-4 py-4 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all placeholder:text-slate-500 text-slate-900 bg-white">
        </div>

        {{-- Nomor Telepon --}}
        <div>
            <input type="tel" name="phone_number" id="phone_number" required
                   value="{{ old('phone_number') }}"
                   placeholder="Nomor Telepon"
                   class="w-full px-4 py-4 text-sm border border-slate-300 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all placeholder:text-slate-500 text-slate-900 bg-white">
        </div>

        {{-- Submit Button --}}
        <button type="button" id="submitBtn"
                class="w-full py-4 mt-4 text-sm font-bold text-white rounded-xl transition-all duration-200 hover:opacity-90"
                style="background: #1e3a5f;">
            Kirim Pendaftaran
        </button>

        {{-- Disclaimer text --}}
        <p class="text-xs text-center mt-4" style="color: #625b71;">
            Relawan yang terdaftar akan diverifikasi oleh BNPB sebelum mendapatkan penugasan resmi di lapangan.
        </p>
    </form>

    {{-- Confirmation Modal --}}
    <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.4); backdrop-filter: blur(8px);">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl">
            {{-- Icon --}}
            <div class="flex justify-center mb-4">
                <div class="w-18 h-18 rounded-full flex items-center justify-center" style="background: #EADDFF;">
                    <i class="bi bi-person-check-fill text-3xl" style="color: #6650a4;"></i>
                </div>
            </div>

            {{-- Title --}}
            <h3 class="text-lg font-bold text-center mb-2" style="color: #1D1B20;">
                Konfirmasi Pendaftaran
            </h3>

            {{-- Message --}}
            <p class="text-sm text-center text-slate-600 mb-4">
                Apakah Anda yakin ingin mendaftar sebagai relawan SIGMA?
            </p>

            {{-- Warning Box --}}
            <div class="p-3 rounded-xl mb-5"
                 style="background: rgba(220, 38, 38, 0.06); border: 1px solid rgba(220, 38, 38, 0.3);">
                <p class="text-xs text-center font-semibold" style="color: #B71C1C;">
                    Wajib menjalankan tugas sampai selesai setelah menerima penugasan resmi.
                </p>
            </div>

            {{-- Actions --}}
            <button type="submit" form="volunteerForm"
                    class="w-full py-3 mb-2 text-sm font-bold text-white rounded-xl transition-all"
                    style="background: #1e3a5f;">
                Ya, Saya Yakin
            </button>
            <button type="button" id="cancelBtn"
                    class="w-full py-3 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                Batal
            </button>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const modal     = document.getElementById('confirmModal');
    const form      = document.getElementById('volunteerForm');

    submitBtn.addEventListener('click', () => {
        // Validate form first
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        modal.classList.remove('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Close on background click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
</script>
@endsection
