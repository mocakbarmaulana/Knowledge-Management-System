<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleCreateRequest extends FormRequest
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
        $requiredString = 'required|string';

        return [
            'title' => $requiredString,
            'content' => $requiredString,
            'status' => 'required|in:draft,published',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    public function passedValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title),
            'user_id' => Auth::id(),
        ]);
    }
}
