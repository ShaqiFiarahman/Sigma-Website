<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('news:fetch')]
#[Description('Ambil berita bencana dari RSS feed')]
class FetchNewsCommand extends Command
{
    // Command Ambil Berita
    public function handle(\App\Services\NewsService $newsService)
    {
        $this->info('Fetching news...');
        $newsService->fetchNews();
        $this->info('News fetched successfully.');
    }
}
