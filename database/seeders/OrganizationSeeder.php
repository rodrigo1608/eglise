<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::firstOrCreate(
            ['name' => 'HotSystems'],
            [
                'email' => 'admin@hotsystems.com',
                'logo'  => '/storage/images/Hot400px.png',
            ]
        );
    }
}
