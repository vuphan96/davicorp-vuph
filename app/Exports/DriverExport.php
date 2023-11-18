<?php

namespace App\Exports;

use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminDriverCustomer;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverExport implements FromView, WithColumnWidths, WithStyles
{
    // Excel export
    public function view(): View
    {
        $drivers = AdminDriver::with(['details', 'details.customerDavicorp', 'details.customerDavicook'])->get();
        $dataDriver =[];
        $dataDriverDetail =[];
        foreach ($drivers as $driver) {
            $details = [];
            foreach ($driver->details as $detail){
                $tempDetail = [
                    "type_order" => $detail["type_order"],
                    "type_customer" => $detail["type_customer"],
                ];
                if($detail->customerDavicorp){
                    $tempDetail["customer_code"] = $detail->customerDavicorp ? $detail->customerDavicorp->customer_code : '';
                    $tempDetail["name"] = $detail->customerDavicorp ? $detail->customerDavicorp->name : '';
                } else {
                    $tempDetail["customer_code"] = $detail->customerDavicook ? $detail->customerDavicook->customer_code : '';
                    $tempDetail["name"] = $detail->customerDavicook ? $detail->customerDavicook->name : '';
                }
                $dataDriverDetail[] = $tempDetail;
            }
            $dataDriver[] =[
                'id_name' => $driver['id_name'],
                'full_name' => $driver['full_name'],
                'phone' => $driver['phone'],
                'email' => $driver['email'],
                'address' => $driver['address'],
                'login_name' => $driver['login_name'],
                'password' => $driver['password'],
                'status' => $driver['status'],
                'details' => $dataDriverDetail,
            ];
        }
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.warehouse.driver.excel.export_template')->with(['data' => $dataDriver]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {

        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:G1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 20, //mã
            'C' => 25, // tên
            'D' => 25, // địa chỉ
            'E' => 25, // sđt
            'F' => 25, // email
            'G' => 15
        ];
    }
}
