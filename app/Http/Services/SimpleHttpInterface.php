<?php

namespace App\Http\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

interface SimpleHttpInterface {
    public function get(array $query = []);
    /**@throws HttpResponseException */
    public function throw();
}
