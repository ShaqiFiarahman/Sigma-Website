<?php

namespace App\Http\Controllers\Traits;

use App\Models\News;

trait FetchesNews
{
    // Trait Ambil Berita
    private function getNews(int $limit = 9): array
    {
        // Ambil berita bencana terbaru dalam 7 hari terakhir dibatasi jumlah maksimalnya
        return News::where('published_at', '>=', now()->subDays(7))
            ->latest('published_at')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'time' => $item->published_at->diffForHumans(),
                    'category' => strtoupper($item->source),
                    'tone' => 'info',
                    'image_url' => $item->image_url,
                    'url' => $item->url,
                    'source' => $item->source,
                ];
            })
            ->toArray();
    }
}
