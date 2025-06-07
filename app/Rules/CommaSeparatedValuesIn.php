<?php

declare(strict_types = 1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CommaSeparatedValuesIn implements ValidationRule
{
    /**
     * Indicates whether the rule should be implicit.
     */
    public bool $implicit = true;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected array $allowed){}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $submittedValues   = array_map('trim', explode(',', (string) $value));
        $invalidAttributes = array_diff($submittedValues, $this->allowed);

        if ($invalidAttributes !== []) {
            $amountInvalidValues = count($invalidAttributes);

            $lastInvalidValue = array_pop($invalidAttributes);

            $messageSingular = 'O campo :attribute contém um valor inválido: ';

            $messagePlural = 'O campo :attribute contém valores inválidos: ';

            $message = ($amountInvalidValues > 1)
                ? $messagePlural . implode(', ', $invalidAttributes) . ' e ' . $lastInvalidValue . '.'
                : $messageSingular . $lastInvalidValue;

            $fail($message)->translate();
        }
    }
}
