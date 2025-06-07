<?php

namespace App\Traits\Requests;

trait ApplyQueryFilter
{
    public function applyFilter($request, &$query, $extraFieldName)
    {
        if (!$request->has($extraFieldName)) {
            return;
        }

        $filters = explode(',',$request->get($extraFieldName));

        foreach ($filters as $filter) {
            $parts = explode(':', $filter, 3);

            if (count($parts) !== 3) {
                continue;
            }

            [$attribute, $operator, $value] = $parts;

            if ($operator === 'like') {
                $value = "%$value%";
            }

            $query->where($attribute, $operator, $value);
        }
    }
}
