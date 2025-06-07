<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ShowRequest extends IndexRequest
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
        $rules = parent::rules();

        // Remove a validação de perPage se estiver presente
        unset($rules['perPage']);

        return $rules;
    }
}
