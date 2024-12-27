<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\ServiceRequest;
use App\Models\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = User::factory()->count(100)->create();

        // Create 100 companies
        $companies = Company::factory()->count(50)->create([
            'user_id' => $users->random()->id,
        ]);

        // For each company, create 10 files
        $companies->each(function ($company) {
            File::factory()->count(10)->create([
                'company_id' => $company->id,
            ]);
        });


        // for each company, create 10 service requests
        $companies->each(function ($company) {
            ServiceRequest::factory()->count(10)->create([
                'company_id' => $company->id,
            ]);
        });
    }
}
