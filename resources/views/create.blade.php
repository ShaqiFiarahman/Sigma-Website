@extends('layout')
@section('title', 'Buat Laporan Baru')
@section('subtitle', 'Masukkan detail laporan bencana secara akurat.')

@section('content')
<div class="max-w-3xl">

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

        {{-- Form header gradient --}}
        <div class="px-8 py-6 border-b border-slate-100"
             style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 100%);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(228,240,246,0.15); border: 1px solid rgba(228,240,246,0.2);">
                    <i class="bi bi-file-earmark-plus text-white"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">Formulir Laporan Baru</h2>
                    <p class="text-xs mt-0.5" style="color: rgba(228,240,246,0.5);">Lengkapi semua kolom yang diperlukan</p>
                </div>
            </div>
        </div>

        <form action="{{ route('laporan.store') }}" method="POST" id="laporanForm">
            @csrf
            
            <div class="p-7 space-y-6">
                
                {{-- Judul --}}
                <div>
                    <label for="judul" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Judul Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul" id="judul" required
                           placeholder="Contoh: Banjir bandang di kawasan Perumahan Indah"
                           class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">
                </div>

                {{-- Lokasi --}}
                <div>
                    <label for="lokasi" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Lokasi Kejadian <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="bi bi-geo-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="lokasi" id="lokasi" required
                               placeholder="Contoh: Jl. Merdeka No. 10, Bantul, DIY"
                               class="w-full pl-10 pr-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="deskripsi" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Deskripsi Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="5" required
                              placeholder="Ceritakan detail kejadian secara kronologis..."
                              class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-y placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white"></textarea>
                    <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                        <i class="bi bi-info-circle"></i>
                        Pastikan mencakup informasi terkait waktu, korban, dan dampak kerusakan jika diketahui.
                    </p>
                </div>

                {{-- Upload --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                        Dokumentasi Foto <span class="text-red-500">*</span>
                    </label>
                    <label for="foto"
                           class="flex flex-col items-center justify-center w-full h-36 px-4 transition-all duration-200 border-2 border-slate-200 border-dashed rounded-xl appearance-none cursor-pointer hover:border-blue-400 focus:outline-none"
                           id="uploadLabel"
                           style="background: #F8FAFC;">
                        <input type="file" id="foto" name="foto" accept="image/*" class="hidden" required>
                        
                        <div id="uploadPlaceholder" class="flex flex-col items-center space-y-2 text-center">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-1" style="background: #E4F0F6;">
                                <i class="bi bi-cloud-arrow-up text-xl" style="color: #1e3a8a;"></i>
                            </div>
                            <span class="text-sm text-slate-600 font-medium">Klik untuk unggah <span class="font-normal text-slate-400">atau seret file ke sini</span></span>
                            <span class="text-xs text-slate-400">PNG, JPG, WEBP maks 5MB</span>
                        </div>

                        <div id="uploadPreview" class="hidden flex-col items-center justify-center w-full h-full">
                            <i class="bi bi-file-earmark-image text-3xl text-blue-400 mb-2"></i>
                            <p id="previewName" class="text-sm font-medium text-blue-600 truncate max-w-xs"></p>
                            <p class="text-xs text-slate-400 mt-1">File terpilih. Klik untuk mengganti.</p>
                        </div>
                    </label>
                </div>

            </div>

            {{-- Footer / Actions --}}
            <div class="px-7 py-5 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl" style="background: #FAFBFD;">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                        class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2"
                        style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(10,15,30,0.2);">
                    <i class="bi bi-send"></i> Kirim Laporan
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
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin-fast"></i> Mengirim...';
        submitBtn.disabled  = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
    });
</script>
@endsection
