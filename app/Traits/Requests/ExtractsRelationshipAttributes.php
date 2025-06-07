<?php

namespace App\Traits\Requests;

trait ExtractsRelationshipAttributes
{
    /**
     * Extrai atributos solicitados de uma relação e garante a inclusão de um atributo obrigatório.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $key Nome da chave da querystring (ex: 'user_attributes')
     * @param string|null $requiredAttribute Atributo que deve ser incluído (ex: 'organization_id')
     * @return string|null Retorna string com atributos separados por vírgula ou null se a chave não existir
     */
    public function extractRelationshipAttributes($request, string $queryParam, ?string $requiredAttribute = null): ?string
    {
        if (!$request->has($queryParam)) {
            return null;
        }

        $attributes = explode(',', $request->get($queryParam));

        if ($requiredAttribute) {
            $attributes[] = $requiredAttribute;
        }

        return ':' . implode(',', array_unique($attributes));
    }
}
