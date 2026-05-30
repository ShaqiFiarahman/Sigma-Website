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
        'banjir', 'gempa', 'tanah longsor', 'kebakaran', 'tsunami', 'gunung meletus', 'cuaca ekstrem', 'bencana', 'bmkg', 'hujan lebat', 'angin kencang', 'pohon tumbang'
    ];

    public function fetchNews()
    {
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
                    
                    if ($this->containsKeywords($title)) {
                        $imageUrl = $this->extractImage($item);

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
        $text = strtolower($text);

        if (str_contains($text, 'gempar') || str_contains($text, 'olahraga') || str_contains($text, 'sepakbola')) {
            return false;
        }

        foreach ($this->keywords as $keyword) {
            if (str_contains($text, strtolower($keyword))) {
                return true;
            }
        }
        return false;
    }

    protected function extractImage($item)
    {
        if ($item->enclosure) {
            return (string) $item->enclosure['url'];
        }

        $namespaces = $item->getNamespaces(true);
        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content)) {
                $attrs = $media->content->attributes();
                if (isset($attrs['url'])) {
                    return (string) $attrs['url'];
                }
            }
            if (isset($media->thumbnail)) {
                $attrs = $media->thumbnail->attributes();
                if (isset($attrs['url'])) {
                    return (string) $attrs['url'];
                }
            }
        }

        preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', (string) $item->description, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }
}
