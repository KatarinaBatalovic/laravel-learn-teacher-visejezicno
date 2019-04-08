<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Model\Page;
use Illuminate\Support\Facades\View;

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
        Schema::defaultStringLength(191);
        
        if(!app()->runningInConsole()){
            $topLevelPages = Page::notdeleted()
                    ->toplevel()
                    ->active()
                    ->orderBy('order_number', 'ASC')
                    ->get();
            view()->share('pagesTopLevel', $topLevelPages);
        }
        
        
    }
}
