<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Models\Scopes\HasStatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */

    use HasFactory;
    use Notifiable;
    use HasStatusScope;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [

        'firstname',

        'lastname',

        'email',

        'profile_picture',

        'password',

        'organization_id',
    ];

    protected $casts = [

        'email_verified_at' => 'datetime',
        'profile_picture'   => 'string',
        'organization_id'   => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Define os campos disponíveis que podem ser usados via query string na API,
     */
    public static $availableQueryFields = [

        'user_attributes' => [
            'firstname',
            'lastname',
            'email',
            'profile_picture',
            'password',
            'organization_id',
        ],

        'includes' => [
            'organization',
            'phone',
            'evaluations'
        ],

        'organization_attributes' => [
            'id',
            'name',
            'email',
            'logo',
        ],

        'user_filters' => [
           'firstname',
            'lastname',
            'email',
            'profile_picture',
            'password',
            'organization_id',
        ]
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relacionamento: Empresa à qual o usuário pertence.
     */
    public function organization(): BelongsTo
    {
        return $this->BelongsTo(Organization::class);
    }

    /**
     * Relacionamento: Telefone do usuário.
     */
    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class);
    }

    // Mutator para garantir que o email seja salvo em minúsculas
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower((string) $value);
    }

    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
