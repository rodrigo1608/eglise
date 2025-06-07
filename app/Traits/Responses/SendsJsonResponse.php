<?php

declare(strict_types = 1);

namespace App\Traits\Responses;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


trait SendsJsonResponse
{
    protected function successResponse(mixed $data, int $status = 200, ?string $method = null)
    {
        $message = match ($method) {
            'index' => empty($data->all())
            ? "Não encontramos nenhum registro correspondente aos parâmetros informados."
            : "Registros de recuperados com sucesso!",

            'show' => "Registro recuperado com sucesso!",

            'store' => "Registro criado com sucesso!",

            'update' => "Registro de atualizado com sucesso!",

            default => "Operação realizada com sucesso!"
        };

        $response = [
            'data' => $data,
        ];

        $additionalData = $data->additional['changes'] ?? null;

        if (isset($additionalData)) {
            $response['changes'] = empty($additionalData)
                ? 'Os dados foram atualizados com os mesmos valores, por isso não houve mudança.'
                : $additionalData;
        }

        $response['message'] = $message;

        return response()->json($response, $status);
    }

    protected function notFoundResponse()
    {
        return response()->json([
            'message' => "Nenhum resultado encontrado. Verifique os critérios de busca e tente novamente.",
        ], 404);
    }

    public function exceptionResponse(Exception $e, string $context)
    {
        Log::error(self::class . " " . $context . ' :: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(),
            'context'                                                                 => $context]);

        return response()->json([
            'error' => $context,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
