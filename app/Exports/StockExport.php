<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private $products;
    private $totals;

    public function __construct($products, $totals = [])
    {
        $this->products = $products;
        $this->totals = $totals;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];

        // Add data rows with serial numbers
        foreach ($this->products as $index => $product) {
            $stock = $product->stock < 0 ? 0 : $product->stock;
            $selling_price = $product->selling_price ?? 0;

            $rows[] = [
                $index + 1,                                                    // SL (Serial Number)
                $product->name,                                                // Name
                $product->avg_purchase_price ?? 0,                             // Avg P.P
                $product->last_purchase_price ?? 0,                            // L. P.P
                $product->selling_price ?? 0,                                  // Selling Price
                $product->stockDetails->sum('in_quantity') ?? 0,               // In Quantity
                $product->stockDetails->sum('out_quantity') ?? 0,              // Out Quantity
                $product->stock ?? 0,                                          // Stock
                remove_comma($stock) * remove_comma($product->avg_purchase_price) ?? 0,  // Stock P.P
                remove_comma($stock) * remove_comma($selling_price) ?? 0,      // Stock S.P
            ];
        }

        // Add totals row
        $rows[] = [
            '',                                          // SL
            '',                                          // Name
            '',                                          // Avg P.P
            '',                                          // L. P.P
            'Total',                                     // Selling Price column used for label
            $this->totals['totalInQty'] ?? 0,            // In Quantity
            $this->totals['totalOutQty'] ?? 0,           // Out Quantity
            $this->totals['totalStock'] ?? 0,            // Stock
            $this->totals['totalStockPP'] ?? 0,          // Stock P.P
            $this->totals['totalStockSP'] ?? 0,          // Stock S.P
        ];

        return $rows;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name ?? 'Company Name'],      // Title rows
            ['Stock List'],
            ['Time: ' . now()->format('d-m-Y h:i A')],
            [
                __('SL.'),
                __('Name'),
                __('Avg P.P'),
                __('L. P.P'),
                __('Selling Price'),
                __('In Quantity'),
                __('Out Quantity'),
                __('Stock'),
                __('Stock P.P'),
                __('Stock S.P'),
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'J';

        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:' . $lastCol . '1');  // Title
        $sheet->mergeCells('A2:' . $lastCol . '2');  // Subtitle
        $sheet->mergeCells('A3:' . $lastCol . '3');  // Time

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Center align title rows
        $sheet->getStyle('A1:' . $lastCol . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:' . $lastCol . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:' . $lastCol . '3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Apply bold to header row (row 4)
        $sheet->getStyle('A4:' . $lastCol . '4')->getFont()->setBold(true);
        $sheet->getStyle('A4:' . $lastCol . '4')->getAlignment()->setHorizontal('center');

        // Apply borders to data area (from row 4 to last row)
        $sheet->getStyle('A4:' . $lastCol . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Style the totals row (last row) - bold
        $sheet->getStyle('A' . $lastRow . ':' . $lastCol . $lastRow)->getFont()->setBold(true);
        $sheet->getStyle('E' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);   // SL
        $sheet->getColumnDimension('B')->setWidth(30);  // Name
        $sheet->getColumnDimension('C')->setWidth(12);  // Avg P.P
        $sheet->getColumnDimension('D')->setWidth(12);  // L. P.P
        $sheet->getColumnDimension('E')->setWidth(14);  // Selling Price
        $sheet->getColumnDimension('F')->setWidth(12);  // In Quantity
        $sheet->getColumnDimension('G')->setWidth(12);  // Out Quantity
        $sheet->getColumnDimension('H')->setWidth(10);  // Stock
        $sheet->getColumnDimension('I')->setWidth(12);  // Stock P.P
        $sheet->getColumnDimension('J')->setWidth(12);  // Stock S.P

        // Right align numeric columns (C to J) from row 5 to last row
        $sheet->getStyle('C5:' . $lastCol . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    public function title(): string
    {
        return 'Stock List';
    }
}
