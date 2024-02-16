<?php

namespace App\Providers;

use App\Http\Services\ISBNEncoder;
use App\Http\Services\EncoderInterface;
use App\Http\Services\NYTHttp;
use App\Http\Services\NYTParser;
use App\Http\Services\ParserInterface;
use App\Http\Services\SimpleHttpInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        EncoderInterface::class => ISBNEncoder::class,
        SimpleHttpInterface::class => NYTHttp::class,
        ParserInterface::class => NYTParser::class
    ];
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
        //
    }
}
