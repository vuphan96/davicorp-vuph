<?php

namespace Database\Seeders;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderObject;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopZone;
use Faker;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{

    public function run()
    {
        // Make some order
        $customer = ShopCustomer::all();
        $product = ShopProduct::all();
        $object = ShopOrderObject::all();
        $supplier = ShopSupplier::all();
        for ($i = 0; $i < 40; $i++) {
            $randomCustomer = $customer->random(1)[0];
            $randomObject = $object->random(1)[0];

            $order = new ShopOrder([
                'name' => $randomCustomer->name,
                'customer_id' => $randomCustomer->id,
                'phone' => $randomCustomer->phone,
                'email' => $randomCustomer->email,
                'address' => $randomCustomer->address,
                'status' => rand(0, 1),
                'object_id' => $randomObject->id,
                'delivery_time' => fake()->dateTimeThisMonth
            ]);

            $order->save();

            $detailCount = rand(1, 10);
            for ($j = 0; $j <= $detailCount; $j++) {
                $randomProduct = $product->random(1)[0];
                $randomSupplier = $object->random(1)[0];

                $detail = new ShopOrderDetail(
                    [
                        'order_id' => $order->id,
                        'product_id' => $randomProduct->id,
                        'supplier_id' => $randomSupplier->id,
                        'price' => rand(1000, 20000),
                        'qty' => rand(20, 100),
                    ]);
                $detail->total_price = $detail->price * $detail->qty;
                $detail->save();
            }

            $order->total = $order->details->sum('total_price');
            $order->subtotal = $order->details->sum('total_price');

            $order->save();
        }

    }
}
