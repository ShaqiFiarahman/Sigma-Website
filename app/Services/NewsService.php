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

    // Ambil Berita RSS
    public function fetchNews()
    {
        // Hapus data berita lama yang sudah berumur lebih dari 7 hari
        News::where('published_at', '<', now()->subDays(7))->delete();

        // Iterasi daftar link RSS feed dari berbagai media berita online
        foreach ($this->feeds as $source => $url) {
            try {
                $xml = simplexml_load_file($url);
                // Jika XML RSS feed gagal dimuat, catat log peringatan dan lewati feed ini
                if (!$xml) {
                    Log::warning("Failed to load RSS feed from {$source}");
                    continue;
                }

                // Iterasi setiap item berita yang ada di dalam channel RSS
                foreach ($xml->channel->item as $item) {
                    $title = (string) $item->title;
                    $description = (string) $item->description;
                    $link = (string) $item->link;
                    $pubDate = (string) $item->pubDate;
                    
                    // Jika judul berita mengandung kata kunci terkait bencana, simpan/perbarui berita tersebut
                    if ($this->containsKeywords($title)) {
                        $imageUrl = $this->extractImage($item);

                        // Simpan berita baru atau perbarui jika URL berita sudah ada sebelumnya
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

    // Utilitas Parsing & Ekstraksi Teks
    protected function containsKeywords($text)
    {
        $text = strtolower($text);

        // Saring dan abaikan berita olahraga atau kata sensitif buatan yang salah tebak
        if (str_contains($text, 'gempar') || str_contains($text, 'olahraga') || str_contains($text, 'sepakbola')) {
            return false;
        }

        // Iterasi kata kunci bencana untuk mencari kecocokan kata pada judul
        foreach ($this->keywords as $keyword) {
            // Kembalikan nilai true jika kata kunci ditemukan dalam teks judul
            if (str_contains($text, strtolower($keyword))) {
                return true;
            }
        }
        return false;
    }

    protected function extractImage($item)
    {
        // Jika RSS menyertakan enclosure tag, gunakan URL media yang ada di dalamnya
        if ($item->enclosure) {
            return (string) $item->enclosure['url'];
        }

        $namespaces = $item->getNamespaces(true);
        // Gunakan namespace media dari RSS jika tersedia untuk mengekstrak gambar
        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            // Jika ada konten media, ambil URL gambar dari atributnya
            if (isset($media->content)) {
                $attrs = $media->content->attributes();
                if (isset($attrs['url'])) {
                    return (string) $attrs['url'];
                }
            }
            // Jika hanya ada thumbnail media, ambil URL thumbnail tersebut
            if (isset($media->thumbnail)) {
                $attrs = $media->thumbnail->attributes();
                if (isset($attrs['url'])) {
                    return (string) $attrs['url'];
                }
            }
        }

        // Jika URL gambar ditemukan di dalam deskripsi HTML via regex, gunakan URL tersebut
        preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', (string) $item->description, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }
}
