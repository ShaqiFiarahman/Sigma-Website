@extends('layouts.app')
@section('title', 'Kirim Laporan Tugas')

@section('page-actions')
    <a href="{{ route('volunteer.reports') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
        <i class="bi bi-arrow-left text-xs"></i> Riwayat
    </a>
@endsection

@section('content')

@if($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800">
        <p class="font-bold mb-1">Terjadi kesalahan:</p>
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200/60 rounded-2xl overflow-hidden" style="box-shadow: 0 2px 8px rgba(10,15,30,0.04);">
        
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-100"
            style="background: linear-gradient(135deg, #0A0F1E 0%, #0f1f4a 100%);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                    style="background: rgba(228,240,246,0.15); border: 1px solid rgba(228,240,246,0.2);">
                    @php
                        $skillIcon = match($volunteer->skill) {
                            'MEDIS' => 'bi-heart-pulse',
                            'SAR' => 'bi-life-preserver',
                            'LOGISTIK' => 'bi-box-seam',
                            'KONSUMSI' => 'bi-cup-hot',
                            'PSIKOSOSIAL' => 'bi-chat-heart',
                            default => 'bi-clipboard-data',
                        };
                    @endphp
                    <i class="bi {{ $skillIcon }} text-white"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">Laporan {{ ucfirst(strtolower($volunteer->skill)) }}</h2>
                    <p class="text-[11px] text-white/50">{{ $volunteer->name }} · {{ $volunteer->assignment ?? 'Belum ada penugasan' }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('volunteer.report.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            {{-- Pilih Bencana (opsional) --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                    Terkait Bencana <span class="text-slate-400 font-normal normal-case">(opsional)</span>
                </label>
                <select name="disaster_id"
                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white text-slate-700">
                    <option value="">— Tidak terkait bencana tertentu —</option>
                    @foreach($disasters as $disaster)
                        <option value="{{ $disaster->id }}">{{ $disaster->title }} — {{ $disaster->location ?? 'Lokasi tidak diketahui' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dynamic Fields per Skill --}}
            <div class="border-t border-slate-100 pt-5">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Data Laporan</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($fields as $field)
                        @if($field['type'] === 'textarea')
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $field['label'] }}</label>
                                <textarea name="data[{{ $field['name'] }}]" rows="3" required
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"
                                    placeholder="Tulis {{ strtolower($field['label']) }}...">{{ old('data.' . $field['name']) }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        @elseif($field['type'] === 'select')
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $field['label'] }}</label>
                                <select name="data[{{ $field['name'] }}]" required
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white">
                                    <option value="">— Pilih —</option>
                                    @foreach($field['options'] as $opt)
                                        <option value="{{ $opt }}" {{ old('data.' . $field['name']) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">{{ $field['label'] }}</label>
                                <input type="{{ $field['type'] }}" name="data[{{ $field['name'] }}]" required
                                    value="{{ old('data.' . $field['name']) }}"
                                    min="{{ $field['type'] === 'number' ? '0' : '' }}"
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                    placeholder="{{ $field['label'] }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Catatan Tambahan --}}
            <div class="border-t border-slate-100 pt-5">
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                    Catatan Tambahan <span class="text-slate-400 font-normal normal-case">(opsional)</span>
                </label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"
                    placeholder="Catatan atau informasi tambahan...">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold text-white rounded-xl transition-all hover:-translate-y-0.5"
                style="background: linear-gradient(135deg, #0A0F1E 0%, #1e3a8a 100%); box-shadow: 0 4px 12px rgba(30,58,138,0.3);">
                <i class="bi bi-send"></i> Kirim Laporan
            </button>
        </form>
    </div>
</div>

@endsection
