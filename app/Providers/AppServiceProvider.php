<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use App\Models\Rating;
use App\Models\Post;
use App\Models\Recipe;
use App\Policies\RatingPolicy;
use App\Policies\RecipePolicy;
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
        Gate::policy(Recipe::class, RecipePolicy::class);

        // Register observers
        Post::observe(PostObserver::class);
    }
}
