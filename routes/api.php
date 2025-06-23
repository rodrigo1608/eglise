<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::post('/login', function (Request $request) {

    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);

});

Route::middleware('auth:sanctum')->group(function () {

    // Rotas para Organization
    Route::get('organization', [OrganizationController::class, 'index'])
        ->middleware('validate-extra-fields:App\Models\Organization')
        ->name('organization.index');

    Route::post('organization', [OrganizationController::class, 'store'])
        ->middleware('validate-requested-fields:App\Models\Organization')
        ->name('organization.store');

    Route::get('organization/{organization}', [OrganizationController::class, 'show'])
        ->middleware('validate-extra-fields:App\Models\Organization,organization_filters')
        ->name('organization.show');

    Route::delete('organization/{organization}', [OrganizationController::class, 'destroy'])
        ->name('organization.destroy');

    Route::put('organization/{organization}', [OrganizationController::class, 'update'])
        ->name('organization.update');

    Route::patch('organization/{organization}', [OrganizationController::class, 'update'])
        ->name('organization.patch');

    // Rotas para User
    Route::get('user', [UserController::class, 'index'])
        ->middleware('validate-extra-fields:App\Models\User')
        ->name('user.index');

    Route::post('user', [UserController::class, 'store'])
        ->name('user.store');

    Route::get('user/{user}', [UserController::class, 'show'])
        ->middleware('validate-extra-fields:App\Models\User,user_filters')
        ->name('user.show');

    Route::delete('user/{user}', [UserController::class, 'destroy'])
        ->name('user.destroy');

    Route::put('user/{user}', [UserController::class, 'update'])
        ->name('user.update');

    Route::patch('user/{user}', [UserController::class, 'update'])
        ->name('user.patch');

});
