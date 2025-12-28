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

class EmployeeSalaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    public function __construct(private $payments) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->payments;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Employee Paid Salary List'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Employee'),
                __('Paid'),
                __('Date'),
                __('Note')
            ],
        ];
    }
    public function map($payment): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            $payment->employee?->name,
            $payment->amount,
            now()->parse($payment->date)->format('d-m-Y'),
            $payment->note,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:E1');  // Title
        $sheet->mergeCells('A2:E2');  // Subtitle
        $sheet->mergeCells('A3:E3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:E' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Calculate the row number where we need to add the total
                // 4 header rows + number of payment rows + 1
                $lastRow = 4 + $this->payments->count() + 1;
                
                // Calculate total
                $total = $this->payments->sum('amount');
                
                // Add total row
                $event->sheet->getDelegate()->setCellValue('A' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('B' . $lastRow, 'Total');
                $event->sheet->getDelegate()->setCellValue('C' . $lastRow, $total);
                $event->sheet->getDelegate()->setCellValue('D' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('E' . $lastRow, '');
                
                // Make the total row bold
                $event->sheet->getDelegate()->getStyle('B' . $lastRow . ':C' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }
    
    public function title(): string
    {
        return 'Employee Paid Salary List';
    }
}
