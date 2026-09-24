<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\SampleData;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a demo account with sample CRM data.
     */
    public function run(SampleData $sampleData): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@fieldstone.test'],
            ['name' => 'Demo User', 'company_name' => 'Agent Solutions', 'password' => 'password'],
        );

        if (! $sampleData->existsFor($user)) {
            $sampleData->seedFor($user);
        }
    }
}
