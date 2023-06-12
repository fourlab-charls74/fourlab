<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** ExcelViewExport
 * # 참고화면 : stk20 (매장RT)
 * # Params
 * ** $view_url
 * ** $data
 * ** $style
 * ** images
 * ** $keys : list_key / one_sheet_count / cell_width / cell_height
 */

class ExcelViewExport implements WithMultipleSheets
{
    public function __construct($view_url, $data, $style, $images, $keys)
    {
		$this->view_url = $view_url;
		$this->data = $data;
		$this->style = $style;
		$this->images = $images;
		$this->keys = $keys;
    }

	public function sheets(): array
	{
		$sheets = [];
		$sheets_count = floor(count($this->data[$this->keys['list_key'] ?? 'list'] ?? []) / ($this->keys['one_sheet_count'] ?? 25)) - 1;
		if ($sheets_count < 0) $sheets_count = 0;

		foreach (range(0, $sheets_count) as $i) {
			$sheets[] = new ExcelOneSheetExport($this->view_url, $this->data, $this->style, $this->images, $this->keys, $i);
		}

		return $sheets;
	}
}

class ExcelOneSheetExport implements FromView, WithStyles, WithDrawings
{
	public function __construct($view_url, $data, $style, $images, $keys, $sheet_num)
	{
		$this->view_url = $view_url;
		$this->data = $data;
		$this->data['sheet_num'] = $sheet_num * 1;
		$this->style = $style;
		$this->images = $images;
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
		
		$sheet->getPageMargins()->setTop(0.5);
		$sheet->getPageMargins()->setBottom(0.5);
		$sheet->getPageMargins()->setLeft(0);
		$sheet->getPageMargins()->setRight(0);
		$sheet->getPageSetup()->setHorizontalCentered(true);
		$sheet->getPageSetup()->setVerticalCentered(true);
		$sheet->getPageSetup()->setFitToPage(true);
		$sheet->getPageSetup()->setPaperSize(9);
		
		return $this->style;
	}

	public function drawings()
	{
		$list = [];
		
		if ($this->images !== null) {
			foreach ($this->images as $img) {
				$drawing = new Drawing();
				if (isset($img['title'])) $drawing->setName($img['title']);
				if (isset($img['desc'])) $drawing->setDescription($img['desc']);
				if (isset($img['public_path'])) $drawing->setPath(public_path($img['public_path']));
				if (isset($img['cell'])) $drawing->setCoordinates($img['cell']);		
				if (isset($img['height'])) $drawing->setHeight($img['height']);
				$list[] = $drawing;
			}
		}
	
		return $list;
	}
}
