<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class RemoveDupticatedUserPriceBoardDetail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $preData = \App\Front\Models\ShopUserPriceboardDetail::all();
            $processedData = [];
            $processedDataText = [];
            foreach ($preData as $data) {
                if (in_array($data["customer_id"] . $data["user_priceboard_id"], $processedDataText)) {
                    continue;
                }
                $processedData[] = [
                    "customer_id" => $data["customer_id"],
                    "user_priceboard_id" => $data["user_priceboard_id"]
                ];
                $processedDataText[] = $data["customer_id"] . $data["user_priceboard_id"];
            }

            foreach ($preData as $deleteData){
                \App\Front\Models\ShopUserPriceboardDetail
                    ::where("customer_id", $deleteData->customer_id)
                    ->where("user_priceboard_id", $deleteData->user_priceboard_id)
                    ->delete();
            }
            \App\Front\Models\ShopUserPriceboardDetail::insert($processedData);
            $afterData = \App\Front\Models\ShopUserPriceboardDetail::all();

            DB::commit();
            echo "Total detail record: " . count($preData) . PHP_EOL;
            echo "Deleted record: " . count($preData) - count($processedDataText) . PHP_EOL;
            echo "Current record: " . count($afterData) . PHP_EOL;
            echo "Successfully, Committed" . PHP_EOL;
        } catch (Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
            echo "Error, Rollback";
            DB::rollBack();
        }

    }
}
