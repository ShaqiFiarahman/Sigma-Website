{{-- Admin Laporan: Right Panel - Detail --}}
<div class="flex-1 min-w-0" id="detailPanel">

    {{-- Empty state --}}
    <div class="bg-white border border-slate-200/80 rounded-2xl flex items-center justify-center py-32" id="detailEmpty" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">
        <div class="text-center">
            <i class="bi bi-file-earmark-text text-3xl text-slate-200 block mb-2"></i>
            <p class="text-sm text-slate-400">Pilih laporan dari daftar</p>
        </div>
    </div>

    {{-- Detail content --}}
    <div class="hidden" id="detailContent">
        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06);">

            {{-- Photo --}}
            <div id="detailPhoto" class="h-44 bg-slate-100 overflow-hidden relative hidden">
                <img id="detailImg" src="" class="w-full h-full object-cover" alt="">
            </div>

            <div class="p-5 space-y-5">

                {{-- Title --}}
                <div>
                    <div class="flex items-start justify-between gap-3 mb-1">
                        <h2 class="text-lg font-bold text-slate-900" id="detailTitle"></h2>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0" id="detailBadge"></span>
                    </div>
                    <p class="text-xs text-slate-500" id="detailMeta"></p>
                </div>

                {{-- Info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] text-slate-400 font-medium mb-1">Lokasi</p>
                        <p class="text-xs font-medium text-slate-800" id="detailLocation"></p>
                        <p class="text-[10px] text-slate-400 mt-0.5 font-mono" id="detailCoords"></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-medium mb-1">Pelapor</p>
                        <p class="text-xs font-medium text-slate-800" id="detailReporter"></p>
                        <p class="text-[10px] text-slate-400 mt-0.5" id="detailTime"></p>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <p class="text-[10px] text-slate-400 font-medium mb-1">Deskripsi</p>
                    <p class="text-xs text-slate-600 leading-relaxed" id="detailDesc"></p>
                </div>

                {{-- Action --}}
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-xs font-bold text-slate-700 mb-3">Tindakan</p>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="text-[10px] text-slate-500 block mb-1">Status</label>
                            <select id="actionStatus" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 bg-white">
                                <option value="PENDING">Pending</option>
                                <option value="AWAS">Awas</option>
                                <option value="SIAGA_1">Siaga 1</option>
                                <option value="SIAGA_2">Siaga 2</option>
                                <option value="DECLINE">Tolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500 block mb-1">Jenis Bencana</label>
                            <select id="actionType" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-blue-400 bg-white">
                                <option value="flood">Banjir</option>
                                <option value="fire">Kebakaran</option>
                                <option value="earthquake">Gempa</option>
                                <option value="landslide">Longsor</option>
                                <option value="tsunami">Tsunami</option>
                                <option value="storm">Badai</option>
                                <option value="volcano">Gunung Meletus</option>
                                <option value="unknown">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" id="btnSaveAction"
                            class="w-full py-2 text-xs font-semibold text-white rounded-lg cursor-pointer"
                            style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%);">
                        Simpan Perubahan
                    </button>
                    <button type="button" id="btnResolveAction"
                            class="w-full py-2 mt-2 text-xs font-semibold text-white rounded-lg cursor-pointer transition-all hover:opacity-90"
                            style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                        Tandai Selesai
                    </button>
                </div>

                {{-- Mini Map --}}
                <div id="detailMiniMap" class="w-full h-72 rounded-xl border border-slate-100 overflow-hidden bg-slate-100"></div>
            </div>
        </div>
    </div>
</div>
