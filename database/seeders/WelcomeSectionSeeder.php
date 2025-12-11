<?php

namespace Database\Seeders;

use App\Models\WelcomeSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WelcomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        WelcomeSection::firstOrCreate(
            ['component' => 'banner'],
            ['name' => 'Main Banner', 'order' => 1, 'is_visible' => true]
        );

        WelcomeSection::firstOrCreate(
            ['component' => 'events'],
            ['name' => 'Upcoming Events', 'order' => 2, 'is_visible' => true]
        );

        WelcomeSection::firstOrCreate(
            ['component' => 'news'],
            ['name' => 'Latest News', 'order' => 3, 'is_visible' => true]
        );
    }
}
