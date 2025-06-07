<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Logs\LogPlus;

use App\Rules\CommaSeparatedValuesIn;

use App\Rules\ValidFilters;

use App\Traits\Validations\FormRequests\ExtractsModelNamespaceFromRequest;

use App\Traits\Validations\ValidatesPerPage;

use App\Traits\Validations\ValidatesRelationships;
use Illuminate\Validation\ValidationException;

class IndexRequest extends ApiFormRequest
{
    use ExtractsModelNamespaceFromRequest;
    use ValidatesPerPage;
    use ValidatesRelationships;

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
        $modelNamespace = $this->getModelNamespaceFromRequest();

        $permittedAttributes = $modelNamespace::$availableQueryFields;

        $rules = [];

        foreach (array_keys($this->query()) as $attribute) {

            $rules[$attribute] = match ($attribute) {
                'perPage'    => $this->getPerPageRule(),
                'includes'   => ['required', new CommaSeparatedValuesIn($permittedAttributes['includes'])],
                'organization_attributes' => ['required', new CommaSeparatedValuesIn($permittedAttributes['organization_attributes'])],
                'organization_filters' => ['required', new ValidFilters($permittedAttributes['organization_filters'])],
                'user_attributes' => ['required', new CommaSeparatedValuesIn($permittedAttributes['user_attributes'])],
                'user_filters' =>['required', new ValidFilters($permittedAttributes['user_filters'])],
                default => throw ValidationException::withMessages([
                    $attribute =>"O parâmetro '{$attribute}' foi recebido, mas não foi registrado nas regras de validação em " . static::class
                ]),
            };
        }
        return $rules;
    }

    #[\Override]
    public function messages()
    {
        return [
            'perPage.regex'       => $this->getPerPageMessage(),
            'includes.required'   => $this->getRequiredRelationshipsValidationMessage(),
            'includes.array'      => $this->getInRelationshipsValidationMessage(),
            'attributes.required' => "O parâmetro 'attributes' deve conter ao menos um atributo válido.",
            'user_attributes.required' => "O parâmetro 'user_attributes' deve conter ao menos um atributo válido."
        ];
    }
}
