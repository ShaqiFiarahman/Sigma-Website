<style>
    .u-footer {
        position: relative;
        background-color: white;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .u-footer::before {
        content: '';
        position: absolute;
        top: -85px;
        left: 50%;
        transform: translateX(-50%);
        width: 140%;
        height: 100px;
        background-color: #F0F4F8;
        border-radius: 50%;
        pointer-events: none;
        box-shadow: 0 12px 28px rgba(59, 111, 232, 0.12);
        filter: blur(3px);
    }
</style>
<footer class="py-10 u-footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-xs text-slate-500 mb-8">
            <!-- Column 1: Brand & Info -->
            <div>
                <span class="font-extrabold text-[#2B52C3] text-sm block mb-2 tracking-wide">SIGMA</span>
                <p class="text-slate-600 mb-4 leading-relaxed max-w-xs">Sistem Informasi Gawat Darurat dan Mitigasi Bencana</p>
                <p class="font-semibold text-slate-800 mb-1">Data didukung:</p>
                <p class="text-slate-500">BMKG &bull; BNPB &bull; Laporan Masyarakat</p>
            </div>

            <!-- Column 2: Menu -->
            <div class="md:text-center">
                <span class="font-bold text-slate-900 text-sm block mb-3">Menu</span>
                <div class="flex flex-col gap-2 text-slate-600 md:items-center">
                    <a href="{{ route('dashboard') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Dashboard</a>
                    <a href="{{ route('laporan.index') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Laporan</a>
                    <a href="{{ route('panduan') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Panduan</a>
                </div>
            </div>

            <!-- Column 3: Tim -->
            <div class="md:text-right">
                <span class="font-bold text-slate-900 text-sm block mb-3">Tim</span>
                <div class="inline-flex gap-x-4 text-slate-600">
                    <div class="flex flex-col gap-y-2 text-left">
                        <span class="font-medium">Fadel</span>
                        <span class="font-medium">Fathoni</span>
                    </div>
                    <div class="flex flex-col gap-y-2 text-right">
                        <span class="font-medium">Fandhi</span>
                        <span class="font-medium">Huda</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-slate-100 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-400 text-[11px] font-medium">&copy;2026 SIGMA &bull; Data: BMKG & BNPB</p>
        </div>
    </div>
</footer>
