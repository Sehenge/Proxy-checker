<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Source::factory([
            'url' => 'https://proxylist.geonode.com/api/proxy-list?limit=500',
            'type' => 'json',
            'is_working' => 1,
        ])->create();
    }
}
