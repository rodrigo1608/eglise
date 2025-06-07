<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\Organization\OrganizationStoreUpdateRequest;
use App\Http\Requests\ShowRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Traits\Fetches\FetchesIndexData;
use App\Traits\Requests\ExtractsRelationshipAttributes;
use App\Traits\Requests\ApplyQueryFilter;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{

    use FetchesIndexData;
    use ExtractsRelationshipAttributes;
    use ApplyQueryFilter;

    public function index(IndexRequest $request): JsonResponse
    {
        $query                 = Organization::query();

        $this->applyFilter($request, $query, 'organization_filters');

        $userAttributes = $this->extractRelationshipAttributes($request, 'user_attributes', 'organization_id');

        $query->with('users' . $userAttributes);

        $organizations         = $this->fetchesIndexData($request, $query);

        $organizationCollection = OrganizationResource::collection($organizations);


        // $organizationCollection = new  OrganizationCollection($organizations);

        return ApiResponse::success($organizationCollection, 'index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrganizationStoreUpdateRequest $request): JsonResponse
    {

        DB::beginTransaction();

        try {

            $validatedData = $request->validated();

            $organizationLogo = $request->file('logo');

            $validatedData['logo'] = $organizationLogo?->store('images/logos');

            $organization = Organization::create($validatedData);

            $organization->refresh();

            DB::commit();

            $organizationResource = new OrganizationResource($organization);

            return ApiResponse::success($organizationResource, 'store');
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                $e,
                "Falha ao processar o armazenamento da organização."
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowRequest $request, $id): JsonResponse
    {
        try {

            $query                 = Organization::query();

            $this->applyFilter($request, $query, 'organization_filters');

            $userAttributes = $this->extractRelationshipAttributes($request, 'user_attributes', 'organization_id');

            $organization                = $query->with('users' . $userAttributes)->findOrFail($id);

            $organizationResource = new OrganizationResource($organization);

            return ApiResponse::success($organizationResource, 'show');

        } catch (ModelNotFoundException $e) {

            return ApiResponse::notFound($id);
        } catch (Exception $e) {

            return ApiResponse::error(
                $e,
                "Falha ao processar o armazenamento da organização."
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganizationStoreUpdateRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {

            $organization    = Organization::findOrFail($id);
            $oldOrganization = $organization->toArray();
            $validatedData   = $request->validated();

            if ($request->hasFile('logo')) {

                Storage::delete($oldOrganization['logo']);

                $validatedData['logo'] = $request->file('logo')->store('images/logos');
            }

            $organization->update($validatedData);

            $organization->refresh();

            $newOrganization = $organization->toArray();

            $changes = [];

            foreach ($oldOrganization as $key => $value) {

                $hasChanged = $key !== 'updated_at' && $newOrganization[$key] !== $value;

                if ($hasChanged) {

                    $changes[$key] = [
                        'old' => $value,
                        'new' => $newOrganization[$key],
                    ];
                }
            }

            DB::commit();

            $organizationResource = (new OrganizationResource($organization))->additional(['changes' => $changes]);

            return ApiResponse::success($organizationResource, 'update');
        } catch (ModelNotFoundException) {

            return ApiResponse::notFound($id);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                $e,
                "Falha ao processar a atualização da organização."
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $organization = Organization::findOrFail($id);

            $name         = $organization->name;

            // Storage::delete($organization->logo);

            $organization->delete();

            return response()->json([
                'message' => "Organização '{$name}' desativada com sucesso.",
            ], 200);
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound();
        } catch (Exception $e) {
            return ApiResponse::error(
                $e,
                "Falha ao processar a desativação da organização."
            );
        }
    }
}
