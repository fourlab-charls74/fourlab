<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** ExcelViewExport
 * # 참고화면 : stk20 (매장RT)
 * # Params
 * ** $view_url
 * ** $data
 * ** $style
 * ** $keys : list_key / one_sheet_count / cell_width / cell_height
 */

class ExcelViewExport implements WithMultipleSheets
{
    public function __construct($view_url, $data, $style, $keys)
    {
		$this->view_url = $view_url;
		$this->data = $data;
		$this->style = $style;
		$this->keys = $keys;
    }

	public function sheets(): array
	{
		$sheets = [];
		$sheets_count = floor(count($this->data[$this->keys['list_key'] ?? 'list'] ?? []) / ($this->keys['one_sheet_count'] ?? 25));

		foreach (range(0, $sheets_count) as $i) {
			$sheets[] = new ExcelOneSheetExport($this->view_url, $this->data, $this->style, $this->keys, $i);
		}

		return $sheets;
	}
}

class ExcelOneSheetExport implements FromView, WithStyles
{
	public function __construct($view_url, $data, $style, $keys, $sheet_num)
	{
		$this->view_url = $view_url;
		$this->data = $data;
		$this->data['sheet_num'] = $sheet_num * 1;
		$this->style = $style;
		$this->keys = $keys;
	}

	public function view(): View
	{
		return view($this->view_url, $this->data);
	}

	public function styles(Worksheet $sheet)
	{
		$sheet->getDefaultColumnDimension()->setWidth($this->keys['cell_width'] ?? 10);
		
		for ($i = 0; $i < 100; $i++) {
			$sheet->getRowDimension($i)->setRowHeight($this->keys['cell_height'] ?? 30);
		}
		
		return $this->style;
	}
}
