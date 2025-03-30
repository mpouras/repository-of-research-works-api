<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\Publisher;
use App\Models\User;
use App\Models\Volume;
use App\Observers\ArticleObserver;
use App\Observers\IssueObserver;
use App\Observers\PublicationObserver;
use App\Observers\PublisherObserver;
use App\Observers\UserObserver;
use App\Observers\VolumeObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Publisher::observe(PublisherObserver::class);
        Publication::observe(PublicationObserver::class);
        Volume::observe(VolumeObserver::class);
        Issue::observe(IssueObserver::class);
        Article::observe(ArticleObserver::class);
    }
}
