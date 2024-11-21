<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
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
                'Created By',
                'Type',
                'Amount',
                'Payment Type',
            ],
        ];
    }
    public function map($data): array
    {
        return [
            $data->date,
            $data->createdBy->name,
            $data->expenseType->name,
            $data->amount,
            ucfirst($data->payment_type)
        ];
    }
}
