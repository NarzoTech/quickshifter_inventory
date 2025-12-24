<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OtherSalesReportExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $sales;
    private $data;

    public function __construct($sales, $data = null)
    {
        $this->sales = $sales;
        $this->data = $data;
    }
    /**
     * @return array
     */
    public function array(): array
    {
        $rows = [];
        $index = 0;
        
        foreach ($this->sales as $sale) {
            // Get only products from outside (source = 2)
            $outsideProducts = $sale->details->where('source', 2);
            
            foreach ($outsideProducts as $pIndex => $detail) {
                $productName = 'N/A';
                if ($detail->product_id) {
                    $productName = $detail->product->name ?? 'N/A';
                } elseif ($detail->service_id) {
                    $productName = $detail->service->name ?? 'N/A';
                }
                
                $profit = ($detail->selling_price - $detail->purchase_price) * $detail->quantity;
                
                $rows[] = [
                    $pIndex == 0 ? ++$index : '',
                    $pIndex == 0 ? $sale->order_date->format('d-m-Y') : '',
                    $pIndex == 0 ? $sale->invoice : '',
                    $pIndex == 0 ? ($sale?->customer?->name ?? 'Guest') : '',
                    $productName,
                    $detail->quantity,
                    $detail->purchase_price,
                    $detail->selling_price,
                    $profit,
                    $pIndex == 0 ? $sale->sale_note : '',
                ];
            }
        }
        
        return $rows;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Other Sales Report (Sales From Outside)'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Date'),
                __('Invoice'),
                __('Customer'),
                __('Product Name'),
                __('Quantity'),
                __('Purchase Price'),
                __('Selling Price'),
                __('Profit'),
                __('Remark'),
            ],
        ];
    }

    public function registerEvents(): array
    {
        $data = $this->data;
        return [
            AfterSheet::class => function (AfterSheet $event) use ($data) {
                $lastRow = $event->sheet->getHighestRow() + 1;
                $event->sheet->setCellValue('A' . $lastRow, '');
                $event->sheet->setCellValue('B' . $lastRow, '');
                $event->sheet->setCellValue('C' . $lastRow, '');
                $event->sheet->setCellValue('D' . $lastRow, '');
                $event->sheet->setCellValue('E' . $lastRow, 'Total');
                $event->sheet->setCellValue('F' . $lastRow, $data['total_quantity'] ?? 0);
                $event->sheet->setCellValue('G' . $lastRow, currency($data['total_purchase'] ?? 0));
                $event->sheet->setCellValue('H' . $lastRow, currency($data['total_selling'] ?? 0));
                $event->sheet->setCellValue('I' . $lastRow, currency($data['total_profit'] ?? 0));
                $event->sheet->setCellValue('J' . $lastRow, '');
                $event->sheet->getStyle('A' . $lastRow . ':J' . $lastRow)->getFont()->setBold(true);
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
        $sheet->getStyle('A4:J' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(30);



        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Other Sales Report';
    }
}
