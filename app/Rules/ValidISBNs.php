<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class ValidISBNs implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $list, Closure $fail): void
    {
        foreach ($list as $isbn) {
            if(!is_string($isbn)) {
                $fail("$attribute is not in a supported format.");
            }
            $len = strlen($isbn);
            if (!in_array($len, [10, 13])) {
                $fail("$attribute ($isbn) is not a valid ISBN.");
            }
        }
    }
}
