<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFilters implements ValidationRule
{

    public function __construct(
        protected array $allowedFields,
        protected array $allowedOperators = ['=', 'like', '>', '<', '>=', '<=']
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filters = explode(',', $value);

        foreach ($filters as $filter) {

            $parts = explode(':', $filter);

            if (count($parts) !== 3) {
                $fail("O filtro '$filter' está mal formatado. Use o formato campo:operador:valor.");
               continue;
            }

            [$attribute, $operator] = $parts;

            if (!in_array($attribute, $this->allowedFields)) {
                $fail("O campo '$attribute' não é permitido para filtro.");
            }

            if (!in_array($operator, $this->allowedOperators)) {
                $fail("O operador '$operator' não é permitido. Campos permitidos:  $this->allowedOperators.");
            }

            // O valor ($filterValue) pode ser qualquer coisa, então você pode ignorar a validação dele aqui.
        }
    }
}
