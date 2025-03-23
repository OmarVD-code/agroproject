<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlySalesExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $statement = "CALL generate_sales_monthly_report(?, ?)";
        return collect(DB::select($statement, [$this->year, $this->month]));
    }

    public function headings(): array
    {
        return ["Producto", "Cantidad"];
    }
}
