<?php

namespace Database\Seeders;

use App\Front\Models\ShopZone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run()
    {
        //Make some fake zone
        for ($i = 0; $i < 10; $i++) {
            $fake = [
                'name' => fake('vi_VN')->city,
            ];
            $fake['zone_code'] = sc_word_format_url($fake['name']);
            (new ShopZone($fake))->save();
        }
    }
}
