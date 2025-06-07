<?php

if (!function_exists('getModelNamespace')) {
    function getModelNamespace(string $modelName): ?string
    {
        if (is_null($modelName)) {
            return null;
        }

        $modelNamespace = 'App\\Models\\' . ucfirst($modelName);

        return class_exists($modelNamespace) ? $modelNamespace : null;
    }
}

if (!function_exists('getResourceNamespace')) {
    function getResourceNamespace(string $resourceName): ?string
    {
        if (is_null($resourceName)) {
            return null;
        }

        $resourceNamespace = 'App\Http\Resources\\' . ucfirst($resourceName) . 'Resource';

        return class_exists($resourceNamespace) ? $resourceNamespace : null;
    }
}
