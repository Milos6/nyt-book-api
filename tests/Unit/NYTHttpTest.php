<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Services\NYTHttp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class NYTHttpTest extends TestCase
{
    private string $url;
    private string $key;
    protected function setUp(): void
    {
        parent::setUp();
        $this->url = config('services.nyt.url');
        $this->key = config('services.nyt.key');
    }

    public function test_url_variable_not_set_and_url_not_passed(): void
    {
        $this->app['config']->set('services.nyt.url', null);
        $this->expectException(HttpResponseException::class);
        new NYTHttp();
    }

    public function test_url_variables_not_set_url_and_passed(): void
    {
        $this->app['config']->set('services.nyt.url', null);
        try {
            new NYTHttp($this->url);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
        $this->assertTrue(true);
    }

    public function test_api_variable_not_set(): void
    {
        $this->app['config']->set('services.nyt.key', null);
        $this->expectException(HttpResponseException::class);
        new NYTHttp();
    }

    protected function tearDown(): void
    {
        $this->app['config']->set('services.nyt.url', $this->url);
        $this->app['config']->set('services.nyt.key', $this->key);
    }
}
