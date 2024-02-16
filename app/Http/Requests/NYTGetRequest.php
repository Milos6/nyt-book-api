<?php

namespace App\Http\Requests;

use Closure;
use App\Rules\ValidISBNs;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NYTGetRequest extends FormRequest
{
    const OFFSET_MULTIPLE = 20;

    protected $redirectRoute = 'best-sellers';
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'author' => 'string',
            'isbn' => [
                'array',
                new ValidISBNs
            ],
            'title' => 'string',
            'offset' => 'integer|multiple_of:' . self::OFFSET_MULTIPLE
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
