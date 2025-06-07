<?php

namespace App\Traits\Resources;

trait FiltersResourceAttributes
{
    protected function filterResponse($request, array &$response, string $resourceKey): void
    {

        $attributeKey = $resourceKey . '_attributes';

        if ($request->has($attributeKey)) {
            $attributes = explode(',', (string) $request->get($attributeKey));
            $response = array_intersect_key($response, array_flip($attributes));
        }
    }
}
