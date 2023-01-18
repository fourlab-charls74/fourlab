<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $query;

    public function __construct($query, $headers = [])
    {
        $this->query = $query;
        $this->headers = $headers;
    }

    public function headings(): array
    {
        return array_values($this->headers);
    }

    public function map($row): array
    {
        return array_column(array_map(
                    function($h) use ($row) { 
                        return ['key' => $h, 'value' => $row->$h ?? '']; 
                    }, array_keys($this->headers)
                ), 'value', 'key');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(DB::select($this->query));
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8'
        ];
    }

    public function registerEvents(): array{
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                
                $last = chr(64 + count($this->headers));
                foreach (range('A', $last) as $columnId) {
                    $sheet->getColumnDimension($columnId)->setAutoSize(true);
                }
            }
        ];
    }
}
