@extends('layouts.app')
@section('title', 'Pendaftaran Relawan')
@section('subtitle', 'Bergabung sebagai relawan tanggap bencana SIGMA.')

@section('page-actions')
    @if(!$existing)
        <x-ui.back-button :route="route('dashboard')" />
    @endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

@if($existing)
    {{-- STATUS PENDAFTARAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        @include('user._volunteer-status')
        @include('user._volunteer-info')
    </div>

@else
    @guest
        {{-- PROMPT LOGIN UNTUK GUEST --}}
        <div class="max-w-md mx-auto my-12 text-center animate-fade-up">
            <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-sm overflow-hidden"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 10px 30px -10px rgba(10,15,30,0.08);">
                <i class="bi bi-person-plus-fill text-4xl text-blue-600 mb-5 block"></i>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Pendaftaran Relawan</h2>
                <p class="text-sm text-slate-500 mb-8 leading-relaxed">Anda harus login ke dalam akun SIGMA Anda terlebih dahulu untuk mendaftar sebagai relawan tanggap bencana.</p>
                <a href="{{ route('login') }}" class="btn-primary w-full flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5"
                   style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 4px 12px rgba(30,58,138,0.25);">
                    <i class="bi bi-arrow-right text-base"></i> Login
                </a>
            </div>
        </div>
    @else
        {{-- FORM PENDAFTARAN --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- KOLOM KIRI: FORM --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

                {{-- Form Header --}}
                <div class="pl-6 pr-8 py-5 border-l-4 border-l-[#3B6FE8] border-b border-slate-100 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-600 border border-blue-100/50 shrink-0">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Formulir Pendaftaran Relawan</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Lengkapi data diri Anda untuk bergabung</p>
                        </div>
                    </div>
                </div>

                {{-- Step Progress --}}
                <div class="px-7 pt-6 pb-2">
                    <div class="flex items-center justify-between">
                        {{-- Step 1 --}}
                        <div class="flex items-center gap-2 step-indicator" data-step="1">
                            <div class="step-dot w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                                 style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); color: white; box-shadow: 0 2px 8px rgba(30,58,138,0.3);">
                                1
                            </div>
                            <span class="step-label text-xs font-semibold text-slate-800 hidden sm:inline">Data Diri</span>
                        </div>

                        {{-- Line --}}
                        <div class="flex-1 mx-3 h-0.5 rounded-full bg-slate-200 overflow-hidden">
                            <div class="step-line-1 h-full rounded-full transition-all duration-500" style="width: 0%; background: linear-gradient(90deg, #3B6FE8, #1e3a8a);"></div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex items-center gap-2 step-indicator" data-step="2">
                            <div class="step-dot w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 border-slate-200 text-slate-400 bg-white transition-all duration-300">
                                2
                            </div>
                            <span class="step-label text-xs font-medium text-slate-400 hidden sm:inline">Keahlian</span>
                        </div>

                        {{-- Line --}}
                        <div class="flex-1 mx-3 h-0.5 rounded-full bg-slate-200 overflow-hidden">
                            <div class="step-line-2 h-full rounded-full transition-all duration-500" style="width: 0%; background: linear-gradient(90deg, #3B6FE8, #1e3a8a);"></div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex items-center gap-2 step-indicator" data-step="3">
                            <div class="step-dot w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 border-slate-200 text-slate-400 bg-white transition-all duration-300">
                                3
                            </div>
                            <span class="step-label text-xs font-medium text-slate-400 hidden sm:inline">Konfirmasi</span>
                        </div>
                    </div>
                </div>

                {{-- Validation errors --}}
                @if($errors->any())
                    <div class="mx-7 mt-4 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700">
                        <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-circle-fill"></i> Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-7 mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('volunteer.store') }}" method="POST" id="volunteerForm">
                    @csrf

                    {{-- STEP 1: Data Diri --}}
                    <div class="step-content p-7 space-y-5" data-step="1">
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', auth()->user()->full_name ?? '') }}"
                                   placeholder="Masukkan nama lengkap Anda"
                                   class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                Alamat Domisili <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address" id="address" rows="3" required
                                      placeholder="Masukkan alamat lengkap tempat tinggal Anda"
                                      class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-y placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">{{ old('address') }}</textarea>
                        </div>

                        <div>
                            <label for="phone_number" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone_number" id="phone_number" required
                                   value="{{ old('phone_number') }}"
                                   placeholder="Contoh: 08123456789"
                                   class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-300 text-slate-800 bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    {{-- STEP 2: Keahlian --}}
                    <div class="step-content p-7 space-y-5 hidden" data-step="2">
                        <div>
                            <label for="skill" class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                Pilih Keahlian / Spesialisasi <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-slate-500 mb-4">Pilih bidang yang paling sesuai dengan kemampuan Anda.</p>

                            <div class="space-y-2.5" id="skillOptions">
                                @foreach($skills as $value => $label)
                                    @php
                                        $icons = [
                                            'MEDIS' => ['icon' => 'bi-heart-pulse', 'color' => 'red', 'desc' => 'Pertolongan pertama & kesehatan'],
                                            'SAR' => ['icon' => 'bi-shield-check', 'color' => 'blue', 'desc' => 'Pencarian & penyelamatan korban'],
                                            'LOGISTIK' => ['icon' => 'bi-box-seam', 'color' => 'amber', 'desc' => 'Distribusi bantuan & kebutuhan'],
                                            'KONSUMSI' => ['icon' => 'bi-cup-hot', 'color' => 'emerald', 'desc' => 'Penyediaan makanan & minuman'],
                                            'PSIKOSOSIAL' => ['icon' => 'bi-chat-heart', 'color' => 'purple', 'desc' => 'Dukungan mental & konseling'],
                                            'PENDIDIKAN' => ['icon' => 'bi-book', 'color' => 'teal', 'desc' => 'Edukasi & penyuluhan bencana'],
                                        ];
                                        $meta = $icons[$value] ?? ['icon' => 'bi-box', 'color' => 'slate', 'desc' => ''];
                                    @endphp
                                    <label class="skill-card flex items-center gap-4 p-4 rounded-xl border-2 border-slate-200 cursor-pointer transition-all duration-200 hover:border-blue-300 hover:bg-blue-50/30 {{ old('skill') === $value ? 'border-blue-500 bg-blue-50/50' : '' }}">
                                        <input type="radio" name="skill" value="{{ $value }}" class="hidden" {{ old('skill', 'MEDIS') === $value ? 'checked' : '' }}>
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-{{ $meta['color'] }}-50">
                                            <i class="bi {{ $meta['icon'] }} text-{{ $meta['color'] }}-500 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-slate-800">{{ $label }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $meta['desc'] }}</p>
                                        </div>
                                        <div class="skill-check w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all">
                                            <i class="bi bi-check text-white text-xs hidden"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: Konfirmasi --}}
                    <div class="step-content p-7 space-y-5 hidden" data-step="3">
                        <div class="text-center mb-2">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3" style="background: #E4F0F6;">
                                <i class="bi bi-clipboard-check text-2xl" style="color: #1e3a8a;"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Periksa Data Anda</h3>
                            <p class="text-xs text-slate-500 mt-1">Pastikan semua informasi sudah benar sebelum mengirim.</p>
                        </div>

                        <div class="bg-slate-50 rounded-xl border border-slate-100 p-5 space-y-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Nama</span>
                                <span class="font-semibold text-slate-800" id="confirmName">â€”</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Alamat</span>
                                <span class="font-semibold text-slate-800 text-right max-w-[60%]" id="confirmAddress">â€”</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Telepon</span>
                                <span class="font-semibold text-slate-800" id="confirmPhone">â€”</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Keahlian</span>
                                <span class="font-semibold text-slate-800" id="confirmSkill">â€”</span>
                            </div>
                        </div>

                        {{-- Warning --}}
                        <div class="p-3 rounded-xl bg-red-50 border border-red-100">
                            <p class="text-xs text-center font-semibold text-red-700">
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                                Wajib menjalankan tugas sampai selesai setelah menerima penugasan resmi.
                            </p>
                        </div>
                    </div>

                    {{-- Footer Navigation --}}
                    <div class="px-7 py-5 border-t border-slate-100 flex items-center justify-between rounded-b-2xl"
                         style="background: #FAFBFD;">
                        <button type="button" id="prevBtn"
                                class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all duration-200 cursor-pointer hidden group flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 12H5M12 19l-7-7 7-7" />
                            </svg>
                            <span>Sebelumnya</span>
                        </button>
                        <div class="flex-1"></div>
                        <button type="button" id="nextBtn"
                                class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer"
                                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.3);">
                            Selanjutnya <i class="bi bi-arrow-right text-xs"></i>
                        </button>
                        <button type="submit" id="finalSubmitBtn"
                                class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer hidden"
                                style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.3);">
                            <i class="bi bi-send-fill"></i> Kirim Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: Info --}}
        <div class="space-y-5 lg:sticky lg:top-24 self-start">

            {{-- Tentang Relawan --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden"
                 style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                        <i class="bi bi-people-fill" style="color: #3B6FE8;"></i>
                        Tentang Relawan SIGMA
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Relawan SIGMA bertugas membantu masyarakat terdampak bencana di lapangan. Setelah mendaftar, data Anda akan diverifikasi oleh Admin.
                    </p>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: #E4F0F6;">
                                <i class="bi bi-1-circle-fill text-xs" style="color: #1e3a8a;"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Daftar</p>
                                <p class="text-[11px] text-slate-500">Isi formulir pendaftaran</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: #E4F0F6;">
                                <i class="bi bi-2-circle-fill text-xs" style="color: #1e3a8a;"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Verifikasi</p>
                                <p class="text-[11px] text-slate-500">Admin memverifikasi data Anda</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background: #E4F0F6;">
                                <i class="bi bi-3-circle-fill text-xs" style="color: #1e3a8a;"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Penugasan</p>
                                <p class="text-[11px] text-slate-500">Ditugaskan ke lokasi bencana</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keahlian yang Dibutuhkan - removed --}}
        </div>
    </div>
    @endguest
