<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\JobTitle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobTitle::factory()->count(10)->create();
        Industry::factory()->count(5)->create();
    }
}
