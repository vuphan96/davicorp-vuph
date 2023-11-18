<?php

namespace App\Front\Models;

use Auth;

use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;
use SCart\Core\Front\Models\ShopCustomFieldDetail;

class ShopCustomer extends Authenticatable
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    use Notifiable, HasApiTokens, AuthenticableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = SC_DB_PREFIX . 'shop_customer';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;
    private static $profile = null;
    protected static $listCustomer = null;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'schoolmaster_password', 'remember_token',
    ];

    public function getSchoolmasterPassword(){
        return $this->schoolmaster_password;
    }

    public function orders()
    {
        return $this->hasMany(ShopOrder::class, 'customer_id', 'id');
    }

    public function zone()
    {
        return $this->belongsTo(ShopZone::class,'zone_id', 'id');
    }

    public function tier()
    {
        return $this->belongsTo(ShopRewardTier::class,'tier_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(ShopDepartment::class,'department_id', 'id');
    }

    public function productSuppliers(){
        return $this->hasMany(ShopProductSupplier::class, 'customer_id', 'id');
    }

    public function priceBoardDetails(){
        return $this->hasMany(ShopUserPriceboardDetail::class, 'customer_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function reward(){
        return $this->hasMany(ShopPoint::class, 'customer_id', 'id');
    }

    public function rewards(){
        return $this->hasMany(ShopPoint::class, 'customer_id', 'id');
    }

    public function rating(){
        return $this->hasMany(ShopRating::class, 'customer_id', 'id');
    }

    public function deletePriceboarDetails(){
        ShopUserPriceboardDetail::where('customer_id', $this->id)->delete();
    }


    /**
     * Send email reset password
     * @param  [type] $token [description]
     * @return [type]        [description]
     */

    public function  getReward($month = null, $year = null){
        return $this->rewards()->where(['month' => $month ?? Carbon::now()->format('m'), 'year' => $year ?? Carbon::now()->format('Y')])->first() ?? [];
    }

    public function sendPasswordResetNotification($token)
    {
        $emailReset = $this->getEmailForPasswordReset();
        return sc_customer_sendmail_reset_notification($token, $emailReset);
    }


    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_customer');
            }
        });

        static::deleted(function ($model) {
            $model->productSuppliers()->delete();
            foreach ($model->rewards as $reward){
                $reward->history()->delete();
            }
            $model->rewards()->delete();
            $model->deletePriceboarDetails();
        });
    }


    /**
     * Update info customer
     * @param  [array] $dataUpdate
     * @param  [int] $id
     */
    public static function updateInfo($dataUpdate, $id)
    {
        $dataClean = sc_clean($dataUpdate);

        $fields = $dataClean['fields'] ?? [];
        unset($dataClean['fields']);

        $user = self::find($id);
        $user->update($dataClean);

        return $user;
    }

    /**
     * Create new customer
     * @return [type] [description]
     */
    public static function createCustomer($dataInsert)
    {
        $dataClean = sc_clean($dataInsert);

        $fields = $dataClean['fields'] ?? [];
        unset($dataClean['fields']);

        $dataAddress = sc_customer_address_mapping($dataClean)['dataAddress'];


        $user = self::create($dataClean);
        $user->save();

        // Process event customer created
//        sc_event_customer_created($user);

        return $user;
    }

    /**
     * Get address default of user
     *
     * @return  [collect]
     */

    public function profile()
    {
        if (self::$profile === null) {
            self::$profile = Auth::user();
        }
        return self::$profile;
    }

    /**
     * Check customer has Check if the user is verified
     *
     * @return boolean
     */
    public function isVerified()
    {
        return !is_null($this->email_verified_at) || $this->provider_id;
    }

    /**
     * Check customer need verify email
     *
     * @return boolean
     */
    public function hasVerifiedEmail()
    {
        return !$this->isVerified() && sc_config('customer_verify');
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerify()
    {
        if ($this->hasVerifiedEmail()) {
            return sc_customer_sendmail_verify($this->email, $this->id);
        }
        return false;
    }

    public function devices()
    {
        return $this->hasMany(ShopDeviceToken::class, 'customer_id', 'id');
    }

    //Getter
    public function getCustomerName()
    {
        return $this->name ?? '';
    }

    public function getCustomerCode()
    {
        return $this->customer_code ?? '';
    }

    public function getCustomerId()
    {
        return $this->id ?? '';
    }
    public function getTierName(){
        return $this->tier->name ?? '';
    }

    public function getTierId(){
        return $this->tier_id ?? '';
    }

    public function getTierRate(){
        return $this->tier->rate ?? 0;
    }
    public static function getIdAll()
    {
        if (!self::$listCustomer) {
            self::$listCustomer = self::pluck('name', 'id')->all();
        }
        return self::$listCustomer;
    }
}
