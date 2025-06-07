<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\ApiFormRequest;
use App\Traits\Validations\HasPatchValidation;
use Illuminate\Validation\Rule;

class UserStoreUpdateRequest extends ApiFormRequest
{
    use HasPatchValidation;

    public function authorize(): bool
    {
        return true; // Alterar conforme a lógica de autorização da sua aplicação
    }

    public function rules(): array
    {

        $userId = $this->route('user');

        $fullRules = [
            'firstname' => ['required', 'string', 'max:30'],

            'lastname' => ['required', 'string', 'max:60'],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email',)->ignore($userId),
            ],

            'password' => ['required', 'min:8', 'confirmed'],

            'profile_picture' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,bmp,webp,svg,tiff',
                'max:2048',
            ],

            'profession' => ['nullable', 'string', 'max:100'],

            'organization_id' => ['required', 'exists:organizations,id'],
        ];

        $isUpdateMethod = $this->method() === 'PATCH' || $this->method() === 'PUT';

        return $isUpdateMethod
            ? $this->getPatchValidate($fullRules)
            : $fullRules;

        return $fullRules;
    }

    #[\Override]
    public function messages(): array
    {
        return [

            'firstname.required' => 'O primeiro nome é obrigatório.',

            'firstname.max' => 'O primeiro nome deve ter no máximo :max caracteres.',

            'lastname.required' => 'O sobrenome é obrigatório.',

            'lastname.max' => 'O sobrenome deve ter no máximo :max caracteres.',

            'email.required' => 'O email é obrigatório.',

            'email.email' => 'Informe um email válido.',

            'email.unique' => 'Esse email já está cadastrado.',

            'password.required' => 'A senha é obrigatória.',

            'password.min' => 'A senha deve ter pelo menos :min caracteres.',

            'password.confirmed' => 'As senhas não coincidem.',

            'profile_picture.image' => 'A foto de perfil deve ser uma imagem.',

            'profile_picture.max' => 'A foto de perfil deve ser uma imagem nos formatos: jpeg, png, jpg, gif, bmp, webp, svg ou tiff.',

            'organization_id.required' => 'A organização é obrigatória.',

            'organization_id.exists' => 'A organização selecionada não existe.',
        ];
    }
}
