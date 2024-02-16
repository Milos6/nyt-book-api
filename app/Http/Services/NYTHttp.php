<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;

class NYTHttp implements SimpleHttpInterface
{
    private string $url;
    public function __construct(?string $url = null, private array $query = [])
    {
        if (is_null(config('services.nyt.key'))) {
            $this->throw();
        }
        if (is_null($url)) {
            if (is_null(config('services.nyt.url'))) {
                $this->throw();
            }
            $this->url = config('services.nyt.url');
        }

        $this->query = [
            "api-key" => config('services.nyt.key'),
        ];
    }

    public function throw()
    {
        $message = config('services.messages.no_config');
        Log::alert(["status" => "CFG_EX", "body" => $message]);
        throw new HttpResponseException(response()->json(
            ['errors' => $message],
            503,
            ['Content-Type: application/json']
        ));
    }

    public function get(array $query = [])
    {
        Log::info(["url" => $this->url, "query" => [...$this->query, ...$query]]);
        return Http::get($this->url, [
            ...$this->query,
            ...$query
        ]);
    }
}
