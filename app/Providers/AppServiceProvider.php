<?php

namespace App\Providers;

use App\View\Composers\VolunteerNotificationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Bind volunteer notification data to the partial view
        View::composer('volunteer._notification', VolunteerNotificationComposer::class);

        // Tambahkan ini untuk Vercel Serverless
        if (env('VERCEL')) {
            config(['view.compiled' => '/tmp']);
            config(['logging.default' => 'stderr']);
            config(['session.driver' => 'cookie']);
            config(['cache.default' => 'array']);
        }
    }
}
