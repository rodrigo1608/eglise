<?php

declare(strict_types = 1);

namespace App\Traits\Validations;

/**
 * Trait para validar os relacionamentos (includes) permitidos passados na query string.
 */
trait ValidatesRelationships
{
    /**
     * Gera regras de validação para o parâmetro 'includes' da query string.
     *
     * @return array Regras de validação para o parâmetro 'includes'.
     */
    public function getIncludesRule(): array
    {
        $modelNamespace = $this->getModelNamespaceFromRequest();

        $permittedRelationships = $modelNamespace::$relationshipMappings;

        $imploded = implode(',', $permittedRelationships);

        return [
            'required',
            'array:' . $imploded,
        ];

        // Utiliza a trait BuildsArrayRules para gerar um array com as regras de validação dos relacionamentos
        // return $this->getArrayRules(
        //     $permittedRelationships, // Lista de atributos permitidos.

        //     $this->get('includes'), // Atributos passados na query string.

        //     $this, // Instância do FormRequest.

        //     'includes' // Nome do campo a ser validado.
        // );
    }

    protected function getRequiredRelationshipsValidationMessage(): string
    {
        return "O parâmetro 'includes' não pode estar vazio. Insira um ou mais relacionamentos válidos.";
    }

    protected function getInRelationshipsValidationMessage(): string
    {
        return "Os relacionamentos informados são inválidos. Por favor, verifique os relacionamentos permitidos.";
    }
}
