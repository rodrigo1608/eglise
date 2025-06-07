<?php

declare(strict_types=1);

namespace App\Traits\Validations\FormRequests;

use App\Logs\LogPlus;

trait ExtractsModelNamespaceFromRequest
{


    /**
     * Obtém o nome da Model a partir da requisição.
     *
     * @return string|null O nome da Model ou null se não for possível determinar.
     */
    public function getModelNamespaceFromRequest(): ?string
    {
        $routeName = $this->route()->getName();

        if (is_null($routeName)) {
            $routePath = $this->route()->uri();
            $routeMethods = implode(', ', $this->route()->methods());

            LogPlus::backtrace(
                'warning',
                "Rota não nomeada detectada durante a extração do namespace da Model. Detalhes: URI: '{$routePath}', Métodos HTTP: [{$routeMethods}].",
                ['context' => 'Extração do namespace da Model']
            );

            return null;
        }

        $parts = explode('.', (string) $routeName);

        if (count($parts) < 2) {
            return null;
        }

        $modelNameLower = $parts[0];

        $modelName = ucfirst($modelNameLower);

        return  getModelNamespace($modelName);
    }
}
