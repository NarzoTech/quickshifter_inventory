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
            [__('Total Sales'), $this->data['totalSales']],
            [__('Sales Returns') . ' (' . __('Deduction') . ')', -$this->data['salesReturns']],
            [__('Net Sales'), $this->data['netSales']],
            [__('Purchase Returns') . ' (' . __('Refund from Supplier') . ')', $this->data['purchaseReturns']],
            [__('Total Income'), $this->data['totalIncome']],
            ['', ''],
            // Expense Section
            [__('EXPENSES'), ''],
            [__('Total Purchases'), $this->data['totalPurchases']],
            [__('Operating Expenses'), $this->data['expenses']],
            [__('Employee Salaries'), $this->data['salaries']],
            [__('Total Expenses'), $this->data['totalExpenses']],
            ['', ''],
            // Profit/Loss
            [__('NET PROFIT / LOSS'), $this->data['profitLoss']],
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
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Center align title rows
        $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Right align amount column
        $sheet->getStyle('B6:B' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Style section headers (Income, Expenses)
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A6:B6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA');

        $sheet->getStyle('A13')->getFont()->setBold(true);
        $sheet->getStyle('A13:B13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');

        // Style Total Income row
        $sheet->getStyle('A11')->getFont()->setBold(true);
        $sheet->getStyle('A11:B11')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('C3E6CB');

        // Style Total Expenses row
        $sheet->getStyle('A17')->getFont()->setBold(true);
        $sheet->getStyle('A17:B17')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F5C6CB');

        // Style Net Profit/Loss row
        $sheet->getStyle('A19')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B19')->getFont()->setBold(true)->setSize(12);

        if ($this->data['profitLoss'] >= 0) {
            $sheet->getStyle('A19:B19')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('28A745');
            $sheet->getStyle('A19:B19')->getFont()->getColor()->setRGB('FFFFFF');
        } else {
            $sheet->getStyle('A19:B19')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DC3545');
            $sheet->getStyle('A19:B19')->getFont()->getColor()->setRGB('FFFFFF');
        }
    }

    public function title(): string
    {
        return __('Profit Loss Report');
    }
}
