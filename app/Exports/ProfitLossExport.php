<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            // Income Section
            [__('INCOME'), ''],
            [__('Total Sales'), currency($this->data['totalSales'])],
            [__('Sales Returns') . ' (' . __('Deduction') . ')', '- ' . currency($this->data['salesReturns'])],
            [__('Net Sales'), currency($this->data['netSales'])],
            [__('Purchase Returns') . ' (' . __('Refund from Supplier') . ')', currency($this->data['purchaseReturns'])],
            [__('Outside Sales Profit') . ' (' . __('Selling') . ': ' . currency($this->data['outsideSellingPrice']) . ' - ' . __('Purchase') . ': ' . currency($this->data['outsidePurchaseCost']) . ')', currency($this->data['outsideProfit'])],
            [__('Total Income'), currency($this->data['totalIncome'])],
            ['', ''],
            // Expense Section
            [__('EXPENSES'), ''],
            [__('Cost of Goods Sold (COGS - Own Inventory)'), currency($this->data['cogs'])],
            [__('Gross Profit') . ' (' . __('Net Sales - COGS') . ')', currency($this->data['grossProfit'])],
            [__('Operating Expenses'), currency($this->data['expenses'])],
            [__('Employee Salaries'), currency($this->data['salaries'])],
            [__('Total Expenses'), currency($this->data['totalExpenses'])],
            ['', ''],
            // Profit/Loss
            [__('NET PROFIT / LOSS'), currency($this->data['profitLoss'])],
        ];
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],
            [__('Profit/Loss Report')],
            [__('Period') . ': ' . $this->data['fromDate'] . ' - ' . $this->data['toDate']],
            ['Time: ' . now()->format('d-m-Y H:i:s')],
            [__('Description'), __('Amount')],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Merge cells for title rows
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');

        // Apply styles to title rows
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(10);

        // Apply borders to data rows
        $sheet->getStyle('A5:B' . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Center align title rows
        $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Right align amount column
        $sheet->getStyle('B6:B' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Style section headers (Income - row 6)
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A6:B6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA');

        // Style Total Income row (row 12)
        $sheet->getStyle('A12')->getFont()->setBold(true);
        $sheet->getStyle('A12:B12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C3E6CB');

        // Style Expenses header (row 14)
        $sheet->getStyle('A14')->getFont()->setBold(true);
        $sheet->getStyle('A14:B14')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');

        // Style Gross Profit row (row 16)
        $sheet->getStyle('A16')->getFont()->setBold(true);
        $sheet->getStyle('A16:B16')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');

        // Style Total Expenses row (row 19)
        $sheet->getStyle('A19')->getFont()->setBold(true);
        $sheet->getStyle('A19:B19')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F5C6CB');

        // Style Net Profit/Loss row (row 21)
        $sheet->getStyle('A21')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B21')->getFont()->setBold(true)->setSize(12);

        if ($this->data['profitLoss'] >= 0) {
            $sheet->getStyle('A21:B21')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('28A745');
            $sheet->getStyle('A21:B21')->getFont()->getColor()->setRGB('FFFFFF');
        } else {
            $sheet->getStyle('A21:B21')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DC3545');
            $sheet->getStyle('A21:B21')->getFont()->getColor()->setRGB('FFFFFF');
        }
    }

    public function title(): string
    {
        return __('Profit Loss Report');
    }
}
