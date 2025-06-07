<?php

namespace App\Traits\Resources;

trait LoadIncludes
{
    public function loadIncludes($request, &$response,  $resourceMaps)
    {

        if ($request->has('includes')) {

            $includesString = $request->get('includes');

            $includes = explode(',', (string) $includesString);

            foreach ($includes as $relation) {

                if (array_key_exists($relation, $resourceMaps)) {

                    $resourceClass = $resourceMaps[$relation];

                    $related = $this->$relation;

                    $response[$relation] = is_a($related, \Illuminate\Support\Collection::class)
                    ? $resourceClass::collection($related)
                    : new $resourceClass($related);
                }

            }

        }
    }

}
