<?php

namespace App\Http\Services;

class ISBNEncoder implements EncoderInterface
{
    public function encode(?array $isbns, string $separator = ";"): string
    {
        return implode($separator, $isbns);
    }
    public function decode(string $encodedIsbns, string $separator = ";"): array
    {
        return explode($separator, $encodedIsbns);
    }
}
