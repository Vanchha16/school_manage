<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Borrow;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        View::composer('*', function ($view) {

        // overdue = borrow_date older than 3 days AND not returned
        $overdueCount = Borrow::query()
            ->whereNull('return_date')
            ->where('borrow_date', '<', now()->subDays(3))
            ->count();

        $view->with('overdueCount', $overdueCount);
    });
    }
}