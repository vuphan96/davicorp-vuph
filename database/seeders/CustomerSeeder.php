<?php

namespace Database\Seeders;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{

    public function run()
    {
        //Prepare data
        $zone = ShopZone::all();
        $department = ShopDepartment::all();

        // Make some customer
        for ($i = 0; $i < 40; $i++) {
            (new ShopCustomer([
                'name' => fake('vi_VN')->company,
                'customer_code' => 'KH' . rand(1000, 2000),
                'email' => fake('vi_VN')->email,
                'phone' => fake('vi_VN')->phoneNumber,
                'address' => fake('vi_VN')->phoneNumber,
                'tax_code' => rand(11111, 99999),
                'zone_id' => $zone->random(1)[0]->id,
                'department_id' => $department->random(1)[0]->id,
            ]))->save();
        }
    }
}
