<?php

declare(strict_types = 1);

namespace App\Traits\Resources;

trait ResolvesRelatedResourceNamespaces
{
    protected function getRelatedResourceNamespaces($relatedRelationship): ?array
    {
        $modelNamespace = $this->resource::class;

        $modelRelationshipMappings = $modelNamespace::$relationshipMappings;

        $filteredMappings = array_filter(
            $modelRelationshipMappings,
            fn ($relationshipValue): bool => in_array($relationshipValue, $relatedRelationship)
        );

        $resourceNamespaces = [];

        foreach ($filteredMappings as $model => $relationship) {
            $resourceNamespaces['App\\Http\\Resources\\' . $model . 'Resource'] = $relationship;
        }

        return  $resourceNamespaces;
    }
}
