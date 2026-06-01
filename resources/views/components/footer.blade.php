@if(strtolower(auth()->user()->role ?? '') !== 'admin')
<footer class="py-10 u-footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 text-xs text-slate-500 mb-8">
            <!-- Column 1: Brand & Info -->
            <div>
                <span class="font-extrabold text-[#2B52C3] text-sm block mb-2 tracking-wide">SIGMA</span>
                <p class="text-slate-600 mb-4 leading-relaxed max-w-xs">Sistem Informasi Gawat Darurat dan Mitigasi Bencana</p>
                <p class="font-semibold text-slate-800 mb-1">Data didukung:</p>
                <p class="text-slate-500">BMKG &bull; BNPB &bull; Laporan Masyarakat</p>
            </div>

            <!-- Column 2: Menu -->
            <div class="flex flex-col md:items-center">
                <div class="text-left">
                    <span class="font-bold text-slate-900 text-sm block mb-3">Menu</span>
                    <div class="flex flex-col gap-2 text-slate-600">
                        @php
                            $role = strtolower(auth()->user()->role ?? '');
                        @endphp
                        
                        @if($role === 'admin')
                            <a href="{{ route('admin.laporan') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Kelola Laporan</a>
                            <a href="{{ route('volunteer.index') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Kelola Relawan</a>
                            <a href="{{ route('shelter') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Kelola Posko</a>
                        @elseif($role === 'relawan')
                            <a href="{{ route('volunteer.reports') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Laporan Tugas</a>
                            <a href="{{ route('volunteer.report.create') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Buat Laporan Tugas</a>
                            <a href="{{ route('laporan.index') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Cari Bencana</a>
                            <a href="{{ route('shelter') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Info Posko</a>
                            <a href="{{ route('panduan') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Panduan Bencana</a>
                        @else
                            {{-- Default: Masyarakat --}}
                            <a href="{{ route('laporan.create') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Buat Laporan</a>
                            <a href="{{ route('laporan.index') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Cari Bencana</a>
                            <a href="{{ route('volunteer.create') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Daftar Relawan</a>
                            <a href="{{ route('shelter') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Info Posko</a>
                            <a href="{{ route('panduan') }}" class="hover:text-[#2B52C3] font-medium transition-colors">Panduan Bencana</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Column 3: Tim -->
            <div class="flex flex-col md:items-center">
                <div class="text-left">
                    <span class="font-bold text-slate-900 text-sm block mb-3">Tim Pengembang</span>
                    <div class="inline-flex gap-x-6 text-slate-600">
                        <div class="flex flex-col gap-y-2">
                            <span class="font-medium">Fadel</span>
                            <span class="font-medium">Fathoni</span>
                        </div>
                        <div class="flex flex-col gap-y-2">
                            <span class="font-medium">Fandhi</span>
                            <span class="font-medium">Huda</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column 4: Unduh Aplikasi -->
            <div class="flex flex-col md:items-end">
                <div class="text-left flex flex-col items-start">
                    <span class="font-bold text-slate-900 text-sm block mb-3">Unduh Aplikasi</span>
                    <p class="text-slate-600 mb-4 leading-relaxed max-w-xs text-left">Dapatkan peringatan dini bencana langsung di genggaman Anda.</p>
                    <a href="#" class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition-all shadow-sm hover:shadow-md">
                        <i class="bi bi-google-play text-lg text-emerald-400"></i>
                        <div class="text-left">
                            <span class="text-[9px] text-slate-400 block font-medium leading-none font-sans uppercase tracking-wider">GET IT ON</span>
                            <span class="text-xs font-bold block leading-tight font-sans">Google Play</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-slate-200/50 pt-6 flex justify-center items-center">
            <p class="text-slate-400 text-[11px] font-medium text-center">&copy; 2026 SIGMA &bull; Data: BMKG & BNPB</p>
        </div>
    </div>
</footer>
@endif
