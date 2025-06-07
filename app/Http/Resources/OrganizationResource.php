<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Resources\FiltersResourceAttributes;
use App\Traits\Resources\LoadIncludes;

class OrganizationResource extends JsonResource
{

    use FiltersResourceAttributes;
    use LoadIncludes;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        $response = [

            'id' => $this->resource->id,
            'name' => $this->resource->name ?? null,
            'email' => $this->resource->email ?? null,
            'logo' => $this->resource->logo ?? null,

            'created_at' => $this->whenNotNull(
                $this->created_at,
                fn() => $this->created_at?->toDateTimeString()
            ),

            'updated_at' => $this?->whenNotNull(
                $this->updated_at,
                fn() => $this->updated_at?->toDateTimeString()
            )
        ];

        $this->filterResponse($request, $response, 'organization');

        $resourceMaps = [
           'users' => getResourceNamespace('user'),
        ];

       $this->loadIncludes($request, $response,  $resourceMaps);

        return $response;
    }
}
