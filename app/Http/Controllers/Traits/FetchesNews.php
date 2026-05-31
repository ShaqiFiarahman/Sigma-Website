<?php

namespace App\Http\Controllers\Traits;

use App\Models\News;

trait FetchesNews
{
    /**
     * Get latest disaster-related news from the last 7 days.
     *
     * @return array
     */
    private function getNews(): array
    {
        return News::where('published_at', '>=', now()->subDays(7))
            ->latest('published_at')
            ->limit(6)
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
