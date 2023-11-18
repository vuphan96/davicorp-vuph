<?php

use App\Admin\Models\AdminDavicookOrder;
use App\Exceptions\ImportException;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopImportPriceboardDetail;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopSupplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SCart\Core\Admin\Admin;
use SCart\Core\Events\CustomerCreated;

define("HUMAN_TO_MACHINE", 0);
define("MACHINE_TO_HUMAN", 1);

function sc_event_customer_created(ShopCustomer $customer): void
{
    CustomerCreated::dispatch($customer);
}

function sc_customer_data_edit_mapping(array $dataRaw)
{

    if (isset($dataRaw['status'])) {
        $dataUpdate['status'] = $dataRaw['status'];
    }

    $validate = [];

    if (!empty($dataRaw['name'])) {
        $dataUpdate['name'] = $dataRaw['name'];
        $validate['name'] = config('validation.customer.name', 'required|string|max:100|unique:"' . ShopCustomer::class . '",name,' . $dataRaw['id'] . ',id');
    }
    if (!empty($dataRaw['email'])) {
        $dataUpdate['email'] = $dataRaw['email'];
        $validate['email'] = config('validation.customer.email', 'required|string|email|max:255') . '|unique:"' . ShopCustomer::class . '",email,' . $dataRaw['id'] . ',id';
    }

    if (!empty($dataRaw['phone'])) {
        $dataUpdate['phone'] = $dataRaw['phone'];
        $validate['phone'] = config('validation.customer.phone', 'regex:/^0[^0][0-9\-]{6,12}$/');
    }

    if (!empty($dataRaw['address'])) {
        $dataUpdate['address'] = $dataRaw['address'];
        $validate['address'] = config('validation.customer.address', 'nullable|string|max:255');
    }

    if (!empty($dataRaw['password'])) {
        $dataUpdate['password'] = bcrypt($dataRaw['password']);
        $validate['password'] = config('validation.customer.password', 'required|string|min:6');
    }
    //Dont update id
    unset($dataRaw['id']);

    $messages = [
        'name.required' => sc_language_render('validation.required', ['attribute' => 'customer.name']),
        'name.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('customer.name')]),
        'email.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.email')]),
        'email.unique' => sc_language_render('validation.unique', ['attribute' => sc_language_render('customer.email')]),
        'email.email' => sc_language_render('validation.email', ['attribute' => sc_language_render('customer.email')]),
        'email.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('customer.email')]),
        'password.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.password')]),
        'phone.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.phone')]),
        'phone.regex' => sc_language_render('customer.phone_regex'),
        'address.required' => sc_language_render('validation.required', ['attribute' => sc_language_render('customer.address')]),
        'address.string' => sc_language_render('validation.string', ['attribute' => sc_language_render('customer.address')]),
        'address.max' => sc_language_render('validation.max', ['attribute' => sc_language_render('customer.address')]),
        'password.confirmed' => sc_language_render('validation.confirmed', ['attribute' => sc_language_render('customer.password')]),
        'password.min' => sc_language_render('validation.min', ['attribute' => sc_language_render('customer.password')]),
    ];
    $dataMap = [
        'validate' => $validate,
        'messages' => $messages,
        'dataUpdate' => $dataUpdate
    ];

    return $dataMap;
}

function getDiffOrderUpdate($oldData, $newData)
{
    $diff = array_diff($newData, $oldData);
    if (empty($diff)) {
        return [];
    }
    $diffData = [];
    if ($diff && $diff != []) {
        foreach ($diff as $key => $value) {
            if ($key == 'updated_at') {
                continue;
            }
            $diffData[] = [
                'change' => $key,
                'old' => $oldData[$key],
                'new' => $newData[$key]
            ];
        }
    }
    return $diffData;
}


