<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('news:fetch')]
#[Description('Fetch disaster news from RSS feeds')]
class FetchNewsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(\App\Services\NewsService $newsService)
    {
        $this->info('Fetching news...');
        $newsService->fetchNews();
        $this->info('News fetched successfully.');
    }
}
