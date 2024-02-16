<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Services\ISBNEncoder;
use App\Http\Services\EncoderInterface;

class ISBNEncoderTest extends TestCase
{
    private EncoderInterface $encoder;
    const VALID_ISBNS = [
        'aaaabbbbccccd',
        'aaaabbbbcc'
    ];
    protected function setUp(): void
    {
        parent::setUp();
        $this->encoder = $this->app->make(ISBNEncoder::class);
    }

    public function test_valid_encode(): void
    {
        $encoded = $this->encoder->encode(self::VALID_ISBNS);
        $this->assertSame(implode(";", self::VALID_ISBNS), $encoded);
    }

    public function test_valid_decode(): void
    {
        $encoded = $this->encoder->encode(self::VALID_ISBNS);
        $decoded = $this->encoder->decode($encoded);
        $this->assertSame(self::VALID_ISBNS, $decoded);
    }
}
