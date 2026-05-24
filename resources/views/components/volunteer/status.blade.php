@props(['existing'])

<div class="lg:col-span-3">
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden"
         style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">
        <div class="p-6 sm:p-8">

            @if($existing->status === 'PENDING')
                <div class="mb-6">
                    <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mb-4">
                        <i class="bi bi-clock-history text-xl text-amber-600"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Menunggu Verifikasi</h2>
                    <p class="text-sm text-slate-500 mt-1">Pendaftaran Anda sedang ditinjau oleh tim Admin.</p>
                </div>

                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex-1 h-2 rounded-full bg-blue-500"></div>
                    <div class="flex-1 h-2 rounded-full bg-amber-400"></div>
                    <div class="flex-1 h-2 rounded-full bg-slate-200"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-500 mb-6">
                    <span>Terdaftar</span><span>Verifikasi</span><span>Penugasan</span>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100 mb-5">
                    <i class="bi bi-clock text-slate-400"></i>
                    <div>
                        <p class="text-xs font-medium text-slate-700">Estimasi waktu verifikasi</p>
                        <p class="text-[11px] text-slate-500">1 – 3 hari kerja sejak pendaftaran</p>
                    </div>
                </div>

                <div class="border border-slate-100 rounded-xl divide-y divide-slate-100 mb-5">
                    <div class="flex justify-between px-4 py-3 text-xs"><span class="text-slate-500">No. Relawan</span><span class="font-mono font-medium text-slate-800">{{ $existing->volunteer_code }}</span></div>
                    <div class="flex justify-between px-4 py-3 text-xs"><span class="text-slate-500">Nama</span><span class="font-medium text-slate-800">{{ $existing->name }}</span></div>
                    <div class="flex justify-between px-4 py-3 text-xs"><span class="text-slate-500">Keahlian</span><span class="font-medium text-slate-800">{{ $existing->skill }}</span></div>
                    <div class="flex justify-between px-4 py-3 text-xs"><span class="text-slate-500">Telepon</span><span class="font-medium text-slate-800">{{ $existing->phone_number }}</span></div>
                    <div class="flex justify-between px-4 py-3 text-xs"><span class="text-slate-500">Alamat</span><span class="font-medium text-slate-800 text-right max-w-[55%]">{{ $existing->address }}</span></div>
                    <div class="flex justify-between px-4 py-3 text-xs"><span class="text-slate-500">Tanggal Daftar</span><span class="font-medium text-slate-800">{{ $existing->created_at->format('d M Y, H:i') }}</span></div>
                </div>

            @elseif($existing->status === 'APPROVED')
                <div class="rounded-2xl overflow-hidden border border-slate-200" style="background: linear-gradient(180deg, #0A0F1E 0%, #1e3a8a 40%, #ffffff 40%);">
                    <div class="px-6 pt-6 pb-8 text-center">
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.15);"><i class="bi bi-shield-check text-white text-sm"></i></div>
                            <span class="text-xs font-bold text-white/70 uppercase tracking-widest">SIGMA</span>
                        </div>
                        <p class="text-sm font-bold text-emerald-400 uppercase tracking-wide">Selamat! Anda Dinyatakan</p>
                        <p class="text-2xl font-extrabold text-white mt-1">LULUS SELEKSI RELAWAN</p>
                    </div>
                    <div class="bg-white px-6 pb-6 pt-4">
                        <div class="text-center mb-5 pb-4 border-b border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $existing->volunteer_code }}</p>
                            <p class="text-xl font-extrabold text-slate-900 mt-1">{{ $existing->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $existing->skill }}</p>
                        </div>
                        <div class="space-y-2.5 text-xs mb-5">
                            <div><p class="text-[10px] text-slate-400 uppercase tracking-wider">Telepon</p><p class="font-semibold text-slate-800">{{ $existing->phone_number }}</p></div>
                            <div><p class="text-[10px] text-slate-400 uppercase tracking-wider">Alamat</p><p class="font-semibold text-slate-800">{{ $existing->address }}</p></div>
                        </div>
                        @if($existing->assignment)
                            <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 mb-4">
                                <p class="text-[10px] text-blue-500 uppercase tracking-wider font-bold">Penugasan</p>
                                <p class="text-sm font-bold text-blue-800 mt-0.5">{{ $existing->assignment }}</p>
                            </div>
                        @else
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 mb-4">
                                <p class="text-xs text-slate-600">Silakan menunggu informasi penugasan dari Admin.</p>
                            </div>
                        @endif
                    </div>
                </div>

            @else
                <div class="rounded-2xl overflow-hidden border border-slate-200" style="background: linear-gradient(180deg, #7f1d1d 0%, #dc2626 40%, #ffffff 40%);">
                    <div class="px-6 pt-6 pb-8 text-center">
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.15);"><i class="bi bi-shield-check text-white text-sm"></i></div>
                            <span class="text-xs font-bold text-white/70 uppercase tracking-widest">SIGMA</span>
                        </div>
                        <p class="text-sm font-bold text-white/90 uppercase tracking-wide">Anda Dinyatakan Tidak Lulus</p>
                        <p class="text-2xl font-extrabold text-white mt-1">SELEKSI RELAWAN</p>
                        <p class="text-xs text-white/60 mt-2">Masih ada kesempatan di periode selanjutnya.</p>
                    </div>
                    <div class="bg-white px-6 pb-6 pt-4">
                        <div class="text-center mb-5 pb-4 border-b border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $existing->volunteer_code }}</p>
                            <p class="text-xl font-extrabold text-slate-900 mt-1">{{ $existing->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $existing->skill }}</p>
                        </div>
                        <div class="space-y-2.5 text-xs mb-5">
                            <div><p class="text-[10px] text-slate-400 uppercase tracking-wider">Telepon</p><p class="font-semibold text-slate-800">{{ $existing->phone_number }}</p></div>
                            <div><p class="text-[10px] text-slate-400 uppercase tracking-wider">Alamat</p><p class="font-semibold text-slate-800">{{ $existing->address }}</p></div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 mb-4">
                            <p class="text-xs text-slate-600">Hubungi Admin untuk informasi lebih lanjut.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-2 mt-6 pt-5 border-t border-slate-100">
                <button type="button" onclick="window.location.href='{{ route('dashboard') }}'"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 hover:border-slate-300 hover:-translate-y-0.5 hover:shadow-sm transition-all duration-200 cursor-pointer text-center">
                    Kembali
                </button>
                <button type="button"
                        onclick="window.open('https://api.whatsapp.com/send?phone=6285934415914&text={{ urlencode('Halo Admin SIGMA, saya ingin menanyakan status pendaftaran relawan atas nama ' . $existing->name . ' (' . $existing->volunteer_code . ').') }}', '_blank')"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-xl hover:-translate-y-0.5 hover:shadow-md transition-all duration-200 cursor-pointer text-center flex items-center justify-center gap-2"
                        style="background: #25D366; box-shadow: 0 2px 8px rgba(37,211,102,0.2);">
                    <i class="bi bi-whatsapp text-xs"></i> Hubungi Admin
                </button>
            </div>
        </div>
    </div>
</div>