function diffToText($oldData, $newData)
{
    $diff = getDiffOrderUpdate($oldData, $newData);
    if (empty($diff)) {
        return [];
    }
    $content = 'Cập nhật ';
    if ($diff != []) {
        foreach ($diff as $key => $value) {
            $change = $value['change'];
            $old = $value['old'];
            $new = $value['new'];
            $content .= "<b>$change</b> ($old -> $new)&nbsp;";
        }
    }
    return $content;
}

/**
 * @param integer $role Admin role 1, User role 1. Default 1
 * */
function solveOrderHistory($oldData, $newData, $order_id, $role = 1)
{
    $diffText = diffToText($oldData, $newData);
    if (empty($diffText)) {
        return [];
    }
    $historyData = [
        'content' => $diffText,
        'add_date' => Carbon::now(),
        'order_id' => $order_id
    ];
    if ($role == 1) {
        $historyData['customer_id'] = 'UPDATE LATER';
    } elseif ($role == 0) {
        $historyData['admin_id'] = Admin::user()->id;
    }
    return $historyData;
}

// Convert money to text
function convert_number_to_words($number, $is_money = null)
{

    $hyphen = ' ';
    $conjunction = ' ';
    $separator = ' ';
    $negative = 'âm ';
    $decimal = ' phẩy ';
    $one = 'mốt';
    $ten = 'lẻ';
    $dictionary = array(
        0 => 'Không',
        1 => 'Một',
        2 => 'Hai',
        3 => 'Ba',
        4 => 'Bốn',
        5 => 'Năm',
        6 => 'Sáu',
        7 => 'Bảy',
        8 => 'Tám',
        9 => 'Chín',
        10 => 'Mười',
        11 => 'Mười một',
        12 => 'Mười hai',
        13 => 'Mười ba',
        14 => 'Mười bốn',
        15 => 'Mười lăm',
        16 => 'Mười sáu',
        17 => 'Mười bảy',
        18 => 'Mười tám',
        19 => 'Mười chín',
        20 => 'Hai mươi',
        30 => 'Ba mươi',
        40 => 'Bốn mươi',
        50 => 'Năm mươi',
        60 => 'Sáu mươi',
        70 => 'Bảy mươi',
        80 => 'Tám mươi',
        90 => 'Chín mươi',
        100 => 'trăm',
        1000 => 'ngàn',
        1000000 => 'triệu',
        1000000000 => 'tỷ',
        1000000000000 => 'nghìn tỷ',
        1000000000000000 => 'ngàn triệu triệu',
        1000000000000000000 => 'tỷ tỷ'
    );

    if (!is_numeric($number)) {
        return false;
    }

    // if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
    // 	// overflow
    // 	trigger_error(
    // 	'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
    // 	E_USER_WARNING
    // 	);
    // 	return false;
    // }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= Str::lower($hyphen . ($units == 1 ? $one : $dictionary[$units]));
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= Str::lower($conjunction . ($remainder < 10 ? $ten . $hyphen : null) . convert_number_to_words($remainder));
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number - ($numBaseUnits * $baseUnit);
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= Str::lower($remainder < 100 ? $conjunction : $separator);
                $string .= Str::lower(convert_number_to_words($remainder));
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction) && (int)$fraction > 0) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string)$fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    if ($is_money) {

        $string .= " đồng.";
    };
    return $string;
}

function formatStringDate($date = null, $format = 'Y-m-d')
{
    if (empty($date) || empty($format)) {
        return '';
    }

    if ($format == 'ngaythangnam') {
        $dateExplode = explode('-', $date);
        $dateString = 'ngày ' . $dateExplode['2'] . ' tháng ' . $dateExplode['1'] . ' năm ' . $dateExplode['0'] . '';
        return $dateString;
    }

    if ($format == 'Ngaythangnam') {
        $dateExplode = explode('-', $date);
        $dateString = 'Ngày ' . $dateExplode['2'] . ' tháng ' . $dateExplode['1'] . ' năm ' . $dateExplode['0'] . '';
        return $dateString;
    }

    $date = Carbon::createFromFormat($date, 'Y-m-d');
    return $date->format($format);
}


