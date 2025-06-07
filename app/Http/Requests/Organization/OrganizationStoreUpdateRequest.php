<?php

declare(strict_types=1);

namespace App\Http\Requests\Organization;

use App\Http\Requests\ApiFormRequest;
use App\Traits\Validations\HasPatchValidation;
use Illuminate\Validation\Rule;
use App\Logs\LogPlus;
use App\Http\Responses\ApiResponse;
use App\Traits\Validations\FormRequests\ExtractsModelNamespaceFromRequest;

class OrganizationStoreUpdateRequest extends ApiFormRequest
{
    use HasPatchValidation;
    use ExtractsModelNamespaceFromRequest;

    /**
     * Determine se o usuário está autorizado a fazer esta solicitação.
     */
    public function authorize(): bool
    {
        // Você pode modificar isso de acordo com a lógica de autorização.
        return true;
    }

    /**
     * Obtenha as regras de validação para a solicitação.
     * @return mixed[]
     */
    public function rules(): array
    {

        $organizationId = $this->route('organization');

        $fullRules = [

            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('organizations', 'name')->ignore($organizationId),
            ],

            'email' => [
                'nullable',
                'email',
                Rule::unique('organizations', 'email')->ignore($organizationId),
            ],

            'logo' => [
            'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,bmp,webp,svg,tiff',
                'max:2048',
            ],

        ];
        $isUpdateMethod = $this->method() === 'PATCH' || $this->method() === 'PUT';

        return $isUpdateMethod
            ? $this->getPatchValidate($fullRules)
            : $fullRules;
    }

    #[\Override]
    public function messages(): array
    {
        return [
            'name.required'     => 'O campo nome é obrigatório.',
            'name.unique'       => 'O nome da organização informado já está em uso.',
            'name.string'       => 'O campo nome deve ser uma string.',
            'name.max'          => 'O campo nome não pode ter mais de 100 caracteres.',
            'email.email'       => 'O campo email deve ser um endereço de e-mail válido.',
            'email.unique'      => 'O e-mail informado já está em uso.',
            'logo.file'         => 'O arquivo do logo deve ser um arquivo válido.',
            'logo.mimes'        => 'O logo deve ser uma imagem nos formatos: jpeg, png, jpg, gif, bmp, webp, svg ou tiff.',
            'logo.max'          => 'O logo não pode ter mais de 2MB.',

        ];
    }
}
