{{-- Shelter Edit: Form Section --}}
<div class="lg:col-span-3">
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden" style="box-shadow: 0 1px 3px rgba(10,15,30,0.06), 0 4px 16px rgba(10,15,30,0.04);">

        <form action="{{ route('admin.shelter.update', $shelter->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Photo Banner --}}
            <div class="relative h-36 bg-slate-100 overflow-hidden group">
                @if($shelter->photo_url)
                    <img src="{{ $shelter->photo_url }}" alt="{{ $shelter->name }}" class="w-full h-full object-cover" id="photoPreview">
                @else
                    <div class="w-full h-full flex items-center justify-center" id="photoPlaceholder">
                        <p class="text-xs text-slate-400">Belum ada foto</p>
                    </div>
                    <img src="" alt="" class="w-full h-full object-cover hidden" id="photoPreview">
                @endif
                <label for="photoInput" class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 transition-all cursor-pointer">
                    <span class="opacity-0 group-hover:opacity-100 transition-opacity text-xs font-semibold text-white bg-black/50 px-3 py-1.5 rounded-lg">Ganti Foto</span>
                </label>
                <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden">
            </div>

            <div class="p-6 space-y-6">

                {{-- Section: Informasi Dasar --}}
                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3">Informasi Dasar</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Nama Posko</label>
                            <input type="text" name="name" value="{{ old('name', $shelter->name) }}" required
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Alamat</label>
                            <input type="text" name="address" id="addressInput" value="{{ old('address', $shelter->address) }}" placeholder="Ketik alamat atau cari di peta..."
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>

                        {{-- Hidden coordinates --}}
                        <input type="hidden" name="latitude" id="latitudeInput" value="{{ old('latitude', $shelter->latitude) }}">
                        <input type="hidden" name="longitude" id="longitudeInput" value="{{ old('longitude', $shelter->longitude) }}">

                        {{-- Map Location Picker --}}
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Pilih Lokasi Koordinat Posko</label>
                            <div id="mapPicker" class="w-full rounded-xl border border-slate-200 overflow-hidden" style="height: 220px; box-shadow: 0 2px 8px rgba(10,15,30,0.04);"></div>
                            <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1">
                                <i class="bi bi-info-circle"></i>
                                <span>Geser penanda merah atau klik pada peta untuk memposisikan koordinat posko secara akurat.</span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Nomor Telepon Kontak</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $shelter->contact_phone) }}" placeholder="6281234567890"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                    </div>
                </div>

                {{-- Section: Kapasitas --}}
                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3">Kapasitas</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Saat Ini</label>
                            <input type="number" name="capacity_current" value="{{ old('capacity_current', $shelter->capacity_current) }}" required min="0"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Maksimal</label>
                            <input type="number" name="capacity_max" value="{{ old('capacity_max', $shelter->capacity_max) }}" required min="1"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                    </div>
                </div>

                {{-- Section: Operasional --}}
                <div>
                    <p class="text-xs font-bold text-slate-700 mb-3">Operasional</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                                <option value="Tersedia" {{ $shelter->status === 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="Penuh" {{ $shelter->status === 'Penuh' ? 'selected' : '' }}>Penuh</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-500 mb-1">Kebutuhan Logistik <span class="text-slate-400">(pisahkan dengan koma)</span></label>
                            <input type="text" name="logistics" value="{{ old('logistics', implode(', ', $shelter->logistics ?? [])) }}" placeholder="Sembako, Air Mineral, Selimut"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-500/20 bg-white text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-2" style="background: #FAFBFD;">
                <button type="button" onclick="history.back()" class="px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer">Batal</button>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl cursor-pointer" style="background: linear-gradient(135deg, #3B6FE8 0%, #1e3a8a 100%); box-shadow: 0 2px 8px rgba(30,58,138,0.25);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