function cleanExcelFile(array $sheets)
{
    $output = [];
    foreach ($sheets as $sheet) {
        if (empty($sheet)) {
            continue;
        }
        foreach ($sheet as $row_no => $row) {
            $is_null_row = 1;
            foreach ($row as $col) {
                if ($col) {
                    $is_null_row = 0;
                }
            }
            if ($is_null_row) {
                unset($sheet[$row_no]);
            }
        }
        $output[] = $sheet;
    }
    return $output;
}

/**
 *
 *
 **/

function solveRow($row, array $masterCol, array $detailCol = null)
{
    $items = [];
    $details = [];
    foreach ($masterCol as $subCol) {
        $items[$subCol] = $row[$subCol] ?? '';
    }
    foreach ($detailCol as $subCol) {
        $details[$subCol] = $row[$subCol] ?? '';
    }
    $output = [
        'items' => empty(implode($items)) ? [] : $items,
        'details' => empty(implode($details)) ? [] : $details,
    ];

    if (empty($output['items']) && empty($output['details'])) {
        $output['type'] = 'null_row';
    } elseif (empty($output['items']) && !empty($output['details'])) {
        $output['type'] = 'detail_only';
    } elseif (!empty($output['items']) && !empty($output['details'])) {
        $output['type'] = 'both';
    } elseif (!empty($output['items']) && empty($output['details'])) {
        $output['type'] = 'customer_only';
    }

    return $output;
}

function getSupplierFromCode($input, $suppliers, $line = null)
{
    $index = array_search($input, $suppliers) ?? '';
    return $index ?? false;
}

function getProductFromSku($input, $products, $line = null)
{
    $index = array_search($input, $products) ?? '';
    return $index;
}

function getCustomerFromCode($input, $customers, $line = null, $sheet = null)
{
    $index = array_search($input, $customers) ?? 0;
    return $index;
}

function getProductPirceFromCode($input, $prices)
{
    $index = array_search($input, $prices);
    return $index;
}

function getZoneFromCode($input, $zone, $row_index = null)
{
    $index = array_search($input, $zone);
    return $index;
}

function lowerArray(array $array)
{
    foreach ($array as $key => $item) {
        $array[$key] = trim(Str::lower($item));
    }
    return $array;
}

function getDepartmentFromName($input, $department, $row_index = null)
{
    $index = array_search(trim(Str::lower($input)), lowerArray($department));
    if (!$index) {
        throw new ImportException("Trường \"Khách hàng thuộc\" chưa hợp lệ (Dòng $row_index )");
    }
    return $index;
}

function getCategoryFromCode($input, $category, $row_index = null)
{
    $index = array_search(trim(Str::lower($input)), lowerArray($category));
    return $index;
}

function getUnitFromName($input, $units, $row_index = null)
{
    $index = array_search(trim(Str::lower($input)), lowerArray($units));
    return $index;
}

function getTierFromName($input, $tier, $row_index = null)
{
    $index = array_search(trim(Str::lower($input)), lowerArray($tier));
    return !$index ? 0 : $index;
}


// Customer Import maping
function mapingProductDetail($details, $products, $suppliers, $customers, $startRow = null)
{
    $productOutput = [];
    $errorBags = [];
    foreach ($details as $index => $item) {
        $line = $item['row_index'];
        $suppliers_id = getSupplierFromCode($item['ma_nha_cung_cap'], $suppliers, $item['row_index']);
        $product_id = getProductFromSku($item['ma_san_pham'], $products, $item['row_index']);
        $customer_id = getCustomerFromCode($item['ma_khach_hang'], $customers, $item['row_index']);
        if (!$suppliers_id || !$product_id || !$customer_id) {
            if (!$suppliers_id) {
                $errorBags[$line][] = "Mã nhà cung cấp";
            }
            if (!$product_id) {
                $errorBags[$line][] = "Mã sản phẩm";
            }
            if (!$customer_id) {
                $errorBags[$line][] = "Mã khách hàng";
            }
            continue;
        }

        $productOutput[$item['row_index']] = [
            'supplier_id' => $suppliers_id,
            'product_id' => $product_id,
            'customer_id' => $customer_id
        ];
    }
    return ['data' => empty($errorBags) ? $productOutput : [], 'error' => $errorBags];
}

