<?php

namespace App\Console\Commands;

use App\Admin\Models\AdminHoliday;
use App\Front\Models\ShopCustomer;
use Illuminate\Console\Command;
use App\Admin\Models\AdminNotifyMessage;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotificationCustomer;
use Illuminate\Support\Carbon;
use mysql_xdevapi\Exception;
use Illuminate\Support\Facades\DB;
use App\Front\Models\ShopDeviceToken;

class AddNotificationAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifyAuto:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = carbon::now();
        $time = $now->format('H:i');
        $checkBlockOrderWithHolidayToday = AdminHoliday::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('status', 1)->first();

        if ($checkBlockOrderWithHolidayToday) {
            return true;
        }

        $checkBlockOrderSatAndSun = AdminHoliday::where('type', 'everyday')->first();
        $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->addDay(5)->addHour(11);
        $endOfWeek = Carbon::now()->endOfWeek();

        if ($checkBlockOrderSatAndSun->status == 1) {
            if ($now >= $startOfWeek && $now <= $endOfWeek) {
                return true;
            }
        }

        $dataNotifyAutos = AdminNotifyMessage::where('time', $time)->get();
        $dataCustomer = ShopCustomer::all();
        DB::beginTransaction();
        try {
            foreach ($dataNotifyAutos as $dataNotifyAuto) {
                $title_description = $dataNotifyAuto->title_description;
                $content = $dataNotifyAuto->content;
                $result = new AdminNotification([
                    'title' =>  $title_description ?? '',
                    'content' => $content ?? '',
                    'display' => 0
                ]);
                if($result->save()) {
                    $notification_id = $result->id;
                    $dataOut = [];
                    foreach ($dataCustomer as $item) {
                        $customer_id = $item->id;
                        $dataOut[] = [
                            'notification_id' => $notification_id ?? '',
                            'customer_id' => $customer_id ?? '',
                        ];

                        $devices = ShopDeviceToken::where('customer_id', $item->id)->get();
                        foreach ($devices as $device) {
                            AdminNotificationCustomer::sendCloudMessageToAndroid($device->device_token, $content, $title_description);
                        }
                    }
                    AdminNotificationCustomer::insert($dataOut);
                }
            }

        } catch ( Exception $e){
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
    }
}
