<?php

declare(strict_types=1);

use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rotas para Organization
Route::get('organization', [OrganizationController::class, 'index'])
    ->middleware('validate-extra-fields:App\Models\Organization')
    ->name('organization.index');

Route::post('organization', [OrganizationController::class, 'store'])->middleware('validate-requested-fields:App\Models\Organization')
    ->name('organization.store');

Route::get('organization/{organization}', [OrganizationController::class, 'show'])
    ->middleware('validate-extra-fields:App\Models\Organization,organization_filters')
    ->name('organization.show');

Route::delete('organization/{organization}', [OrganizationController::class, 'destroy'])
    ->name('organization.destroy');

Route::put('organization/{organization}', [OrganizationController::class, 'update'])
    ->name('organization.update');

Route::patch('organization/{organization}', [OrganizationController::class, 'update'])
    // ->middleware('resource-not-found:App\Models\Organization')
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
    // ->middleware('resource-not-found')
    ->name('user.update');

Route::patch('user/{user}', [UserController::class, 'update'])
    // ->middleware('resource-not-found:App\Models\User')
    ->name('user.patch');