function checkRowRequiredDetails($columns, $columnData)
{
    $output = [];
    foreach (array_keys($columns) as $column) {
        if (is_null($columnData[$column]) || $columnData[$column] == "") {
            $output[] = $columns[$column];
        }
    }
    return empty($output) ? [] : implode(', ', $output);
}

function mapingCustomerImport($rawCustomer, $zone, $department, $tiers, $startRow = null)
{
    $outputCustomer = [];
    $errorBags = [];
    foreach ($rawCustomer as $key => $item) {
        $line = $item['row_index'];
        $zone_id = getZoneFromCode($item['ma_khu_vuc'], $zone, $item['row_index']);
        $tier_id = getTierFromName($item['hang_khach_hang'], $tiers, $item['row_index']);
        $department_id = getDepartmentFromName($item['khach_hang_thuoc'], $department, $item['row_index']);

        if (!$zone_id || !$tier_id || !$department_id) {
            if (!$zone_id) {
                $errorBags[$line][] = "Khu vực";
            }
            if (!$tier_id) {
                $errorBags[$line][] = "Hạng khách hàng";
            }
            if (!$department_id) {
                $errorBags[$line][] = "Khách hàng thuộc";
            }
            continue;
        }
        $outputCustomer[$item['row_index']] = [
            'customer_code' => $item['ma_khach_hang'],
            'schoolmaster_code' => $item['ten_dang_nhap_tk_hieu_truong'],
            'name' => $item['ten_khach_hang'],
            'email' => $item['email'],
            'phone' => $item['so_dien_thoai'],
            'address' => $item['dia_chi'],
            'tier_id' => $tier_id,
            'tax_code' => $item['ma_so_thue'],
            'order_num' => $item['thuoc_stt'],
            'route' => $item['thuoc_tuyen_hang'],
            'short_name' => $item['ten_hien_thi_tren_tem'],
            'zone_id' => $zone_id,
            'department_id' => $department_id,
            'kind' => $item['loai_khach_hang'] == 'CTY HĐ CT' ? 1 : ($item['loai_khach_hang'] == 'TH HĐ CTY' ? 2 : ($item['loai_khach_hang'] == 'HĐ CH' ? 0 : 3)),
            'teacher_code' => $item['ma_giao_vien'] ?? '',
            'student_code' => $item['ma_hoc_sinh'] ?? '',
            'password' => bcrypt(empty($item['mat_khau']) ? 'khachhang@davicorp' : $item['mat_khau']),
            'schoolmaster_password' => bcrypt(empty($item['mat_khau_tk_hieu_truong']) ? 'hieutruong@davicorp' : $item['mat_khau_tk_hieu_truong']),
            'status' => $item['trang_thai']
        ];
    }
    return ['data' => empty($errorBags) ? $outputCustomer : [], 'error' => $errorBags];
}

function checkRowRequired($needle, $row)
{
    if (empty($row)) {
        return 0;
    }
    foreach ($row as $key) {
        return empty($needle[$key]) ? 1 : 0;
    }
}

function checkTemplate($needle, $column)
{
    if (empty($needle) || empty($column)) {
        return 0;
    }
    $needle = array_filter($needle);
    $column = array_filter($column);
    return count(array_intersect($needle, $column)) == count($column) ? 1 : 0;
}

function getProductSku($input, $products)
{
    $index = in_array($input, $products);
    return $index;
}

function formatDateVn($date, bool $datetime = null)
{
    $format = $datetime ? 'd/m/Y H:i' : 'd/m/Y';
    return $date ? \Carbon\Carbon::make($date)->format($format) : '';
}

