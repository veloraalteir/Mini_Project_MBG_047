<?php

namespace App\Providers;

use App\Models\Report;
use App\Observers\ReportObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('pagination.custom');

        Report::observe(ReportObserver::class);
    }
}