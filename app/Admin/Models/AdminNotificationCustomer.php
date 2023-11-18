<?php
namespace App\Admin\Models;

use App\Events\NotifyToCustomer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class AdminNotificationCustomer extends Model
{
    protected $table      = SC_DB_PREFIX .'admin_notification_customer';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function notification()
    {
        return $this->belongsTo(AdminNotification::class, 'notification_id','id');
    }

    public static function sendCloudMessageToAndroid($deviceToken = "", $message = "", $title = "", $customerId = "")
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key='.env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        );

        $message = self::htmlToPlainText($message);

        $notification = array(
            'title' => stripslashes($title),
            'body' => stripslashes($message),
            'sound'=> 'default',
            'image' => 'http://'.request()->getHttpHost(). '/images/davicorp_logo.png'
        );

        $to = array(
            'to' => $deviceToken,
            'priority' => 'high',
            'notification' => $notification
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($to));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function sendNotifyToWeb($customerId = "", $message = "", $title = ""){
        try{
            if ($customerId) {
                $options = array(
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'encrypted' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => env('PUSHER_APP_USE_TLS'),
                );
                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    $options
                );

                $data['title'] = $title;
                $data['text'] = $message;
                $pusher->trigger('customer', 'customer_notify_' . $customerId, $data);
            }
        }catch (\Exception $e){
            Log::warning($e->getMessage());
        }
    }


    public static function sendNotifyToAdmin($message = "", $title = ""){
        try{
            $options = array(
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'encrypted' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => env('PUSHER_APP_USE_TLS'),
            );
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            $data['title'] = $title;
            $data['text'] = $message;
            $pusher->trigger('admin', 'admin_notify', $data);
        }catch (\Exception $e){
            Log::warning($e->getMessage());
        }
    }

    public static function htmlToPlainText($str){
        $str = str_replace('&nbsp;', ' ', $str);
        $str = html_entity_decode($str, ENT_QUOTES | ENT_COMPAT , 'UTF-8');
        $str = html_entity_decode($str, ENT_HTML5, 'UTF-8');
        $str = html_entity_decode($str);
        $str = htmlspecialchars_decode($str);
        $str = strip_tags($str);

        return $str;
    }
}
