<?php
namespace App\Admin\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    public $table = SC_DB_PREFIX . 'admin_menu';
    protected $guarded = [];
    private static $getList = null;
    private static $getListDisplay = null;

    public static function getListAll()
    {
        if (self::$getList == null) {
            self::$getList = self::orderBy('sort', 'asc')
            ->get();
        }
        return self::$getList;
    }

    public static function getListAllDisplay()
    {
        if (self::$getListDisplay == null) {
            self::$getListDisplay = self::orderBy('sort', 'asc')
            ->where('hidden', 0)
            ->get();
        }
        return self::$getListDisplay;
    }

    /**
     * Get list menu can visible for user
     *
     * @return  [type]  [return description]
     */
    public static function getListVisible()
    {
        $list = self::getListAllDisplay();
        $listVisible = [];
        foreach ($list as  $menu) {
            if (!$menu->uri) {
                $listVisible[] = $menu;
            } else {
                $url = sc_url_render($menu->uri);
                if (\Admin::user()->checkUrlAllowAccess($url)) {
                    $listVisible[] = $menu;
                }
            }
        }
        $listVisible = collect($listVisible);
        $groupVisible = $listVisible->groupBy('parent_id');
        foreach ($listVisible as $key => $value) {
            if ((isset($groupVisible[$value->id]) && count($groupVisible[$value->id]) == 0)
                || (!isset($groupVisible[$value->id]) && !$value->uri)
            ) {
                unset($listVisible[$key]);
                continue;
            }
        }
        $listVisible = $listVisible->groupBy('parent_id');
        return $listVisible;
    }

    /**
     * Check url is child of other url
     *
     * @param   [type]  $urlParent  [$urlParent description]
     * @param   [type]  $urlChild   [$urlChild description]
     *
     * @return  [type]              [return description]
     */
    public static function checkUrlIsChild($urlParent, $urlChild)
    {
        $check = false;
        $urlParent = strtolower($urlParent);
        $urlChild = strtolower($urlChild);
        if ($urlChild) {
            if (
                strpos($urlParent, $urlChild . '/') !== false
                || strpos($urlParent, $urlChild . '?') !== false
                || $urlParent == $urlChild
            ) {
                $check = true;
            }
        }
        return $check;
    }

    public static function getActiveMenu($menus, $currentUrl) {
        if (empty($menus)) {
            return null;
        }

        $activeMenu = $menus[0][0]->id;
        foreach ($menus[0] as $levelTop) {
            if (!empty($menus[$levelTop->id])) {
                if (count($menus[$levelTop->id])) {
                    foreach ($menus[$levelTop->id] as $level0) {
                        if (!empty($menus[$level0->id])) {
                            foreach ($menus[$level0->id] as $level1) {
                                if($level1->uri && AdminMenu::checkUrlIsChild($currentUrl, sc_url_render($level1->uri))) {
                                    $activeMenu = $levelTop->id;
                                } else if (!empty($menus[$level1->id])) {
                                    foreach ($menus[$level1->id] as $level2) {
                                        if($level2->uri && AdminMenu::checkUrlIsChild($currentUrl, sc_url_render($level2->uri))) {
                                            $activeMenu = $levelTop->id;
                                        } else if (!empty($menus[$level2->id])) {
                                            foreach ($menus[$level2->id] as $level3) {
                                                if($level3->uri && AdminMenu::checkUrlIsChild($currentUrl, sc_url_render($level3->uri))) {
                                                    $activeMenu = $levelTop->id;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $activeMenu;
    }

    public function getTree($parent = 0, &$tree = null, $menus = null, &$st = '')
    {
        $menus = $menus ?? $this->getListAll()->groupBy('parent_id');
        $tree = $tree ?? [];
        $lisMenu = $menus[$parent] ?? [];
        foreach ($lisMenu as $menu) {
            $tree[$menu->id] = $st . ' ' . sc_language_render($menu->title);
            if (!empty($menus[$menu->id])) {
                $st .= '--';
                $this->getTree($menu->id, $tree, $menus, $st);
                $st = '';
            }
        }

        return $tree;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            //
        });
    }

    /*
    Re-sort menu
     */
    public function reSort(array $data)
    {
        try {
            DB::connection(SC_CONNECTION)->beginTransaction();
            foreach ($data as $key => $menu) {
                $this->where('id', $key)->update($menu);
            }
            DB::connection(SC_CONNECTION)->commit();
            $return = ['error' => 0, 'msg' => ""];
        } catch (\Throwable $e) {
            DB::connection(SC_CONNECTION)->rollBack();
            $return = ['error' => 1, 'msg' => $e->getMessage()];
        }
        return $return;
    }

    /**
     * [updateInfo description]
     */
    public static function updateInfo($arrFields, $id)
    {
        return self::where('id', $id)->update($arrFields);
    }

    /**
     * Create new menu
     * @return [type] [description]
     */
    public static function createMenu($dataInsert)
    {
        $dataUpdate = $dataInsert;
        return self::create($dataUpdate);
    }
}
