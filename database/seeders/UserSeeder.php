<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('name', 'HotSystems')->first();

        $users = [
            [
                'email'     => 'rodrigo@email.com',
                'firstname' => 'Rodrigo',
                'lastname'  => 'Lima',
            ],
            [
                'email'     => 'hadailton@gmail.com',
                'firstname' => 'Hadailton',
                'lastname'  => 'Carvalho',
            ],
            [
                'email'     => 'yan@email.com',
                'firstname' => 'Yan',
                'lastname'  => 'Facundes',
            ],
            [
                'email'     => 'alexandre@email.com',
                'firstname' => 'Alexandre',
                'lastname'  => 'Moraes',
            ],
            [
                'email'     => 'maiza@email.com',
                'firstname' => 'Maiza',
                'lastname'  => 'Godoy',
            ],
            [
                'email'     => 'arthur@email.com',
                'firstname' => 'Arthur',
                'lastname'  => 'Lamas',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'firstname'         => $data['firstname'],
                    'lastname'          => $data['lastname'],
                    'profile_picture'   => '/storage/images/fa-user.jpg',
                    'email_verified_at' => now(),
                    'password'          => Hash::make('123', ['rounds' => 10]),
                    'remember_token'    => Str::random(10),
                    'organization_id' => $organization->id
                ]

            );

            // Associa o usuário à organização, se ainda não estiver associado
            if ($organization && ! $user->organization_id === null) {

                $user->organization_id = $organization->id;
            }
        }
    }
}
