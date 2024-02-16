<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;

class NYTGetRequestTest extends TestCase
{
    const MULTIPLE_OF = 20;
    const URL = "/api/1/nyt/best-sellers";
    const VALID_PARAMS = [
        'isbn' => [
            'aaaabbbbcc',
            'aaaabbbbccccd'
        ],
        'author' => 'asdc',
        'offset' => '40'
    ];

    const NYT_200_RESPONSE = [
        "status" => "OK",
        "copyright" => "Copyright (c) 2019 The New York Times Company.  All Rights Reserved.",
        "num_results" => 28970,
        "results" => [
            [
                "title" => "#GIRLBOSS",
                "description" => "An online fashion retailer traces her path to success.",
                "contributor" => "by Sophia Amoruso",
                "author" => "Sophia Amoruso",
                "contributor_note" => "",
                "price" => 0,
                "age_group" => "",
                "publisher" => "Portfolio/Penguin/Putnam",
                "isbns" => [
                    [
                        "isbn10" => "039916927X",
                        "isbn13" => "9780399169274"
                    ]
                ],
                "ranks_history" => [
                    [
                        "primary_isbn10" => "1591847931",
                        "primary_isbn13" => "9781591847939",
                        "rank" => 8,
                        "list_name" => "Business Books",
                        "display_name" => "Business",
                        "published_date" => "2016-03-13",
                        "bestsellers_date" => "2016-02-27",
                        "weeks_on_list" => 0,
                        "ranks_last_week" => null,
                        "asterisk" => 0,
                        "dagger" => 0
                    ]
                ],
                "reviews" => [
                    [
                        "book_review_link" => "",
                        "first_chapter_link" => "",
                        "sunday_review_link" => "",
                        "article_chapter_link" => ""
                    ]
                ]
            ]
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    private function fakeSuccess()
    {

        Http::fake([
            config('services.nyt.url') . "?*" => Http::response(
                self::NYT_200_RESPONSE,
                200,
                ['Content-Type: application/json']
            ),
        ]);
    }

    public function getWithUri(string $uri)
    {
        return $this->call(method: 'GET', uri: $uri, server: ['Accept' => 'application/json']);
    }

    public function generateUrl(string $url = self::URL, object|array $query = self::VALID_PARAMS, string $separator = '?')
    {
        $queryString = http_build_query($query);
        return $url . $separator . $queryString;
    }
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->fakeSuccess();

        $response = $this->getWithUri($this->generateUrl());

        $response->assertStatus(200)->assertJson(self::NYT_200_RESPONSE);
    }

    public function test_the_isbn_code_is_valid(): void
    {
        $this->fakeSuccess();

        $params = [
            ...self::VALID_PARAMS,
            'isbn' => [
                "aaaabbbb10",
                "aaaabbbbccc13",
                "aaaabbbbccc13",
            ]
        ];

        $response = $this->getWithUri($this->generateUrl(query: $params));

        $response->assertStatus(200)->assertJson(self::NYT_200_RESPONSE);
    }

    public function test_the_isbn_code_is_wrong_length(): void
    {
        $this->fakeSuccess();

        $params = [
            ...self::VALID_PARAMS,
            'isbn' => [
                "aa"
            ]
        ];

        $response = $this->getWithUri($this->generateUrl(query: $params));

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => ['isbn'],
        ]);
    }

    public function test_the_isbn_code_is_wrong_length_2(): void
    {
        $this->fakeSuccess();

        $params = [
            ...self::VALID_PARAMS,
            'isbn' => [
                "aaaabbbbcc",
                "aaaabbbbccdd",
                "aaaabbbbccccd",
            ]
        ];

        $response = $this->getWithUri($this->generateUrl(query: $params));

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => ['isbn'],
        ]);
    }

    public function test_the_offset_is_invalid_multiple_of(): void
    {
        $this->fakeSuccess();

        $params = [
            ...self::VALID_PARAMS,
            'offset' => 31
        ];

        $response = $this->getWithUri($this->generateUrl(query: $params));

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => ['offset'],
        ]);
    }

    public function test_503_fail()
    {
        Http::fake([
            config('services.nyt.url') . "?*" => Http::response(
                json_encode("Error"),
                500,
                ['Content-Type: application/json']
            )
        ]);

        $response = $this->getWithUri($this->generateUrl());
        $response->assertStatus(503);
        $this->assertEquals($response->json(), ['errors' => config('services.messages.unavailable')]);
    }

    public function test_422_fail()
    {
        $msg = "Validation fail";
        Http::fake([
            config('services.nyt.url') . "?*" => Http::response(
                json_encode($msg),
                422,
                ['Content-Type: application/json']
            )
        ]);

        $response = $this->getWithUri($this->generateUrl());
        $response->assertStatus(422);
        $response->assertJson(['errors' => $msg]);
    }
}
