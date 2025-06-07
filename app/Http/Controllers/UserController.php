<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;

use App\Http\Requests\IndexRequest;

use App\Http\Requests\ShowRequest;

use App\Http\Requests\User\UserStoreUpdateRequest;

use App\Http\Resources\UserResource;

use App\Models\User;

use App\Traits\Fetches\FetchesIndexData;

use App\Traits\Requests\ExtractsRelationshipAttributes;

use App\Traits\Requests\ApplyQueryFilter;

use App\Traits\Responses\SendsJsonResponse;

use Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use FetchesIndexData;
    use SendsJsonResponse;
    use ExtractsRelationshipAttributes;
    use ApplyQueryFilter;

    public function index(IndexRequest $request): JsonResponse
    {
        $query = User::query();

        $this->applyFilter($request, $query, 'user_filters');

        $organizationAttributes = $this->extractRelationshipAttributes($request, 'organization_attributes');

        $query->with('organization'.$organizationAttributes);

        $users = $this->fetchesIndexData($request, $query);

        $usersResource = UserResource::collection($users);

        return ApiResponse::success($usersResource, 'index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreUpdateRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $valitedData = $request->validated();

            $profilePicture = $request->file('profile_picture');

            $valitedData['profile_picture'] = $profilePicture?->store('images/profile-pictures');

            $user = User::create($valitedData);

            $user->refresh();

            DB::commit();

            $userResource = new UserResource($user);

            return ApiResponse::success($userResource, 'store');
        } catch (Exception $e) {

            DB::rollBack();

            return ApiResponse::error(
                $e,
                "Falha ao processar o registro do usuário."
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowRequest $request,  $id): JsonResponse
    {
        try {

            $user = User::with('organization')->findOrFail($id);

            $userResource = new UserResource($user);

            return ApiResponse::success($userResource, 'show');
        } catch (ModelNotFoundException) {
            return ApiResponse::notFound();
        } catch (Exception $e) {
            return ApiResponse::error(
                $e,
                "Falha ao processar a busca do usuário."
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserStoreUpdateRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {

            $user = User::findOrFail($id);
            $oldUser = $user->toArray();

            $validatedData = $request->validated();

            if ($request->hasFile('profile_picture')) {
                Storage::delete($oldUser['profile_picture']);

                $validatedData['profile_picture'] = $request->file('profile_picture')->store('images/profile_pictures');
            }

            $user->update($validatedData);

            $user->refresh();

            $newUser = $user->toArray();

            $changes = [];

            foreach ($oldUser as $key => $value) {
                $isDifferent = $newUser[$key] !== $value;

                if ($isDifferent) {
                    $changes[$key] = [
                        'old' => $value,
                        'new' => $newUser[$key],
                    ];
                }
            }

            DB::commit();

            $userResource = (new UserResource($user))->additional(['changes' => $changes]);


             return ApiResponse::success($userResource, 'update');

        } catch (ModelNotFoundException $e) {

            return ApiResponse::notFound($id);
        }
        catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                $e,
               "Falha ao processar a atualização da usuário."
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $name = $user->getFullName();

            Storage::delete($user->profile_picture);

            $user->delete();

            return response()->json([

                'message' => "Usuário '{$name}' excluído com sucesso.",

            ], 200);
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->exceptionResponse($e, "Falha ao excluir usuário.");
        }
    }
}
