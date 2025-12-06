<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private $index;
    private $opening = 0;
    private  $credit = 0;
    private $debit = 0;
    public function __construct(private $ledgers, private $title) {}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->ledgers;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            [$this->title],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Invoice No'),
                __('Memo No'),
                __('Date'),
                __('Description'),
                $this->title == 'Supplier Ledger'
                    ? __('Paid') . ' (' . __('CREDIT') . ')'
                    : __('Received') . ' (' . __('DEBIT') . ')',
                $this->title == 'Supplier Ledger'
                    ? __('Product') . ' (' . __('DEBIT') . ')'
                    : __('Sales') . ' (' . __('CREDIT') . ')',
                __('Balance') . ($this->title == 'Supplier Ledger' ? ' (' . __('DUE') . ')' : ''),
            ],
        ];
    }
    public function map($ledger): array
    {

        $this->opening += $ledger->due_amount;
        $this->credit += $ledger->amount;
        $this->debit += $ledger->total_amount;

        // Map the data to match your format
        return [
            ++$this->index,
            $ledger->invoice_no,
            $ledger->purchase?->memo_no,
            $ledger->date,
            $ledger->invoice_type,
            $ledger->amount,
            $ledger->total_amount,
            $this->opening,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Merge cells for title and subtitle
        $sheet->mergeCells('A1:H1');  // Title
        $sheet->mergeCells('A2:H2');  // Subtitle
        $sheet->mergeCells('A3:H3');  // Time


        // Apply styles to title and subtitle
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);

        // Apply borders and center alignment to header rows
        $sheet->getStyle('A4:H' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Column Widths (Optional)
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);




        // a1 to j1 will be center aligned
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // a2 to j2, a3 to j3  will be center aligned
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return $this->title;
    }
}