/**
 * Check xung đột thời gian đặt hàng
 * @param $start_date1 String bắt đầu range 1
 * @param $end_date1 String kết thúc range 1
 * @param $start_date2 String bắt đầu range 2
 * @param $end_date2 String kết thúc range 2
 * @return bool true: conflict, false: không conflict
 */
function checkConflictRange($start_date1, $end_date1, $start_date2, $end_date2): bool
{
    if (check_in_range($start_date1, $end_date1, $start_date2) || check_in_range($start_date1, $end_date1, $end_date2)) {
        return true;
    }
    if (check_in_range($start_date2, $end_date2, $start_date1) || check_in_range($start_date2, $end_date2, $end_date1)) {
        return true;
    }
    return false;
}

function check_in_range($start_date, $end_date, $date_from_user)
{
    // Convert to timestamp
    $start_ts = strtotime($start_date);
    $end_ts = strtotime($end_date);
    $user_ts = strtotime($date_from_user);

    // Check that user date is between start & end
    return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}

function isDupticatedItem($results)
{
    return (count($results) > 1) ? 1 : 0;
}

function my_array_reverse($a)
{
    $k = array_keys($a);
    $v = array_values($a);
    return array_combine(array_reverse($k), array_reverse($v));
}

function money_format($num, string $postfix = null): string
{
    return number_format($num, 0, '', ',') . " $postfix";
}

function array_sum_values(array $input, $key)
{
    $sum = 0;
    array_walk($input, function ($item, $index, $params) {
        if (!empty($item[$params[1]]))
            $params[0] += $item[$params[1]];
    }, array(&$sum, $key)
    );
    return $sum;
}

function paginate($items, $perPage = 20, $page = null, $options = ['path' => ''])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

}

/**
 * Check admin has permission
 */
if (!function_exists('has_perm')) {
    function has_perm($perm)
    {
        return Admin::user()->can($perm);
    }
}
function nowDateString(){
    return Carbon::today()->format("d/m/Y");
}
function convertVnDateObject(string $date){
    try {
        return Carbon::createFromFormat('d/m/Y', $date);
    } catch (Exception $e){
        return Carbon::createFromFormat('d/m/Y', "01/01/1970");
    }
}

function convertStandardDate($date) {
    $format = 'd/m/Y H:i:s';
    $now = Carbon::today()->format("d/m/Y");
    try {
        return Carbon::createFromFormat($format, $date);
    } catch (Exception $e){
        return Carbon::createFromFormat($format, $now);
    }
}

function convertDate($date, int $type){
    switch ($type){
        case HUMAN_TO_MACHINE:
            try {
                if(is_object($date)){
                    return $date ? $date->format("Y-m-d") : "1970-01-01";
                }
                if (is_string($date)){
                    return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                }
            } catch (Throwable $e) {
                return null;
        }

        case MACHINE_TO_HUMAN:
            try {
                if(is_object($date)){
                    return $date ? $date->format("d/m/Y") : "1/1/1970";
                }
                if (is_string($date)){
                    return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
                }
            } catch (Throwable $e) {
                return null;
            }

    }
}

// Xư lý làm tròn nguyên liệu suất
function roundTotalBom($total_bom, $unit_type)
{
    $int_total_bom = intval($total_bom);
    if ($unit_type == 1) {
        if ($total_bom == 0) {
            return 0;
        }
        if ($total_bom < 1) {
            return 1;
        } 
        if ($total_bom - $int_total_bom < 0.5) {
            return $int_total_bom;
        }    
        return $int_total_bom + 1;
    } 
    return round($total_bom, 2);
}

// Kiểm tra làm tròn của nguyên liệu suất của nguyên liệu có đơn vị nguyên
function checkRoundedIntTotalBom($total_bom, $unit_type)
{
    if (fmod($total_bom, 1) > 0 && $unit_type == 1) {
        return true;
    }
    return false;
} 
