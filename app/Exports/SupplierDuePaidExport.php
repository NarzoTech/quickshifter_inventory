<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SupplierDuePaidExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
    private Collection|Arrayable $payments;

    public function __construct(Collection|Arrayable $payments)
    {
        $this->payments = $payments;
    }
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
            ['Supplier Due Pay List'],
            ['Time: ' . now()],
            ['SL', 'Date', 'Invoice No', 'Supplier', 'Amount', 'Paid By']
        ];
    }
    public function map($payment): array
    {
        // Map the data to match your format
        return [
            $payment->id,
            now()->parse($payment->payment_date)->format('d M , Y'),
            $payment->purchase?->invoice_number,
            $payment->supplier->name,
            $payment->amount,
            $payment->createdBy->name
        ];
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
                $event->sheet->getDelegate()->setCellValue('B' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('C' . $lastRow, '');
                $event->sheet->getDelegate()->setCellValue('D' . $lastRow, 'Total');
                $event->sheet->getDelegate()->setCellValue('E' . $lastRow, $total);
                $event->sheet->getDelegate()->setCellValue('F' . $lastRow, '');
                
                // Make the total row bold
                $event->sheet->getDelegate()->getStyle('D' . $lastRow . ':E' . $lastRow)->getFont()->setBold(true);
            },
        ];
    }
    
    public function title(): string
    {
        return 'Supplier List';  // Sheet title
    }
}

