<?php

namespace Database\Seeders;

use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopDish;
use App\Front\Models\ShopProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DavicookOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShopDavicookOrder::truncate();
        ShopDavicookOrderDetail::truncate();

        $customer = ShopDavicookCustomer::all();
        $product = ShopProduct::all();
        $dish = ShopDish::all();
        for ($i = 0; $i < 20; $i++) {
            $randomCustomer = $customer->random(1)[0];
            $order = new ShopDavicookOrder([
                'id_name' => 'O-'.rand(10000000, 999999999),
                'customer_name' => $randomCustomer->name,
                'customer_id' => $randomCustomer->id,
                'phone' => $randomCustomer->phone,
                'email' => $randomCustomer->email,
                'address' => $randomCustomer->address,
                'status' => rand(0, 2),
                'purchase_priority_level' => rand(0, 1),
                'total' => rand(1000, 9000000),
                'edited' => rand(0,1),
                'delivery_date' => now(),
                'bill_date' => fake()->dateTimeThisMonth
            ]);

            $order->save();
            $order_id = $order->id;
            for ($j = 0; $j <= 2; $j++) {
                $dish_id = $dish[$j]->id;
                for ($z = 1; $z <= 3; $z++) {
                    $randomProduct = $product->random(1)[0];
                    $detail = new ShopDavicookOrderDetail(
                        [
                            'order_id' => $order_id,
                            'product_id' => $randomProduct->id,
                            'dish_id' => $dish_id,
                            'bom' => rand(1000, 20000),
                            'total_bom' => rand(1000, 20000),
                            'import_price' => rand(10000, 90000),
                            'qty' => rand(1, 20),
                        ]);
                    $detail->amount_of_product_in_order = $detail->import_price * $detail->qty;
                    $detail->save();
                }
            }

            $order->total = $order->details->sum('amount_of_product_in_order');
            $order->subtotal = $order->details->sum('amount_of_product_in_order');

            $order->save();
        }
    }
}
