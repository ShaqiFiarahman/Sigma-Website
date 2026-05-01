@extends('layout')
@section('title', 'Buat Laporan Baru')
@section('subtitle', 'Masukkan detail laporan bencana secara akurat.')

@section('content')
<div class="max-w-3xl">

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('laporan.store') }}" method="POST" id="laporanForm">
            @csrf
            
            <div class="p-6 sm:p-8 space-y-6">
                
                {{-- Judul --}}
                <div>
                    <label for="judul" class="block text-sm font-medium text-slate-900 mb-1.5">Judul Laporan <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul" required placeholder="Contoh: Banjir bandang di kawasan Perumahan Indah"
                           class="w-full px-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all placeholder:text-slate-400">
                </div>

                {{-- Lokasi --}}
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-slate-900 mb-1.5">Lokasi Kejadian <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="bi bi-geo-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="lokasi" id="lokasi" required placeholder="Contoh: Jl. Merdeka No. 10, Bantul, DIY"
                               class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-slate-900 mb-1.5">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="5" required placeholder="Ceritakan detail kejadian secara kronologis..."
                              class="w-full px-4 py-3 text-sm border border-slate-300 rounded-lg focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all resize-y placeholder:text-slate-400"></textarea>
                    <p class="text-xs text-slate-500 mt-2">Pastikan mencakup informasi terkait waktu, korban, dan dampak kerusakan jika diketahui.</p>
                </div>

                {{-- Upload --}}
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1.5">Dokumentasi Foto <span class="text-slate-500 font-normal">(Opsional)</span></label>
                    <label for="foto" class="flex flex-col items-center justify-center w-full h-32 px-4 transition-colors bg-slate-50 border-2 border-slate-300 border-dashed rounded-lg appearance-none cursor-pointer hover:border-primary-400 hover:bg-slate-100 focus:outline-none" id="uploadLabel">
                        <input type="file" id="foto" accept="image/*" class="hidden">
                        
                        <div id="uploadPlaceholder" class="flex flex-col items-center space-y-2 text-center">
                            <i class="bi bi-cloud-arrow-up text-2xl text-slate-400"></i>
                            <span class="text-sm text-slate-600 font-medium">Klik untuk unggah <span class="font-normal text-slate-500">atau seret file ke sini</span></span>
                            <span class="text-xs text-slate-400">PNG, JPG, WEBP maks 5MB</span>
                        </div>

                        <div id="uploadPreview" class="hidden flex-col items-center justify-center w-full h-full">
                            <p id="previewName" class="text-sm font-medium text-primary-600 truncate max-w-xs"></p>
                            <p class="text-xs text-slate-500 mt-1">File terpilih. Klik untuk mengganti.</p>
                        </div>
                    </label>
                </div>

            </div>

            {{-- Footer / Actions --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3 rounded-b-xl">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-4 py-2 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-colors flex items-center">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const fileInput   = document.getElementById('foto');
    const placeholder = document.getElementById('uploadPlaceholder');
    const previewBox  = document.getElementById('uploadPreview');
    const previewName = document.getElementById('previewName');

    fileInput?.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            previewName.textContent = file.name;
            placeholder.classList.add('hidden');
            previewBox.classList.remove('hidden');
            previewBox.classList.add('flex');
        }
    });

    const form      = document.getElementById('laporanForm');
    const submitBtn = document.getElementById('submitBtn');
    form?.addEventListener('submit', () => {
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin-fast mr-2"></i> Mengirim...';
        submitBtn.disabled  = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
    });
</script>
@endsection
