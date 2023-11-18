<?php

namespace Database\Seeders;

use App\Front\Models\ShopEInvoice;
use App\Front\Models\ShopEInvoiceDetail;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $order = ShopOrder::all();
        $product = ShopProduct::with('descriptions')->get();
        ShopEInvoice::truncate();
        ShopEInvoiceDetail::truncate();
        for ($i = 0; $i < 30; $i++) {
            $randomOrder = $order->random(1)[0];
            $invoice = new ShopEInvoice([
                'id' => rand(10000000, 999999999),
                'order_id' => $randomOrder->id_name,
                'customer_name' => $randomOrder->name,
                'customer_code' => null,
                'total_amount' => $randomOrder->total,
                'invoice_date' => now(),
                'delivery_date' => now(),
                'process_status' => rand(0, 4),
                'sync_system' => array_random(['FAST', 'einv']),
                'customer_kind' => rand(0,2),
                'mode_run' => rand(0, 1),
                'priority' => 0,
                'plan_start_date' => now(),
            ]);

            $invoice->save();
            $invoice_id = $invoice->id;
            for ($z = 1; $z <= 3; $z++) {
                $randomProduct = $product->random(1)[0];
                $detail = new ShopEInvoiceDetail(
                    [
                        'einv_id' => $invoice_id,
                        'product_name' => $randomProduct->descriptions->first()->name ?? 'Tên sản phẩm' ,
                        'product_code' =>  $randomProduct->sku,
                        'unit' => $randomProduct->unit_id,
                        'price' => rand(10000.00, 90000.00),
                        'qty' => rand(1, 20),
                    ]);
                $detail->save();
            }
        }
    }
}
