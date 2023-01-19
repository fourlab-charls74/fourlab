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
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ExcelExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithCustomValueBinder
{
    protected $query;

    public function __construct($query, $headers = [], $sizes = [])
    {
        $this->query = $query;
        $this->headers = $headers;
        $this->sizes = $sizes;
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

    public function registerEvents(): array
    {
        $alphabets = range('A', 'Z');
        foreach (range('A', 'Z') as $val) {
            if (count($alphabets) > count($this->headers)) break;
            $alphabets = array_merge($alphabets, array_map(function($r) use ($val) { return $val . $r; }, range('A', 'Z')));
        }

        return [
            AfterSheet::class => function (AfterSheet $event) use ($alphabets) {
                $sheet = $event->getSheet()->getDelegate();
                $last = $alphabets[count($this->headers) - 1];
                $lastnum = $this->collection()->count() + 1;
                
                foreach (array_slice($alphabets, 0, count($this->headers)) as $key => $columnId) {
                    $size = $this->sizes[array_keys($this->headers)[$key] ?? ''] ?? 10;
                    $sheet->getColumnDimension($columnId)->setAutoSize(false);
                    $sheet->getColumnDimension($columnId)->setWidth($size);
                }

                $event->sheet->getDelegate()->getStyle('A1:' . $last . '1')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('D6E2FF');

                $event->sheet->getDelegate()->getStyle("A1:$last$lastnum")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
            }
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
