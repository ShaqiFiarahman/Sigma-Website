@extends('layout')
@section('title', 'Buat Laporan Baru')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Intro Banner --}}
    <div class="flex items-start gap-4 bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 mb-5">
        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white text-base shrink-0">
            <i class="bi bi-broadcast"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-blue-900">Laporkan Kejadian Bencana</p>
            <p class="text-xs text-blue-500 leading-relaxed mt-0.5">
                Isi formulir dengan informasi akurat. Setiap laporan akan diverifikasi oleh tim admin sebelum ditindaklanjuti.
            </p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-sm">
                <i class="bi bi-pencil-fill"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900 leading-none">Form Pelaporan Bencana</p>
                <p class="text-xs text-slate-400 mt-0.5">Kolom bertanda <span class="text-red-500 font-bold">*</span> wajib diisi</p>
            </div>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('laporan.store') }}" method="POST" id="laporanForm" class="p-7 space-y-6">
            @csrf

            {{-- Judul --}}
            <div>
                <label for="judul" class="block text-xs font-bold text-slate-700 mb-2">
                    Judul Laporan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="judul" id="judul" required
                       placeholder="Contoh: Banjir bandang di kawasan Perumahan Indah"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50
                              text-sm font-medium text-slate-900 placeholder:text-slate-400
                              focus:outline-none focus:border-blue-400 focus:bg-white
                              focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]
                              transition-all duration-200">
                <p class="text-xs text-slate-400 mt-1.5">Gunakan judul yang singkat, jelas, dan deskriptif.</p>
            </div>

            {{-- Lokasi --}}
            <div>
                <label for="lokasi" class="block text-xs font-bold text-slate-700 mb-2">
                    Lokasi Kejadian <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <i class="bi bi-geo-alt-fill absolute left-4 top-1/2 -translate-y-1/2 text-red-400 text-sm pointer-events-none"></i>
                    <input type="text" name="lokasi" id="lokasi" required
                           placeholder="Contoh: Jl. Merdeka No. 10, Bantul, DIY"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50
                                  text-sm font-medium text-slate-900 placeholder:text-slate-400
                                  focus:outline-none focus:border-blue-400 focus:bg-white
                                  focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]
                                  transition-all duration-200">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-2">
                    Deskripsi Kejadian <span class="text-red-500">*</span>
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="6" required
                          placeholder="Ceritakan detail kejadian secara singkat dan jelas. Sertakan: kondisi terkini, jumlah korban, dan bantuan yang dibutuhkan..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50
                                 text-sm font-medium text-slate-900 placeholder:text-slate-400
                                 focus:outline-none focus:border-blue-400 focus:bg-white
                                 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]
                                 transition-all duration-200 resize-none"></textarea>
                <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                    <i class="bi bi-lightbulb text-amber-400"></i>
                    Sertakan: kondisi terkini · jumlah korban · kebutuhan mendesak
                </p>
            </div>

            {{-- Foto Upload --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    Dokumentasi Foto <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <label for="foto"
                       class="relative flex flex-col items-center justify-center gap-3
                              w-full border-2 border-dashed border-slate-200 rounded-2xl p-10
                              bg-slate-50 cursor-pointer group
                              hover:border-blue-400 hover:bg-blue-50 transition-all duration-200"
                       id="uploadLabel">
                    <input type="file" id="foto" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">

                    {{-- Placeholder state --}}
                    <div id="uploadPlaceholder" class="flex flex-col items-center gap-2 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-200 group-hover:bg-blue-100
                                    flex items-center justify-center text-3xl text-slate-400 group-hover:text-blue-500
                                    transition-colors duration-200">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-600 group-hover:text-blue-600 transition-colors">
                                <span class="text-blue-600">Klik untuk unggah</span> atau seret foto ke sini
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">PNG, JPG, WEBP — Maks. 5 MB</p>
                        </div>
                    </div>

                    {{-- Preview state --}}
                    <div id="uploadPreview" class="hidden flex-col items-center gap-2 w-full">
                        <img id="previewImg" src="" alt="Preview"
                             class="w-full max-h-48 object-contain rounded-xl">
                        <p id="previewName" class="text-xs text-slate-500 font-medium"></p>
                    </div>
                </label>
            </div>

            {{-- Divider --}}
            <div class="border-t border-slate-100"></div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200
                          text-slate-500 text-sm font-semibold bg-transparent
                          hover:bg-slate-100 hover:border-slate-300 transition-all">
                    <i class="bi bi-x"></i> Batal
                </a>
                <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 text-white
                               text-sm font-bold shadow-[0_2px_8px_rgba(29,78,216,0.22)]
                               hover:bg-blue-700 hover:-translate-y-0.5
                               hover:shadow-[0_4px_14px_rgba(29,78,216,0.3)]
                               transition-all duration-200">
                    <i class="bi bi-send-fill"></i> Kirim Laporan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // File preview
    const fileInput   = document.getElementById('foto');
    const placeholder = document.getElementById('uploadPlaceholder');
    const previewBox  = document.getElementById('uploadPreview');
    const previewImg  = document.getElementById('previewImg');
    const previewName = document.getElementById('previewName');

    fileInput?.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src  = e.target.result;
                previewName.textContent = file.name;
                placeholder.classList.add('hidden');
                previewBox.classList.remove('hidden');
                previewBox.classList.add('flex');
            };
            reader.readAsDataURL(file);
        }
    });

    // Submit loading
    const form      = document.getElementById('laporanForm');
    const submitBtn = document.getElementById('submitBtn');
    form?.addEventListener('submit', () => {
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin-fast"></i> Mengirim...';
        submitBtn.disabled  = true;
        submitBtn.style.opacity = '0.7';
    });
</script>
@endsection
