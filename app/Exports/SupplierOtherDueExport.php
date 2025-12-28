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

class SupplierOtherDueExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    public function __construct(private $summeries) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->summeries;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Supplier Other Due List'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Name'),
                __('Company'),
                __('Phone'),
                __('Total'),
                __('Paid'),
                __('Due')
            ],
        ];
    }
    public function map($summery): array
    {
        // Map the data to match your format
        return [
            ++$this->index,
            $summery->name,
            $summery->company,
            $summery->phone,
            $summery->otherSummery->sum('amount'),
            $summery->otherSummery->sum('paid'),
            $summery->otherSummery->sum('due') ?? 0,
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $summeries = $this->collection();
                
                // Calculate the row number where we need to add the total
                // 4 header rows + number of supplier rows + 1
                $lastRow = 4 + $summeries->count() + 1;
                
                // Calculate totals
                $totalAmount = 0;
                $totalPaid = 0;
                $totalDue = 0;
                
                foreach ($summeries as $summery) {
                    $totalAmount += $summery->otherSummery->sum('amount');
                    $totalPaid += $summery->otherSummery->sum('paid');
                    $totalDue += $summery->otherSummery->sum('due') ?? 0;
                }
                
                // Add total row
                $event->sheet->getDelegate()->setCellValue('A' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('B' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('C' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('D' . $lastRow, 'Total');
                $event->sheet->getDelegate()->setCellValue('E' . $lastRow, $totalAmount);
                $event->sheet->getDelegate()->setCellValue('F' . $lastRow, $totalPaid);
                $event->sheet->getDelegate()->setCellValue('G' . $lastRow, $totalDue);
                
                // Make the total row bold
                $event->sheet->getDelegate()->getStyle('D' . $lastRow . ':G' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:G1');  // Title
        $sheet->mergeCells('A2:G2');  // Subtitle
        $sheet->mergeCells('A3:G3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:G' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);


        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Supplier Other Due List';
    }
}
