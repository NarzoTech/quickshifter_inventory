<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(private $users) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->users;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Customer List'],
            ['Time: ' . now()],
            ['SL', 'Name', 'Mobile', 'Area', 'Purchase', '', '', 'Purchase Return', '', ''],
            ['', '', '', '', 'Total', 'Pay', 'Due', 'Total', 'Pay', 'Due']
        ];
    }

    public function map($user): array
    {
        // Map the data to match your format
        return [
            $user->id,                        // SL
            $user->name,                      // Name
            $user->phone,                    // Mobile
            $user->area->name,                      // Area
            $user->sales->sum('grand_total') ?? 0,            // Purchase Total
            $user->total_paid ?? 0,              // Purchase Pay
            $user->total_due ?? 0,              // Purchase Due
            $user->total_sale_return ?? 0,      //
            $user->total_sale_return_pay ?? 0,  // Purchase Return Pay
            $user->total_sale_return_due ?? 0,  // Purchase Return Due

        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:J1');  // Title
        $sheet->mergeCells('A2:J2');  // Subtitle
        $sheet->mergeCells('A3:J3');  // Time

        // Merge cells for top-level headers
        $sheet->mergeCells('E4:G4');  // Purchase spans 'Total' and 'Pay'
        $sheet->mergeCells('H4:J4');  // Purchase Return spans 'Total' and 'Pay'

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


        // from c6 to end data will be right aligned
        $sheet->getStyle('C6:C' . $sheet->getHighestRow())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:J3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return 'Customer List';  // Sheet title
    }
}
