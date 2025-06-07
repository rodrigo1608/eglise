<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureFieldsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $modelNamespace): Response
    {
        $permittedAttributes = $modelNamespace::$availableQueryFields['organization_attributes'];

        $requestFields = array_keys($request->all());

        $invalidFields = array_diff($requestFields, $permittedAttributes);

        if (!empty($invalidFields)) {

            return ApiResponse::invalidData($invalidFields);

        }

        return $next($request);
    }
}
