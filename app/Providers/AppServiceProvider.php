<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use App\Models\Rating;
use App\Models\Post;
use App\Policies\RatingPolicy;
use App\Policies\PostPolicy;
use App\Observers\PostObserver;

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
        // Set application locale
        App::setLocale('vi');
        
        // Register policies
        Gate::policy(Rating::class, RatingPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        
        // Register observers
        Post::observe(PostObserver::class);
    }
}
