<?php
#S-Cart/Core/Front/Models/ShopSupplier.php
namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;


class ShopSupplier extends Authenticatable
{
    use ModelTrait;
    use UuidTrait;
    use HasApiTokens, Notifiable;

    private static $getList = null;
    public $table = SC_DB_PREFIX . 'shop_supplier';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public static function getSupplierListAdmin(array $dataSearch = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';

        $supplierList = ShopSupplier::where('store_id', session('adminStoreId'));
        if ($keyword) {
            $supplierList
                ->where('name', 'like', '%' . $keyword . '%');
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $su = $supplierList->orderBy($field, $sort_field);
        } else {
            $supplierList = $supplierList->orderBy('id', 'desc');
        }
        $supplierList = $supplierList->paginate(config('pagination.admin.medium'));

        return $supplierList;
    }

    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::get()->keyBy('id');
        }
        return self::$getList;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($supplier) {
//                $supplier->customers()->delete();
//                $supplier->categories()->delete();
            }
        );
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_supplier');
            }
        });
    }

    public function customers()
    {
        return $this->hasMany(ShopSupplierCustomer::class, 'supplier_id', 'id');
    }

    public function categories()
    {
        return $this->hasMany(ShopSupplierCategory::class, 'supplier_id', 'id');
    }

    public function productSuppliers()
    {
        return $this->hasMany(ShopProductSupplier::class, 'supplier_id', 'id');
    }

    /*
    *Get thumb
    */

    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl($lang)
    {
        return sc_route('supplier.detail', ['alias' => $this->alias, 'lang' => $lang ?? app()->getLocale()]);
    }

    /*
    *Get image
    */

    public function getThumb()
    {
        return sc_image_get_path_thumb($this->image);
    }

    public function getName()
    {
        return $this->name ?? '';
    }

    public function getImage()
    {
        return sc_image_get_path($this->image);
    }

    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Get page detail
     *
     * @param   [string]  $key     [$key description]
     * @param   [string]  $type  [id, alias]
     * @param   [int]  $checkActive
     *
     */
    public function getDetail($key, $type = null, $checkActive = 1)
    {
        if (empty($key)) {
            return null;
        }
        if ($type === null) {
            $data = $this->where('id', $key);
        } else {
            $data = $this->where($type, $key);
        }
        if ($checkActive) {
            $data = $data->where('status', 1);
        }
        $data = $data->where('store_id', config('app.storeId'));

        return $data->first();
    }

    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start()
    {
        return new ShopSupplier;
    }

    /**
     * build Query
     */
    public function buildQuery()
    {
        $query = $this->where('status', 1)
            ->where('store_id', config('app.storeId'));

        /**
         * Note: sc_moreWhere will remove in the next version
         */
        if (count($this->sc_moreWhere)) {
            foreach ($this->sc_moreWhere as $key => $where) {
                if (count($where)) {
                    $query = $query->where($where[0], $where[1], $where[2]);
                }
            }
        }
        $query = $this->processMoreQuery($query);


        if ($this->sc_random) {
            $query = $query->inRandomOrder();
        } else {
            if (is_array($this->sc_sort) && count($this->sc_sort)) {
                foreach ($this->sc_sort as $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }
        return $query;
    }
    /**
     * Find the user instance for the given username.
     */
    public function findForPassport(string $username): ShopSupplier
    {
        return $this->where('name_login', $username)->first();
    }
    /**
     * Validate the password of the user for the Passport password grant.
     */
    public function validateForPassportPasswordGrant(string $password): bool
    {
        return Hash::check($password, $this->password);
    }
}
