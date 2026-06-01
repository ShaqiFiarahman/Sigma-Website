{{-- ═══════════════════════════════════════════════════
     PROFILE DROPDOWN COMPONENT
     Anchored below the profile trigger in the navbar.
═══════════════════════════════════════════════════ --}}
<div id="profileDropdown"
    class="profile-dropdown hidden absolute right-0 top-full mt-2.5 w-[19.5rem] bg-white rounded-2xl overflow-hidden z-[9999] border border-slate-200/60"
    style="box-shadow: 0 18px 38px -16px rgba(10, 15, 30, 0.28), 0 0 0 1px rgba(10, 15, 30, 0.04);">

    {{-- Header Gradient --}}
    <div class="relative px-6 pt-5 pb-5 text-center"
        style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 100%);">

        {{-- Subtle Dot Grid Pattern --}}
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none"
            style="background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px); background-size: 10px 10px;"></div>

        {{-- Subtle Topography Waves --}}
        <div class="absolute inset-0 opacity-[0.04] pointer-events-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0,35 Q25,15 50,35 T100,35" fill="none" stroke="white" stroke-width="0.35" />
                <path d="M0,50 Q25,30 50,50 T100,50" fill="none" stroke="white" stroke-width="0.35" />
                <path d="M0,65 Q25,45 50,65 T100,65" fill="none" stroke="white" stroke-width="0.35" />
            </svg>
        </div>

        {{-- Avatar --}}
        <div class="profile-avatar w-14 h-14 rounded-full mx-auto flex items-center justify-center bg-white text-slate-800 text-xl font-extrabold border-2 border-white/20 relative shadow-sm">
            {{ substr(auth()->user()->full_name ?? 'U', 0, 1) }}
        </div>

        {{-- Name & Role --}}
        <h3 class="text-[15px] font-bold text-white mt-2.5 truncate px-2">{{ auth()->user()->full_name }}</h3>
        <div class="inline-flex items-center gap-1 mt-1.5 px-2.5 py-0.5 rounded-full"
            style="background: rgba(228,240,246,0.15); border: 1px solid rgba(228,240,246,0.2);">
            @php
                $role = auth()->user()->role;
            @endphp
            @if(strtolower($role) === 'admin')
                <svg class="w-2.5 h-2.5 text-blue-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4" />
                </svg>
            @elseif(strtolower($role) === 'relawan')
                <svg class="w-2.5 h-2.5 text-blue-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            @else
                <svg class="w-2.5 h-2.5 text-blue-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            @endif
            <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider">{{ $role }}</span>
        </div>
    </div>

    {{-- Profile Details & Form Panels --}}
    <div class="px-6 pt-5 pb-5">

        {{-- PANEL 1: MAIN DETAILS VIEW --}}
        <div id="profileMainPanel">
            <div class="space-y-3">
                {{-- Email --}}
                <div class="stagger-item flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                        <i class="bi bi-envelope text-slate-500 text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none">Email</p>
                        <p class="text-[13px] font-semibold text-slate-700 mt-0.5 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                {{-- Total Reports --}}
                @php
                    $roleType = strtolower(auth()->user()->role ?? '');
                    if ($roleType === 'admin') {
                        $laporanLabel = 'Laporan Diverifikasi';
                        $laporanCount = \App\Models\Disaster::where('verified_by', auth()->id())->count() . ' Laporan';
                    } elseif ($roleType === 'relawan') {
                        $volunteerData = \App\Models\Volunteer::where('user_id', auth()->id())->first();
                        $laporanLabel = 'Total Laporan Tugas';
                        if ($volunteerData) {
                            $laporanCount = \App\Models\VolunteerReport::where('volunteer_id', $volunteerData->id)->count() . ' Laporan';
                        } else {
                            $laporanCount = '0 Laporan';
                        }
                    } else {
                        $laporanLabel = 'Total Laporan Anda';
                        $laporanCount = \App\Models\Disaster::where('user_id', auth()->id())->count() . ' Laporan';
                    }
                @endphp
                <div class="stagger-item flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                        <i class="bi bi-file-earmark-text text-slate-500 text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none">{{ $laporanLabel }}</p>
                        <p class="text-[13px] font-semibold text-slate-700 mt-0.5">{{ $laporanCount }}</p>
                    </div>
                </div>

                {{-- Member Since --}}
                <div class="stagger-item flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                        <i class="bi bi-calendar-check text-slate-500 text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none">Bergabung</p>
                        <p class="text-[13px] font-semibold text-slate-700 mt-0.5">
                            {{ auth()->user()->created_at?->format('d M Y') ?? (auth()->user()->updated_at?->format('d M Y') ?? 'Tidak tersedia') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="stagger-item grid grid-cols-2 gap-2 mt-4 pt-3.5 border-t border-slate-100">
                <button type="button" onclick="showProfilePanel('profileEditPanel', 'forward')"
                    class="profile-action-btn flex items-center justify-center gap-1.5 py-2 px-2 text-[10px] font-bold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-lg uppercase tracking-wider">
                    <i class="bi bi-pencil-square text-xs"></i> Edit Profil
                </button>
                <button type="button" onclick="showProfilePanel('profilePasswordPanel', 'forward')"
                    class="profile-action-btn flex items-center justify-center gap-1.5 py-2 px-2 text-[10px] font-bold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-lg uppercase tracking-wider">
                    <i class="bi bi-key text-xs"></i> Ubah Sandi
                </button>
            </div>

            {{-- Logout --}}
            <div class="stagger-item mt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="profile-action-btn w-full flex items-center justify-center gap-1.5 py-2 text-[11px] font-bold text-red-600/90 hover:text-red-600 hover:bg-red-50/50 rounded-lg uppercase tracking-wider">
                        <i class="bi bi-box-arrow-right text-sm"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- PANEL 2: EDIT PROFILE FORM --}}
        <div id="profileEditPanel" class="hidden">
            <h4 class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-3.5 flex items-center gap-1.5">
                <i class="bi bi-pencil-square text-slate-500 text-sm"></i> Edit Profil
            </h4>

            <form id="profileEditForm" onsubmit="submitProfileEdit(event)">
                @csrf
                <div>
                    <label class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1 block">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ auth()->user()->full_name }}" required
                        class="profile-input w-full px-3 py-2 text-[13px] bg-slate-50/50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:bg-white">
                </div>

                <div id="profileEditError" class="hidden mt-3 p-2 bg-red-50 text-red-600 text-[10px] rounded-lg font-medium border border-red-100"></div>
                <div id="profileEditSuccess" class="hidden mt-3 p-2 bg-emerald-50 text-emerald-600 text-[10px] rounded-lg font-medium border border-emerald-100"></div>

                <div class="grid grid-cols-2 gap-2 mt-4">
                    <button type="button" onclick="showProfilePanel('profileMainPanel', 'back')"
                        class="profile-action-btn py-2 text-[10px] font-bold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-lg uppercase tracking-wider">
                        Batal
                    </button>
                    <button type="submit" id="btnSaveProfile"
                        class="profile-action-btn py-2 text-[10px] font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-lg uppercase tracking-wider flex items-center justify-center gap-1.5">
                        <span id="txtSaveProfile">Simpan</span>
                        <div id="spinnerSaveProfile" class="hidden w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    </button>
                </div>
            </form>
        </div>

        {{-- PANEL 3: CHANGE PASSWORD FORM --}}
        <div id="profilePasswordPanel" class="hidden">
            <h4 class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-3.5 flex items-center gap-1.5">
                <i class="bi bi-key text-slate-500 text-sm"></i> Ubah Sandi
            </h4>

            <form id="profilePasswordForm" onsubmit="submitPasswordChange(event)">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1 block">Kata Sandi Lama</label>
                        <input type="password" name="current_password" required placeholder="Sandi lama"
                            class="profile-input w-full px-3 py-2 text-[13px] bg-slate-50/50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1 block">Kata Sandi Baru</label>
                        <input type="password" name="password" required placeholder="Min. 8 karakter"
                            class="profile-input w-full px-3 py-2 text-[13px] bg-slate-50/50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:bg-white">
                    </div>
                    <div>
                        <label class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1 block">Konfirmasi Sandi</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi sandi baru"
                            class="profile-input w-full px-3 py-2 text-[13px] bg-slate-50/50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 focus:bg-white">
                    </div>
                </div>

                <div id="profilePasswordError" class="hidden mt-3 p-2 bg-red-50 text-red-600 text-[10px] rounded-lg font-medium border border-red-100"></div>
                <div id="profilePasswordSuccess" class="hidden mt-3 p-2 bg-emerald-50 text-emerald-600 text-[10px] rounded-lg font-medium border border-emerald-100"></div>

                <div class="grid grid-cols-2 gap-2 mt-4">
                    <button type="button" onclick="showProfilePanel('profileMainPanel', 'back')"
                        class="profile-action-btn py-2 text-[10px] font-bold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 rounded-lg uppercase tracking-wider">
                        Batal
                    </button>
                    <button type="submit" id="btnSavePassword"
                        class="profile-action-btn py-2 text-[10px] font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-lg uppercase tracking-wider flex items-center justify-center gap-1.5">
                        <span id="txtSavePassword">Simpan</span>
                        <div id="spinnerSavePassword" class="hidden w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    {{-- Profile form submissions (need Blade route URLs) --}}
    function submitProfileEdit(event) {
        event.preventDefault();
        const form = event.target;
        const btn = document.getElementById('btnSaveProfile');
        const txt = document.getElementById('txtSaveProfile');
        const spinner = document.getElementById('spinnerSaveProfile');
        const errDiv = document.getElementById('profileEditError');
        const succDiv = document.getElementById('profileEditSuccess');

        btn.disabled = true;
        txt.textContent = 'Menyimpan...';
        spinner.classList.remove('hidden');
        errDiv.classList.add('hidden');
        succDiv.classList.add('hidden');

        fetch('{{ route("profile.update") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            btn.disabled = false;
            txt.textContent = 'Simpan';
            spinner.classList.add('hidden');

            if (res.status === 200 && res.body.success) {
                succDiv.textContent = res.body.message;
                succDiv.classList.remove('hidden');

                const headerName = document.querySelector('#profileDropdown h3');
                if (headerName && res.body.full_name) headerName.textContent = res.body.full_name;

                const avatarDiv = document.querySelector('#profileDropdown .profile-avatar');
                if (avatarDiv && res.body.full_name) avatarDiv.textContent = res.body.full_name.charAt(0).toUpperCase();

                setTimeout(() => { showProfilePanel('profileMainPanel'); window.location.reload(); }, 1200);
            } else {
                errDiv.textContent = res.body.message || 'Terjadi kesalahan saat menyimpan profil.';
                errDiv.classList.remove('hidden');
            }
        })
        .catch(() => {
            btn.disabled = false;
            txt.textContent = 'Simpan';
            spinner.classList.add('hidden');
            errDiv.textContent = 'Koneksi gagal. Silakan coba lagi.';
            errDiv.classList.remove('hidden');
        });
    }

    function submitPasswordChange(event) {
        event.preventDefault();
        const form = event.target;
        const btn = document.getElementById('btnSavePassword');
        const txt = document.getElementById('txtSavePassword');
        const spinner = document.getElementById('spinnerSavePassword');
        const errDiv = document.getElementById('profilePasswordError');
        const succDiv = document.getElementById('profilePasswordSuccess');

        btn.disabled = true;
        txt.textContent = 'Menyimpan...';
        spinner.classList.remove('hidden');
        errDiv.classList.add('hidden');
        succDiv.classList.add('hidden');

        fetch('{{ route("profile.password") }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            btn.disabled = false;
            txt.textContent = 'Simpan';
            spinner.classList.add('hidden');

            if (res.status === 200 && res.body.success) {
                succDiv.textContent = res.body.message;
                succDiv.classList.remove('hidden');
                form.reset();
                setTimeout(() => { showProfilePanel('profileMainPanel'); }, 1500);
            } else {
                let msg = res.body.message || 'Terjadi kesalahan saat mengubah kata sandi.';
                if (res.body.errors) {
                    const firstKey = Object.keys(res.body.errors)[0];
                    msg = res.body.errors[firstKey][0];
                }
                errDiv.textContent = msg;
                errDiv.classList.remove('hidden');
            }
        })
        .catch(() => {
            btn.disabled = false;
            txt.textContent = 'Simpan';
            spinner.classList.add('hidden');
            errDiv.textContent = 'Koneksi gagal. Silakan coba lagi.';
            errDiv.classList.remove('hidden');
        });
    }
</script>
