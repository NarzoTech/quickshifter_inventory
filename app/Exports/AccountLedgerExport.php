<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountLedgerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $index = 0;
    private $runningBalance;
    private $transactions;
    private $title;
    private $openingBalance;
    private $totalDebit;
    private $totalCredit;
    private $closingBalance;

    public function __construct($transactions, $title, $openingBalance, $totalDebit, $totalCredit, $closingBalance)
    {
        $this->transactions = $transactions;
        $this->title = $title;
        $this->openingBalance = $openingBalance;
        $this->totalDebit = $totalDebit;
        $this->totalCredit = $totalCredit;
        $this->closingBalance = $closingBalance;
        $this->runningBalance = $openingBalance;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name ?? 'Inventory System'],
            [$this->title],
            ['Generated: ' . now()->format('d-m-Y H:i:s')],
            ['Opening Balance: ' . number_format($this->openingBalance, 2)],
            [],
            [
                __('SN'),
                __('Date'),
                __('Description'),
                __('Reference'),
                __('Debit (IN)'),
                __('Credit (OUT)'),
                __('Balance'),
            ],
        ];
    }

    public function map($transaction): array
    {
        $this->runningBalance += $transaction['debit'] - $transaction['credit'];

        return [
            ++$this->index,
            is_string($transaction['date']) ? $transaction['date'] : $transaction['date']->format('d-m-Y'),
            $transaction['description'],
            $transaction['reference'],
            $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '-',
            $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '-',
            number_format($this->runningBalance, 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title rows
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A4:G4');

        // Apply styles to title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(11);

        // Center align title rows
        $sheet->getStyle('A1:G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Apply borders to data rows
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A6:G' . $highestRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Header row styling
        $sheet->getStyle('A6:G6')->getFont()->setBold(true);
        $sheet->getStyle('A6:G6')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Right align amount columns
        $sheet->getStyle('E7:G' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Add total row
        $totalRow = $highestRow + 1;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, '');
        $sheet->setCellValue('C' . $totalRow, '');
        $sheet->setCellValue('D' . $totalRow, __('Total'));
        $sheet->setCellValue('E' . $totalRow, number_format($this->totalDebit, 2));
        $sheet->setCellValue('F' . $totalRow, number_format($this->totalCredit, 2));
        $sheet->setCellValue('G' . $totalRow, number_format($this->closingBalance, 2));

        // Style the total row
        $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('E' . $totalRow . ':G' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    public function title(): string
    {
        return 'Account Ledger';
    }
}
