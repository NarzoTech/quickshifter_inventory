<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class DTSExport implements FromCollection, WithHeadings, WithMapping
{
    private $balance = 0;
    public function __construct(private $data) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            [
                'Date',
                'Mode',
                'Category',
                'Particular',
                'Revenue/Received/Credit',
                'Expense/Paid/Debit',
                'Balance',
                'IV Cost',
            ],
        ];
    }
    public function map($data): array
    {
        if ($data->mode != 'Credit' && $data->category != 'Inventory') {
            $this->balance += $data->credit - $data->debit;
        }
        $accountList = array_values(accountList());
        if (
            $data->category == 'Inventory' &&
            ($data->mode == 'R/P Credit' || in_array($data->mode, $accountList))
        ) {
            $this->balance += $data->credit - $data->debit;
        }
        return [
            $data->date,
            $data->mode,
            $data->category,
            $data->particular,
            $data->credit,
            $data->debit,
            $this->balance,
            $data->iv
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Set the width of the columns (e.g., A, B, C, etc.)
        $sheet->getColumnDimension('A')->setWidth(20); // Adjust the width for column A
        $sheet->getColumnDimension('B')->setWidth(25); // Adjust the width for column B
        $sheet->getColumnDimension('C')->setWidth(30); // Adjust the width for column C
        $sheet->getColumnDimension('D')->setWidth(35); // Adjust the width for column D

        // Set styles for header row (row 1)
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,           // Make headers bold
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center text
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,  // Add borders to headers
                ],
            ],
        ]);

        // Align the entire sheet's cells to the left (optional)
        $sheet->getStyle('A2:H100')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, // Left-align text
            ],
        ]);
    }
}
