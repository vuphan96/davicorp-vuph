<?php

namespace App\Exports\DavicookMenuCard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportMenuCard implements WithMultipleSheets
{
    use Exportable;
    protected $dataSearch;
    protected $dataMenuForStudent;
    protected $dataMenuForTeacher;

    /**
     * AdminExportMenuCard constructor.
     * @param $dataSearch
     * @param $dataMenuForStudent
     * @param $dataMenuForTeacher
     */
    public function __construct($dataSearch, $dataMenuForStudent, $dataMenuForTeacher)
    {
        $this->dataSearch = $dataSearch;
        $this->dataMenuForStudent = $dataMenuForStudent;
        $this->dataMenuForTeacher = $dataMenuForTeacher;
    }

    // Excel export
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new AdminExportMenuCardSheetForStudent($this->dataMenuForStudent);
        $sheets[] = new AdminExportMenuCardSheetForTeacher($this->dataMenuForTeacher);

        return $sheets;
    }

}
