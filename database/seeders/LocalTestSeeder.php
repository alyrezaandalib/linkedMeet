<?php

namespace Database\Seeders;

use App\Models\CompanyActivityType;
use App\Models\Industry;
use App\Models\JobTitle;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobTitles = JobTitle::factory()->count(10)->create();
        $industries = Industry::factory()->count(5)->create();
        $companyActivityTypes = CompanyActivityType::factory(10)->create();

        $centerLatitude = 37.7749;
        $centerLongitude = -122.4194;

        // Create the central user
        $centralUser = User::factory()->create([
            'name' => 'Central User',
            'email' => 'central@example.com',
        ]);

        $centralUser->userDetails()->update([
            'job_title_id' => $jobTitles->random()->id,
            'industry_id' => $industries->random()->id,
        ]);

        $centralUser->companyActivityTypes()->sync($companyActivityTypes->random(3)->pluck('id'));

        UserLocation::create([
            'user_id' => $centralUser->id,
            'latitude' => $centerLatitude,
            'longitude' => $centerLongitude,
        ]);

        // Create 5 nearby users within 500 meters
        for ($i = 0; $i < 5; $i++) {
            $nearbyUser = User::factory()->create();

            $nearbyUser->userDetails()->update([
                'job_title_id' => $jobTitles->random()->id,
                'industry_id' => $industries->random()->id,
            ]);

            $nearbyUser->companyActivityTypes()->sync($companyActivityTypes->random(3)->pluck('id'));

            // Random distance within 500 meters
            $distance = mt_rand(1, 500) / 1000; // Convert meters to kilometers
            $angle = mt_rand(0, 360); // Random direction in degrees

            // Calculate new latitude and longitude
            $latitude = $centerLatitude + ($distance / 6371) * cos(deg2rad($angle));
            $longitude = $centerLongitude + ($distance / (6371 * cos(deg2rad($centerLatitude)))) * sin(deg2rad($angle));

            UserLocation::create([
                'user_id' => $nearbyUser->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
        }

        // Create 15 distant users outside the 500-meter range
        for ($i = 0; $i < 15; $i++) {
            $distantUser = User::factory()->create();

            $distantUser->userDetails()->update([
                'job_title_id' => $jobTitles->random()->id,
                'industry_id' => $industries->random()->id,
            ]);

            $distantUser->companyActivityTypes()->sync($companyActivityTypes->random(3)->pluck('id'));

            // Random distance between 1 and 5 kilometers
            $distance = mt_rand(1001, 5000) / 1000; // Convert meters to kilometers
            $angle = mt_rand(0, 360); // Random direction in degrees

            // Calculate new latitude and longitude
            $latitude = $centerLatitude + ($distance / 6371) * cos(deg2rad($angle));
            $longitude = $centerLongitude + ($distance / (6371 * cos(deg2rad($centerLatitude)))) * sin(deg2rad($angle));

            UserLocation::create([
                'user_id' => $distantUser->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
        }
    }
}
