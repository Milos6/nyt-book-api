<?php

namespace App\Http\Services;

interface EncoderInterface
{
    public function encode(array $isbns, string $separator = ";"): string;
    public function decode(string $encodedIsbns, string $separator = ";"): array;
}
