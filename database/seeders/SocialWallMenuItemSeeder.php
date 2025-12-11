<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class SocialWallMenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // MenuItem::updateOrCreate(
        //     ['link' => '/admin/social-wall'], // Gunakan link sebagai kunci unik
        //     [
        //         'label' => 'Social Wall',
        //         'location' => 'header',
        //         'order' => 100, // Urutan tinggi agar muncul di akhir
        //         'parent_id' => null,
        //     ]
        // );
    }
}
