<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ExpenseSupplierExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{

    public function __construct(private $suppliers) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Check if it's already a collection, otherwise execute the query
        if ($this->suppliers instanceof \Illuminate\Support\Collection || $this->suppliers instanceof \Illuminate\Database\Eloquent\Collection) {
            return $this->suppliers;
        }
        return $this->suppliers->get();
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Expense Supplier List'],
            ['Time: ' . now()],
            [],
            ['SL', 'Name', 'Company', 'Phone', 'Email', 'Address', 'Total Expense', 'Total Paid', 'Advance', 'Total Due']
        ];
    }

    public function map($supplier): array
    {
        static $serial = 0;
        $serial++;

        return [
            $serial,
            $supplier->name,
            $supplier->company ?? '-',
            $supplier->phone ?? '-',
            $supplier->email ?? '-',
            $supplier->address ?? '-',
            $supplier->total_expense ?? 0,
            $supplier->total_paid ?? 0,
            $supplier->advance ?? 0,
            $supplier->total_due ?? 0,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $suppliers = $this->collection();
                
                // Calculate the row number where we need to add the total
                // 5 header rows + number of supplier rows + 1
                $lastRow = 5 + $suppliers->count() + 1;
                
                // Calculate totals
                $totalExpense = 0;
                $totalPaid = 0;
                $totalAdvance = 0;
                $totalDue = 0;
                
                foreach ($suppliers as $supplier) {
                    $totalExpense += $supplier->total_expense ?? 0;
                    $totalPaid += $supplier->total_paid ?? 0;
                    $totalAdvance += $supplier->advance ?? 0;
                    $totalDue += $supplier->total_due ?? 0;
                }
                
                // Add total row
                $event->sheet->getDelegate()->setCellValue('A' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('B' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('C' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('D' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('E' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('F' . $lastRow, 'Total');
                $event->sheet->getDelegate()->setCellValue('G' . $lastRow, $totalExpense);
                $event->sheet->getDelegate()->setCellValue('H' . $lastRow, $totalPaid);
                $event->sheet->getDelegate()->setCellValue('I' . $lastRow, $totalAdvance);
                $event->sheet->getDelegate()->setCellValue('J' . $lastRow, $totalDue);
                
                // Make the total row bold
                $event->sheet->getDelegate()->getStyle('F' . $lastRow . ':J' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:J1');  // Title
        $sheet->mergeCells('A2:J2');  // Subtitle
        $sheet->mergeCells('A3:J3');  // Time

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A5:J5')->getFont()->setBold(true);
        $sheet->getStyle('A5:J5')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
    }

    public function title(): string
    {
        return 'Expense Supplier List';
    }
}
