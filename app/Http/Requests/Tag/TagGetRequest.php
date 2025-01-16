<?php

namespace App\Http\Requests\Tag;

use App\Http\Requests\PaginationRequest;

class TagGetRequest extends PaginationRequest
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
