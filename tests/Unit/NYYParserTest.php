<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Services\NYTParser;
use App\Http\Services\ParserInterface;

class NYYParserTest extends TestCase
{
    private ParserInterface $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = $this->app->make(NYTParser::class);
    }

    public function test_parse_body(): void
    {
        $msg = json_encode("Body");
        $result = $this->parser->body(200, $msg);
        $this->assertSame($result, "Body");

        $result = $this->parser->body(422, $msg);
        $this->assertSame($result, "Body");

        $result = $this->parser->body(500, $msg);
        $this->assertSame($result, config("services.messages.unavailable"));
    }

    public function test_parse_status(): void
    {

        $result = $this->parser->status(200, "Body");
        $this->assertSame($result, 200);

        $result = $this->parser->status(422, "Body");
        $this->assertSame($result, 422);

        $result = $this->parser->status(500, "Body");
        $this->assertSame($result, 503);
    }
}
