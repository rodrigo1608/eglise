<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\Resources\FiltersResourceAttributes;
use App\Traits\Resources\LoadIncludes;

class UserResource extends JsonResource
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

            'organization_id' => $this->resource->organization_id,

            'firstname' => $this->resource->firstname,

            'lastname' => $this->resource->lastname,

            'full_name' => $this->resource->getFullName(),

            'email' => $this->resource->email,

            'profile_picture' => url($this->resource->profile_picture),

            'email_verified_at' => $this->resource->email_verified_at?->toIso8601String(),

            'created_at' => $this->whenNotNull(
                $this->created_at,
                fn() => $this->created_at?->toIso8601String()
            ),

            'updated_at' => $this->whenNotNull(
                $this->updated_at,
                fn() => $this->updated_at?->toIso8601String()
            )

        ];

        $this->filterResponse($request, $response, 'user');

        $resourceMaps = [
            'organization' => getResourceNamespace('organization'),
        ];

        $this->loadIncludes($request, $response,  $resourceMaps);

        return $response;
    }
}
