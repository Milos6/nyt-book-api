<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\NYTGetRequest;
use App\Http\Services\EncoderInterface;
use App\Http\Services\ParserInterface;
use App\Http\Services\SimpleHttpInterface;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class NYTController extends Controller
{

    public function __construct(private EncoderInterface $isbnEncoder, private SimpleHttpInterface $http, private ParserInterface $parser)
    {
    }

    public function get(NYTGetRequest $request)
    {
        $params = [];
        if ($request->has('isbn')) {
            $params['isbn'] = $this->isbnEncoder->encode($request->get('isbn'));
        }
        if ($request->has('author')) {
            $params['author'] = $request->input('author');
        }
        if ($request->has('offset')) {
            $params['offset'] = $request->input('offset');
        }
        if ($request->has('title')) {
            $params['title'] = $request->input('title');
        }


        try {
            $response = $this->http->get($params);
        } catch (HttpResponseException) {
        } catch (Exception $ex) {
            Log::alert(["status" => "EX", "exception" => $ex]);
            $this->throw(config("services.messages.unavailable"), 503, true);
        }
        if ($response->failed()) {
            $this->throw($response->body(), $response->status());
        }

        return response()->json(
            $this->parser->body(200, $response->body()),
            $this->parser->status(200)
        );
    }

    public function throw(string $body, int $status, bool $supressLog = false)
    {
        if (!$supressLog) {
            Log::alert(["status" => $status, "body" => $body]);
        }
        throw new HttpResponseException(response()->json(
            ['errors' => $this->parser->body($status, $body)],
            $this->parser->status($status),
            ['Content-Type: application/json']
        ));
    }
}
