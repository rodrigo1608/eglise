<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Logs\LogPlus;
use Illuminate\Http\JsonResponse;

class ApiResponse
{

    public static function success(mixed $data, string $method = '', int $statusCode = 200): JsonResponse
    {
        // Determinar se é uma coleção (para verificar singular ou plural)
        $count = method_exists($data, 'count') ? $data->count() : (is_array($data) ? count($data) : 1);
        $pluralEnding = $count > 1 ? 's' : '';

        // Definir a mensagem de sucesso com base no método
        $message = match ($method) {
            'index' => empty($data)
                ? "Não encontramos nenhum registro correspondente aos parâmetros informados."
                : "Registro{$pluralEnding} recuperado{$pluralEnding} com sucesso!",

            'show' => "Registro recuperado com sucesso!",

            'store' => "Registro{$pluralEnding} criado{$pluralEnding} com sucesso!",

            'update' => "Registro{$pluralEnding} atualizado{$pluralEnding} com sucesso!",

            'destroy' => "Registro{$pluralEnding} deletado{$pluralEnding} com sucesso!",

            default => "Operação realizada com sucesso!",
        };

        // Criar a resposta base
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

        // Adicionar a mensagem à resposta
        $response['message'] = $message;

        LogPlus::log('info', $data, $message, $statusCode);

        // Retornar a resposta JSON
        return response()->json($response, $statusCode);
    }

    public static function notFound(mixed $data = null, ?string $message = "", ?int $statusCode = 404): JsonResponse
    {
        LogPlus::log('warning', $data, $message, $statusCode);

        return response()->json([
            'status'  => 'error',
            'message' => 'Recurso não encontrado',
        ], 404);
    }

    public static function invalidData(mixed $data, ?string $message = "", ?int $statusCode = 422): JsonResponse
    {
        $clientMessage = $message ?? "Dados de entrada inválidos.";

        LogPlus::log('warning', $data, $clientMessage, $statusCode);

        return response()->json([
            'status'           => 'invalid_data',
            'message'          => $clientMessage,
            'invalid_input(s)' => $data,
        ], $statusCode);
    }

    public static function error(mixed $data, ?string $message = null, ?int $statusCode = null, ?array $errors = null): JsonResponse
    {
        $statusCode ??= 500;
        $isException = $data instanceof \Throwable;

        if ($isException) {
            $message ??= $data->getMessage() ?: 'Erro interno do servidor';
            $errors ??= ['exception' => get_class($data)];
        } else {
            $message ??= 'Erro interno do servidor';
            $errors ??= is_array($data) ? $data : ['error' => $data];
        }

        // Log detalhado
        LogPlus::log(
            'error',
            $data,
            $message,
            $statusCode,
            $isException ? ['trace' => $data->getTraceAsString()] : []
        );

        // Monta o corpo da resposta
        $responseData = [
            'status'  => 'error',
            'message' => $message,
        ];

        // Só mostra detalhes se for erro do cliente ou ambiente não for produção
        if ($statusCode < 500 || config('app.env') !== 'production') {
            $responseData['errors'] = $errors;
        }

        return response()->json($responseData, $statusCode);
    }
}
