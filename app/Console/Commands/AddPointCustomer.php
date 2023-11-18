<?php

namespace App\Console\Commands;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopPointHistory;
use App\Front\Models\ShopRewardPrinciple;
use App\Front\Models\ShopRewardTier;
use Carbon\Carbon;
use Google\Service\Firestore\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Log;

class AddPointCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'point:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug("OK TEST");
        $arrDate = [
//            0 => 'CN',
            1 => '2',
            2 => '3',
            3 => '4',
            4 => '5',
            5 => '6',
//            6 => '7',
        ];
        $tomorrow = Carbon::tomorrow();
        $customers = ShopCustomer::orderBy('created_at','desc')->get();
        foreach ($customers as $customer) {
            # Lấy tất cả đơn hàng của khách hàng theo customer_id và ngày giao hàng là ngày mai.
            $orderWithCustomerAndDate = ShopOrder::where('customer_id', $customer->id)
                                        ->whereIn('status', [1, 2])->whereDate('delivery_time', $tomorrow->toDateString())
                                        ->orderBy('updated_at', 'DESC')->get();
            $orderId = 1;
            $shopPoint = ShopPoint::where('customer_id', $customer->id)->where('month', $tomorrow->month)->where('year', $tomorrow->year)->orderBy('month', 'DESC')->first();
            $point = 0;
            # Check chưa có điểm thưởng tháng hiện tại -> Tạo mới điểm thưởng.
            if (!$shopPoint) {
                $idPoint = ShopPoint::insertGetId(
                    [
                        'customer_id' => $customer->id,
                        'point' => 0,
                        'month' => $tomorrow->month,
                        'year' => $tomorrow->year,
                    ]
                );
                $shopPoint = ShopPoint::find($idPoint);
            }


            foreach ($orderWithCustomerAndDate as $key => $item) {
                # Những đơn đã tính điểm ko cần tính lại.
                $flagHistory = ShopPointHistory::where('order_id', $item->id)->first();
                if ($flagHistory) {
                    continue;
                }
                if ($orderId == 1) {
                    # Check đơn hàng đầu tiên.
                    $checkDayOnWeek = \Illuminate\Support\Carbon::make($item->updated_at)->dayOfWeek;
                    if (array_key_exists($checkDayOnWeek, $arrDate)) {
                        $point = $this->getPointWithDay($item, $shopPoint->id);
                    } else {
                        $point = $this->getPointWithWeekend($item, $shopPoint->id);
                    }

                    $orderId = $item->id;
                } else {
                    $dataInsertHistory = [
                        'point_id' => $shopPoint->id,
                        'order_id' => $item->id,
                        'action' => 0,
                        'original_point' => 0,
                        'blance_point' => 0,
                        'change_point' => 0,
                        'actual_point' => 0,
                        'comment' => NULL,
                    ];
                    ShopPointHistory::create($dataInsertHistory);
                }
            }
            $shopPoint->point = $shopPoint->point + $point;
            $shopPoint->save();
        }
        return Command::SUCCESS;
    }

    public function getPointWithDay($item, $point_id)
    {
        # Check ngày Update > ngày giao hàng 2 ngày -> auto +4 point;
        # Ngày update nhỏ hơn ngày hiện tại.
        $point = 0;
        $now = now()->toDateString();
        $dateTimeUpdateOrder = Carbon::parse($item->updated_at);
        if ($dateTimeUpdateOrder->toDateString() < $now) {
            $shopRewardPrinciple = ShopRewardPrinciple::where('is_weekend', 0)->where('is_admin', $item->is_order_admin)->first();
            $point = !$shopRewardPrinciple ? 0 : $shopRewardPrinciple->point;
            $comment = "Ngày thường, admin {$item->is_order_admin}, Lấy điểm đầu tiên";
            $this->insertHistoryPoint($point_id, $item->id, $point, $comment);
        } else {
            $timeUpdateOrder = $dateTimeUpdateOrder->format('H:i:s');
            $shopRewardPrinciple = ShopRewardPrinciple::where('is_weekend', 0)->where('is_admin', $item->is_order_admin)->whereRaw('? BETWEEN `from` AND `to`', [$timeUpdateOrder])->first();
            if ($shopRewardPrinciple) {
                $point = $shopRewardPrinciple->point;
            }
            $comment = "Ngày thường, admin {$item->is_order_admin}, Lấy điểm theo query";
            $this->insertHistoryPoint($point_id, $item->id, $point, $comment);
        }

        return $point;
    }

    public function getPointWithWeekend($item, $point_id)
    {
        # Check ngày đặt là T7 CN -> Giao hàng lớn hơn thứ 2 tuần sau -> auto + 4;
        # Check ngày đặt là T7 CN -> Giao hàng là CN và T2 -> Xét thời gian;
        $arrDate = [
            0 => 'CN',
            1 => '2',
        ];
        $point = 0;
        $updateDate = Carbon::parse($item->updated_at);
        $deliveryDate = Carbon::parse($item->delivery_time);
        $dayDiff = $deliveryDate->diffInDays($updateDate);
        if (array_key_exists($deliveryDate->dayOfWeek, $arrDate)) {
            if ($dayDiff >= 2) {
                $shopRewardPrinciple = ShopRewardPrinciple::where('is_weekend', 1)->where('is_admin', $item->is_order_admin)->first();
                $point = !$shopRewardPrinciple ? 0 : $shopRewardPrinciple->point;
                $comment = "Ngày cuối tuần, admin {$item->is_order_admin}, Lấy điểm đầu tiên, Trường hợp cho cn t2 tuần tới";
                $this->insertHistoryPoint($point_id, $item->id, $point, $comment);
            } else {
                $timeUpdateOrder = $updateDate->format('H:i:s');
                $shopRewardPrinciple = ShopRewardPrinciple::where('is_weekend', 1)->where('is_admin', $item->is_order_admin)->whereRaw('? BETWEEN `from` AND `to`', [$timeUpdateOrder])->first();
                if ($shopRewardPrinciple) {
                    $point = $shopRewardPrinciple->point;
                }
                $comment = "Ngày cuối tuần, admin {$item->is_order_admin}, Lấy điểm theo query";
                $this->insertHistoryPoint($point_id, $item->id, $point, $comment);
            }
        } else {
            $shopRewardPrinciple = ShopRewardPrinciple::where('is_weekend', 1)->where('is_admin', $item->is_order_admin)->first();
            $point = !$shopRewardPrinciple ? 0 : $shopRewardPrinciple->point;
            $comment = "Ngày cuối tuần, admin {$item->is_order_admin}, Lấy điểm đầu tiên";
            $this->insertHistoryPoint($point_id, $item->id, $point, $comment);
        }

        return $point;
    }

    /**
     * @param $point_id
     * @param $order_id
     * @param $point
     * @param null $comment
     */
    public function insertHistoryPoint($point_id, $order_id, $point, $comment = null)
    {
        $dataInsertHistory = [
            'point_id' => $point_id,
            'order_id' => $order_id,
            'action' => 1,
            'original_point' => 0,
            'blance_point' => 0,
            'change_point' => $point,
            'actual_point' => $point,
            'comment' => $comment,
        ];
        ShopPointHistory::create($dataInsertHistory);
    }
}
