<?php

namespace App\Admin\Models;

use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use SCart\Core\Front\Models\ShopCategory;
use SCart\Core\Front\Models\ShopProductCategory;
use SCart\Core\Front\Models\ShopProductDescription;
use SCart\Core\Front\Models\ShopProductStore;

class AdminProduct extends ShopProduct
{
    public function getName()
    {
        $productDescription = ShopProductDescription::where('product_id', $this->id)->first();
        return isset($productDescription) ? $productDescription->name : '';
    }

    /**
     * Get product detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getProductAdmin($id)
    {
        $data = self::where('id', $id)->first();
        return $data;
    }

    /**
     * Get list product in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getProductListAdmin(array $dataSearch, $storeId = null, $all = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $category_id = $dataSearch['category_id'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $tableProduct = (new ShopProduct)->getTable();
        $tableProductStore = (new ShopProductStore)->getTable();
        //Select field
        $dataSelect = $tableProduct . '.* ';
        $productList = (new ShopProduct)::with('category')
            ->selectRaw($dataSelect)
            ->leftJoin($tableProductStore, $tableProductStore . '.product_id', $tableProduct . '.id');

        if ($category_id) {
            $productList->where('category_id', $category_id);
        }

        if ($keyword) {
            $productList = $productList->where(function ($sql) use ($tableProduct, $keyword) {
                $sql
                    ->where($tableProduct . '.name', 'like', '%' . $keyword . '%')
                    ->orWhere($tableProduct . '.sku', 'like', '%' . $keyword . '%');
            });
        }

        $productList->groupBy($tableProduct . '.id');

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $productList = $productList->sort($field, $sort_field);
        } else {
            $productList = $productList->sort($tableProduct . '.created_at', 'desc');
        }

        if($all){
            return $productList->get();
        }

        return $productList->paginate(config('pagination.admin.big'));
    }

    /**
     * Get list product select in admin
     *
     * @param array $dataFilter [$dataFilter description]
     *
     * @return  []                  [return description]
     */
    public function getProductSelectAdmin(array $dataFilter = [], $storeId = null)
    {
        $keyword          = $dataFilter['keyword'] ?? '';
        $limit            = $dataFilter['limit'] ?? '';
        $kind             = $dataFilter['kind'] ?? [];
        $tableProduct     = (new ShopProduct)->getTable();
        $tableProductStore = (new ShopProductStore)->getTable();
        $colSelect = [
            'id',
            'sku',
            $tableProduct . '.name'
        ];
        $productList = (new ShopProduct)->select($colSelect)
            ->leftJoin($tableProductStore, $tableProductStore . '.product_id', $tableProduct . '.id');

        if ($storeId) {
            // Only get products of store if store <> root or store is specified
            $productList = $productList->where($tableProductStore . '.store_id', $storeId);
        }

        if (is_array($kind) && $kind) {
            $productList = $productList->whereIn('kind', $kind);
        }
        if ($keyword) {
            $productList = $productList->where(function ($sql) use ($tableProduct, $keyword) {
                $sql->where($tableProduct . '.name', 'like', '%' . $keyword . '%')
                    ->orWhere($tableProduct . '.sku', 'like', '%' . $keyword . '%');
            });
        }

        if ($limit) {
            $productList = $productList->limit($limit);
        }
        $productList->groupBy($tableProduct.'.id');
        $dataTmp = $productList->get()->keyBy('id');
        $data = [];
        foreach ($dataTmp as $key => $row) {
            $data[$key] = [
                'id' => $row['id'],
                'sku' => $row['sku'],
                'name' => addslashes($row['name']),
            ];
        }
        return $data;
    }


    /**
     * Create a new product
     *
     * @param array $dataInsert [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createProductAdmin(array $dataInsert)
    {
        return self::create($dataInsert);
    }


    /**
     * Insert data description
     *
     * @param array $dataInsert [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function insertDescriptionAdmin(array $dataInsert)
    {
        return ShopProductDescription::create($dataInsert);
    }

    /**
     * Validate product
     *
     * @param   [type]$type     [$type description]
     * @param null $fieldValue [$field description]
     * @param null $pId [$pId description]
     * @param null $storeId [$storeId description]
     * @param null            [ description]
     *
     * @return  [type]          [return description]
     */
    public function checkProductValidationAdmin($type = null, $fieldValue = null, $pId = null, $storeId = null)
    {
        $tableProductStore = (new ShopProductStore)->getTable();
        $storeId = $storeId ? sc_clean($storeId) : session('adminStoreId');
        $type = $type ? sc_clean($type) : 'sku';
        $fieldValue = sc_clean($fieldValue);
        $pId = sc_clean($pId);
        $check = $this
            ->leftJoin($tableProductStore, $tableProductStore . '.product_id', $this->getTable() . '.id')
            ->where($type, $fieldValue);
        $check = $check->where($tableProductStore . '.store_id', $storeId);
        if ($pId) {
            $check = $check->where($this->getTable() . '.id', '<>', $pId);
        }
        $check = $check->first();

        if ($check) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get total product of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalProduct()
    {
        return self::count();
    }

    /**
     * Get list category id from product id
     *
     * @param [array] $arrProductId
     * @return collection
     */
    public function getListCategoryIdFromProductId($arrProductId)
    {
        return (new ShopProductCategory)->whereIn('product_id', $arrProductId)->get()->groupBy('product_id');
    }

    public static function getUnit($unit_name, $units)
    {
        if ($units && $unit_name) {
            foreach ($units as $key => $unit) {
                if ($unit_name == $unit->name) {
                    return $unit->id;
                }
            }
        }
        return 0;
    }

    public static function getCategories($category_raw, $product_id, $categoryDescriptions)
    {
        $categories_array = explode(', ', $category_raw);
        $output = [];
        if (count($categories_array) > 0) {
            if ($categoryDescriptions) {
                foreach ($categories_array as $category) {
                    foreach ($categoryDescriptions as $key => $description) {
                        if ($description->lang == sc_get_locale() && $description->title == $category) {
                            $output[] = [
                                'product_id' => $product_id,
                                'category_id' => $description->category_id
                            ];
                        }
                    }
                }
                return $output;
            }
        }
        return 0;
    }
}
