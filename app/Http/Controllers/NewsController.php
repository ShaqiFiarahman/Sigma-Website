<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // Daftar Berita
    public function index(Request $request)
    {
        // Mulai query untuk mengambil berita yang terbit dalam 7 hari terakhir secara terbaru
        $query = News::where('published_at', '>=', now()->subDays(7))->latest('published_at');

        // Jika filter sumber berita dipilih, batasi query pada sumber tersebut
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Jika keyword pencarian diisi, filter berita berdasarkan judul atau ringkasan
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                // Cari kecocokan judul atau deskripsi ringkasan berita
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $news = $query->paginate(12);
        $sources = News::select('source')->distinct()->orderBy('source')->pluck('source');

        return view('news.index', compact('news', 'sources'));
    }
}
