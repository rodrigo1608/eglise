<?php

declare(strict_types = 1);

namespace App\Traits\Validations;

trait ValidatesPerPage
{
    /**
     * Método para retornar a regra de validação de paginação
     *
     * @return array<string> A regra de validação para o parâmetro de paginação
     */
    public function getPerPageRule(): array
    {
        return ['regex:/^\d+$|^all$/'];
    }

    /**
     * Retorna a mensagem de erro para uma validação do campo de paginação
     *
     * @return string  Mensagem de feedback
     */
    protected function getPerPageMessage(): string
    {
        return "Informe um número válido para paginação ou 'all' para exibir tudo.";
    }
}
