<?php

namespace Database\Seeders;

use App\Models\SocialNetwork;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialNetwork::updateOrCreate([
            'name' => 'LinkedIn',
        ], [
            'name' => 'LinkedIn',
        ]);
    }
}
