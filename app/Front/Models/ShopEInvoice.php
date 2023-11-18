<?php
namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShopEInvoice extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;


    public $table = SC_DB_PREFIX.'shop_einvoice';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public static $INVOICE_STATUS = [
        0 => 'Chưa làm',
        1 => 'Đã gửi',
        2 => 'Đang làm',
        3 => 'Thất bại',
        4 => 'Thành công',
    ];

    public static $INVOICE_CUSTOMER = [
        0 => 'HĐ CH',
        1 => 'CTY HĐ CT',
        2 => 'TH HĐ CTY',
    ];

    public static $INVOICE_MODE_RUN = [
        0 => 'Chạy ngay',
        1 => 'Chạy lập lịch',
    ];

    public function details() {
        return $this->hasMany(ShopEInvoiceDetail::class, 'einv_id', 'id');
    }

    public function histories() {
        return $this->hasMany(ShopEInvoiceHistory::class, 'einv_id', 'id');
    }

    public function order() {
        return $this->belongsTo(ShopOrder::class, 'order_id', 'id');
    }

    public function multipleEinvoices() {
        return $this->hasMany(ShopEInvoice::class, 'parent_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($einvoice) {
            $einvoice->details()->delete(); //delete order details
            $einvoice->histories()->delete(); //delete history
            $einvoice->multipleEinvoices()->delete(); //delete children
        });

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_einvoice');
            }
        });

    }

}