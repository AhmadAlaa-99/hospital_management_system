<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // أسماء الأقسام/الأطباء محفوظة بالعربي؛ فعّل الرجوع للعربي عند فتح الموقع بـ /en
        config([
            'translatable.use_fallback' => true,
            'translatable.use_property_fallback' => true,
            'translatable.fallback_locale' => 'ar',
        ]);

        \Illuminate\Support\Facades\View::composer(
            ['WebSite.layouts.footer', 'WebSite.layouts.master', 'WebSite.layouts.header'],
            function ($view) {
                if (!\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
                    return;
                }
                $view->with('siteSetting', \App\Models\SiteSetting::current());
                $view->with('footerSections', \Illuminate\Support\Facades\Schema::hasTable('sections')
                    ? \App\Models\Section::with('translations')->take(6)->get()
                    : collect());
                $view->with('footerBlogs', \Illuminate\Support\Facades\Schema::hasTable('blogs')
                    ? \App\Models\Blog::where('is_published', true)->latest('published_at')->take(2)->get()
                    : collect());
            }
        );
    }
}
