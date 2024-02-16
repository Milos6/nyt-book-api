<?php
namespace App\Http\Services;

interface ParserInterface {
    public function body(int $status, string $body): mixed;
    public function status(int $status): int;
}
