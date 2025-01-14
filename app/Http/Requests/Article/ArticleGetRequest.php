<?php

namespace App\Http\Requests\Article;

use App\Http\Requests\PaginationRequest;
use Illuminate\Foundation\Http\FormRequest;

class ArticleGetRequest extends PaginationRequest
{
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
        $parent = parent::rules();

        return array_merge($parent, [
            'search' => 'nullable|string',
        ]);
    }
}
