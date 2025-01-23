<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $count;
    public function __construct(private $sales) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Sales List'],
            ['Time: ' . now()],
            [
                __('SL.'),
                __('Date'),
                __('Invoice No'),
                __('Customer'),
                __('Remark'),
                __('Sale Amount'),
                __('Total Amount'),
                __('Paid Amount'),
                __('Due'),
                __('Payment Status')
            ],
        ];
    }

    public function map($sale): array
    {
        $paymentStatus = ($sale->paid_amount == $sale->grand_total)
            ? __('Paid')
            : (($sale->due_amount > 0 && $sale->paid_amount < $sale->total_price)
                ? __('Partial Due')
                : __('Due'));
        // Map the data to match your format
        return [
            ++$this->count,
            $sale->order_date->format('d-m-y'),
            $sale->invoice,
            $sale?->customer?->name ?? 'Guest',
            $sale->sale_note,
            $sale->total_price,
            $sale->grand_total,
            $sale->paid_amount,
            $sale->due_amount,
            $paymentStatus
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
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);


        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }


    public function title(): string
    {
        return 'Sales List';
    }
}