@endif
</div>
@endsection

@section('scripts')
<script>
    (function() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const finalBtn = document.getElementById('finalSubmitBtn');

        // Only run form wizard script if form elements exist (authenticated user)
        if (prevBtn && nextBtn && finalBtn) {
            let currentStep = 1;
            const totalSteps = 3;

            function showStep(step) {
                // Hide all steps
                document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
                // Show current
                document.querySelector(`.step-content[data-step="${step}"]`).classList.remove('hidden');

                // Update progress indicators
                document.querySelectorAll('.step-indicator').forEach(ind => {
                    const s = parseInt(ind.dataset.step);
                    const dot = ind.querySelector('.step-dot');
                    const label = ind.querySelector('.step-label');

                    if (s < step) {
                        // Completed
                        dot.style.background = 'linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%)';
                        dot.style.color = 'white';
                        dot.style.border = 'none';
                        dot.style.boxShadow = '0 2px 8px rgba(30,58,138,0.3)';
                        dot.innerHTML = '<i class="bi bi-check text-xs"></i>';
                        if (label) { label.className = 'step-label text-xs font-semibold text-slate-800 hidden sm:inline'; }
                    } else if (s === step) {
                        // Active
                        dot.style.background = 'linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%)';
                        dot.style.color = 'white';
                        dot.style.border = 'none';
                        dot.style.boxShadow = '0 2px 8px rgba(30,58,138,0.3)';
                        dot.innerHTML = s;
                        if (label) { label.className = 'step-label text-xs font-semibold text-slate-800 hidden sm:inline'; }
                    } else {
                        // Upcoming
                        dot.style.background = 'white';
                        dot.style.color = '#94a3b8';
                        dot.style.border = '2px solid #e2e8f0';
                        dot.style.boxShadow = 'none';
                        dot.innerHTML = s;
                        if (label) { label.className = 'step-label text-xs font-medium text-slate-400 hidden sm:inline'; }
                    }
                });

                // Update progress lines
                document.querySelector('.step-line-1').style.width = step > 1 ? '100%' : '0%';
                document.querySelector('.step-line-2').style.width = step > 2 ? '100%' : '0%';

                // Update buttons
                prevBtn.classList.toggle('hidden', step === 1);
                nextBtn.classList.toggle('hidden', step === totalSteps);
                finalBtn.classList.toggle('hidden', step !== totalSteps);

                // Populate confirmation on step 3
                if (step === 3) {
                    document.getElementById('confirmName').textContent = document.getElementById('name').value || '—';
                    document.getElementById('confirmAddress').textContent = document.getElementById('address').value || '—';
                    document.getElementById('confirmPhone').textContent = document.getElementById('phone_number').value || '—';
                    const selectedSkill = document.querySelector('input[name="skill"]:checked');
                    document.getElementById('confirmSkill').textContent = selectedSkill ? selectedSkill.closest('.skill-card').querySelector('.text-sm').textContent : '—';
                }
            }

            // Validate current step before proceeding
            function validateStep(step) {
                if (step === 1) {
                    const name = document.getElementById('name');
                    const address = document.getElementById('address');
                    const phone = document.getElementById('phone_number');
                    if (!name.value.trim()) { name.focus(); name.reportValidity(); return false; }
                    if (!address.value.trim()) { address.focus(); address.reportValidity(); return false; }
                    if (!phone.value.trim()) { phone.focus(); phone.reportValidity(); return false; }
                }
                if (step === 2) {
                    const selected = document.querySelector('input[name="skill"]:checked');
                    if (!selected) { alert('Pilih salah satu keahlian.'); return false; }
                }
                return true;
            }

            nextBtn.addEventListener('click', () => {
                if (!validateStep(currentStep)) return;
                if (currentStep < totalSteps) { currentStep++; showStep(currentStep); }
            });

            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) { currentStep--; showStep(currentStep); }
            });

            // Skill card selection
            document.querySelectorAll('.skill-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.skill-card').forEach(c => {
                        c.classList.remove('border-blue-500', 'bg-blue-50/50');
                        c.classList.add('border-slate-200');
                        const check = c.querySelector('.skill-check');
                        check.classList.remove('bg-blue-600', 'border-blue-600');
                        check.classList.add('border-slate-300');
                        check.querySelector('i').classList.add('hidden');
                    });
                    card.classList.remove('border-slate-200');
                    card.classList.add('border-blue-500', 'bg-blue-50/50');
                    const check = card.querySelector('.skill-check');
                    check.classList.remove('border-slate-300');
                    check.classList.add('bg-blue-600', 'border-blue-600');
                    check.querySelector('i').classList.remove('hidden');
                    card.querySelector('input').checked = true;
                });
            });

            // Init: highlight pre-selected skill
            const preSelected = document.querySelector('input[name="skill"]:checked');
            if (preSelected) preSelected.closest('.skill-card').click();

            showStep(1);
        }
    })();
</script>
@endsection
