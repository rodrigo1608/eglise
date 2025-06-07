<?php

declare(strict_types = 1);

namespace App\Traits\Validations;

trait HasPatchValidation
{
    public function getPatchValidate($fullRules): array
    {
        $inputs = $this->all();

        return array_intersect_key($fullRules, $inputs);
    }
}
