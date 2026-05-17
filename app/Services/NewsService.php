<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Support\Facades\Log;

class NewsService
{
    protected $feeds = [
        'CNN Indonesia' => 'https://www.cnnindonesia.com/nasional/rss',
        'Antara News' => 'https://www.antaranews.com/rss/top-news',
        'Republika' => 'https://www.republika.co.id/rss/',
        'Sindonews' => 'https://www.sindonews.com/feed',
    ];

    protected $keywords = [
        'banjir', 'gempa', 'longsor', 'kebakaran', 'tsunami', 'gunung meletus', 'cuaca ekstrem'
    ];

    public function fetchNews()
    {
        // Hapus berita yang lebih dari 7 hari
        News::where('published_at', '<', now()->subDays(7))->delete();

        foreach ($this->feeds as $source => $url) {
            try {
                $xml = simplexml_load_file($url);
                if (!$xml) {
                    Log::warning("Failed to load RSS feed from {$source}");
                    continue;
                }

                foreach ($xml->channel->item as $item) {
                    $title = (string) $item->title;
                    $description = (string) $item->description;
                    $link = (string) $item->link;
                    $pubDate = (string) $item->pubDate;
                    
                    // Filter by keywords (title only)
                    if ($this->containsKeywords($title)) {
                        // Extract image if available
                        $imageUrl = $this->extractImage($item);

                        // Save or update
                        News::updateOrCreate(
                            ['url' => $link],
                            [
                                'title' => $title,
                                'summary' => strip_tags($description),
                                'image_url' => $imageUrl,
                                'source' => $source,
                                'published_at' => date('Y-m-d H:i:s', strtotime($pubDate)),
                            ]
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error fetching news from {$source}: " . $e->getMessage());
            }
        }
    }

    protected function containsKeywords($text)
    {
        foreach ($this->keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function extractImage($item)
    {
        // Try enclosure first
        if ($item->enclosure) {
            return (string) $item->enclosure['url'];
        }

        // Try to find image in description
        preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', (string) $item->description, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }
}
