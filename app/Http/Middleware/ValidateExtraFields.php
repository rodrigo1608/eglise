<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class ValidateExtraFields
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $modelNamespace, string $excludedQueryFieldsString = "")
    {
        $actionName = $request->route()->getActionMethod();

        $excludedQueryFields = explode('|', $excludedQueryFieldsString);

        $availableFields = array_keys($modelNamespace::$availableQueryFields);

        // Permite o uso de 'perPage' apenas na rota index
        if ($actionName === 'index') {
            $availableFields[] = 'perPage';
        }

        // Remove campos da query string que não devem ser permitidos para determinadas rotas,
        // permitindo que os campos aceitos sejam dinâmicos de acordo com o método da ação (ex: show, update, etc.)
        $excludedOnActions = ['show'];

        if (in_array($actionName, $excludedOnActions)) {
            $availableFields = array_diff($availableFields, $excludedQueryFields);
        }

        $invalidExtraFields = array_diff(array_keys($request->all()), $availableFields);

        if (!empty($invalidExtraFields)) {
            return ApiResponse::invalidData($invalidExtraFields, 'Campos extras inválidos');
        }

        return $next($request);
    }
}
