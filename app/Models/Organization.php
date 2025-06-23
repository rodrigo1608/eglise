<?php

declare(strict_types=1);

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'logo',
    ];

    protected $casts = [
        'name'      => 'string',
        'email'     => 'string',
        'logo'      => 'string',
    ];

    /**
     * Define os campos disponíveis que podem ser usados via query string na API,
     * separados por tipo: atributos diretos da model e relacionamentos.
     */
    public static $availableQueryFields = [

        'organization_attributes' => [
            'id',
            'name',
            'email',
            'logo',
        ],

        'includes' => [
            'users',
            'phone',
            'evaluations'
        ],

        'user_attributes' => [
            'firstname',
            'lastname',
            'email',
            'profile_picture',
            'password',
            'organization_id',
        ],

        'organization_filters' => [
            'id',
            'name',
            'email',
            'logo',
        ]
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected static function booted(): void
    {
        static::deleting(function ($org) {
            if (! $org->isForceDeleting()) {

                $org->users()->each->delete();
            }
        });

        static::restoring(function ($org) {

            $org->users()->each->restore();
        });
    }

    public function phone(): HasMany
    {
        return $this->hasMany(Phone::class);    }


}
