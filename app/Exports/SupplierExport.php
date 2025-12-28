<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Supplier\app\Services\SupplierService;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SupplierExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private int $rowNumber = 0;

    public function __construct(private $supplier) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Check if it's already a collection, otherwise execute the query
        if ($this->supplier instanceof \Illuminate\Support\Collection || $this->supplier instanceof \Illuminate\Database\Eloquent\Collection) {
            return $this->supplier;
        }
        return $this->supplier->get();
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Supplier List'],
            ['Time: ' . now()],
            ['SL', 'Name', 'Mobile', 'Area', 'Purchase', '', 'Purchase Return', ''],
            ['', '', '', '', 'Total', 'Pay', 'Total', 'Pay', 'Advance', 'Total Due']
        ];
    }

    public function map($supplier): array
    {
        $this->rowNumber++;
        
        $totalReturn = $supplier->purchaseReturn->sum('return_amount');
        $totalReturnPaid = $supplier->purchaseReturn->sum(
            'received_amount',
        );
        // Map the data to match your format
        return [
            $this->rowNumber,                     // SL (serial number)
            $supplier->name,                      // Name
            $supplier->phone,                    // Mobile
            $supplier->area->name,                      // Area
            $supplier->total_purchase ?? 0,            // Purchase Total
            $supplier->total_paid ?? 0,              // Purchase Pay
            $totalReturn ?? 0,     // Purchase Return Total
            $totalReturnPaid ?? 0,       // Purchase Return Pay
            $supplier->advance ?? 0,                   // Advance
            $supplier->total_due - $totalReturn ?? 0,                 // Total Due
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
                $totalPurchase = 0;
                $totalPaid = 0;
                $totalReturnAmount = 0;
                $totalReturnPaid = 0;
                $totalAdvance = 0;
                $totalDue = 0;
                
                foreach ($suppliers as $supplier) {
                    $returnAmount = $supplier->purchaseReturn->sum('return_amount');
                    $returnPaid = $supplier->purchaseReturn->sum('received_amount');
                    
                    $totalPurchase += $supplier->total_purchase ?? 0;
                    $totalPaid += $supplier->total_paid ?? 0;
                    $totalReturnAmount += $returnAmount;
                    $totalReturnPaid += $returnPaid;
                    $totalAdvance += $supplier->advance ?? 0;
                    $totalDue += ($supplier->total_due - $returnAmount) ?? 0;
                }
                
                // Add total row
                $event->sheet->getDelegate()->setCellValue('A' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('B' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('C' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('D' . $lastRow, 'Total');
                $event->sheet->getDelegate()->setCellValue('E' . $lastRow, $totalPurchase);
                $event->sheet->getDelegate()->setCellValue('F' . $lastRow, $totalPaid);
                $event->sheet->getDelegate()->setCellValue('G' . $lastRow, $totalReturnAmount);
                $event->sheet->getDelegate()->setCellValue('H' . $lastRow, $totalReturnPaid);
                $event->sheet->getDelegate()->setCellValue('I' . $lastRow, $totalAdvance);
                $event->sheet->getDelegate()->setCellValue('J' . $lastRow, $totalDue);
                
                // Make the total row bold
                $event->sheet->getDelegate()->getStyle('D' . $lastRow . ':J' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:J1');  // Title
        $sheet->mergeCells('A2:J2');  // Subtitle
        $sheet->mergeCells('A3:J3');  // Time

        // Merge cells for top-level headers
        $sheet->mergeCells('E4:F4');  // Purchase spans 'Total' and 'Pay'
        $sheet->mergeCells('G4:H4');  // Purchase Return spans 'Total' and 'Pay'

        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A5:J6')->getFont()->setBold(true);
        $sheet->getStyle('A5:J6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A5:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
    }

    public function title(): string
    {
        return 'Supplier List';  // Sheet title
    }
}
