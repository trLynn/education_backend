<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\TextbookRepositoryInterface::class,
            \App\Repositories\Eloquent\TextbookRepository::class
        );

        // Chapter Binding (အသစ်ထည့်ရန်)
        $this->app->bind(
            \App\Repositories\Interfaces\ChapterRepositoryInterface::class,
            \App\Repositories\Eloquent\ChapterRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\SubChapterRepositoryInterface::class,
            \App\Repositories\Eloquent\SubChapterRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\BlockRepositoryInterface::class,
            \App\Repositories\Eloquent\BlockRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\PurchaseRepositoryInterface::class,
            \App\Repositories\Eloquent\PurchaseRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\NoteRepositoryInterface::class,
            \App\Repositories\Eloquent\NoteRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
