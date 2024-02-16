<?php

namespace App\Http\Services;

class NYTParser implements ParserInterface
{
    public function body(int $status, string $body): mixed
    {
        return match ($status) {
            200 => json_decode($body),
            422 => json_decode($body),
            default => config('services.messages.unavailable')
        };
    }
    public function status(int $status): int
    {
        return match ($status) {
            200 => 200,
            422 => 422,
            default => 503,
        };
    }
}
