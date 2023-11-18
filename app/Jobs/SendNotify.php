<?php

namespace App\Jobs;

use App\Front\Models\ShopDeviceToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Admin\Models\AdminNotificationCustomer;
use Illuminate\Support\Facades\Log;

class SendNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $idArray;
    protected $content;
    protected $title;
    public function __construct($idArray, $content, $title)
    {
        $this->idArray = $idArray;
        $this->content = $content;
        $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->idArray as $key => $item) {
            if (isset($item)) {
                $devices = (new ShopDeviceToken())->where('customer_id', $item)->get();
                foreach ($devices as $device) {
                    AdminNotificationCustomer::sendCloudMessageToAndroid($device->device_token, $this->content, $this->title);
                }
                AdminNotificationCustomer::sendNotifyToWeb($item, $this->content, $this->title);
            }
        }
    }
}
