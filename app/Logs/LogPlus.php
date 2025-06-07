<?php

declare(strict_types=1);

namespace App\Logs;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class LogPlus extends Log
{

    public static function log(
        string $level,
        mixed $data,
        ?string $message = null,
        ?int $statusCode = null,
        array $extraContext = []
    ): void {
        self::backtrace($level, $message, array_merge([
            'data'        => $data,
            'status_code' => $statusCode,
            'method'      => request()->method(),
            'request_id'  => request()->header('X-Request-ID') ?? uniqid(),
        ], $extraContext));
    }

    /**
     * Loga uma mensagem com informações de backtrace.
     * Se $message for um array, ele será automaticamente formatado como uma string.
     *
     * @param string $level O nível do log (ex: 'error', 'info', 'debug').
     * @param mixed $message A mensagem a ser logada (pode ser uma string ou um array).
     * @param array $context Contexto adicional para o log.
     * @param int $backtraceLevel Níveis de backtrace a serem capturados (padrão: 1).
     * @throws InvalidArgumentException Se o nível de log for inválido.
     */
    public static function backtrace(string $level, mixed $message, array $context = [], int $backtraceLevel = 1): void
    {
        $validLogLevels = [
            'alert',
            'critical',
            'debug',
            'emergency',
            'error',
            'info',
            'notice',
            'warning',
        ];

        // Valida o nível de log
        if (! in_array($level, $validLogLevels)) {
            throw new InvalidArgumentException("Nível de log inválido: $level");
        }

        // Formata a mensagem automaticamente se for um array
        $formattedMessage = self::getStringMessage($message);

        // Obtém o backtrace
        $backtrace = self::getBacktrace($backtraceLevel);

        // Adiciona informações de arquivo e linha ao contexto
        $context = array_merge($context, [
            'file' => $backtrace['file'] ?? 'unknown',
            'line' => $backtrace['line'] ?? 0,
        ]);

        // Realiza o log usando o método da classe pai
        parent::log($level, $formattedMessage, $context);
    }

    /**
     * Obtém informações do backtrace.
     *
     * @param int $backtraceLevel Níveis de backtrace a serem capturados.
     * @return array Informações do backtrace.
     */
    protected static function getBacktrace(int $backtraceLevel = 1): array
    {
        try {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $backtraceLevel + 1);

            return $backtrace[$backtraceLevel] ?? [];
        } catch (\Throwable) {
            // Fallback em caso de erro no debug_backtrace
            return ['file' => 'unknown', 'line' => 0];
        }
    }

    /**
     * Formata uma mensagem com base no tipo de $data.
     * Se $data for um array, ele será convertido em uma string separada por vírgulas.
     * Se $data for uma string, será retornado diretamente.
     *
     * @param mixed $data Os dados a serem formatados (pode ser um array ou uma string).
     * @param string $context O contexto da mensagem (opcional).
     * @return string A mensagem formatada.
     */
    public static function getStringMessage(mixed $data, string $context = ''): string
    {
        // Converte arrays em strings separadas por vírgulas
        $formattedData = is_array($data) ? implode(', ', $data) : $data;

        // Adiciona o contexto à mensagem, se fornecido
        return $context !== '' && $context !== '0' ? "$context: $formattedData" : $formattedData;
    }
}
