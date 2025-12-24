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

class BarcodeWiseProductExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    private $index;
    private $products;
    private $data;

    public function __construct($products, $data = null)
    {
        $this->products = $products;
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->products;
    }
    public function headings(): array
    {
        $setting = cache('setting');
        return [
            [$setting->app_name],  // Title rows
            ['Barcode Wise Product Report'],
            ['Time: ' . now()],
            [
                __('SN'),
                __('Product Name'),
                __('Attribute'),
                __('Barcode'),
                __('Brand Name'),
                __('Sale'),
                __('Sale Return'),
                __('Purchase'),
            ],
        ];
    }
    public function map($product): array
    {
        // Only count sales from own inventory (source = 1)
        $ownSalesQuery = \Modules\Sales\app\Models\ProductSale::where('product_id', $product->id)
            ->where('source', 1);
        
        $ownSalesReturnsQuery = \Modules\Sales\app\Models\SalesReturnDetails::where('product_id', $product->id)
            ->where('source', 1);
        
        // Apply date filters if provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            
            $ownSalesQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('order_date', [$fromDate, $toDate]);
            });
            
            $ownSalesReturnsQuery->whereHas('saleReturn', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, $toDate]);
            });
        }
        
        $ownSales = $ownSalesQuery->get();
        $ownSalesReturns = $ownSalesReturnsQuery->get();
        
        // Calculate sales
        $saleQty = $ownSales->sum('quantity');
        $salePrice = $ownSales->sum('sub_total');
        
        // Calculate returns
        $returnQty = $ownSalesReturns->sum('quantity');
        $returnPrice = $ownSalesReturns->sum('sub_total');
        
        // Get purchase data
        $purchasePrice = (int) $product->total_purchase['price'];
        $purchaseQty = $product->total_purchase['qty'];
        
        // Map the data to match your format
        return [
            ++$this->index,
            $product->name,
            $product->attribute,
            $product->barcode,
            $product->brand->name ?? 'N/A',
            currency($salePrice) . "(" . number_format($saleQty, 0) . ")",
            currency($returnPrice) . "(" . number_format($returnQty, 0) . ")",
            currency($purchasePrice) . "(" . number_format($purchaseQty, 0) . ")"
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
                
                $totalSalePrice = isset($data['totalSalePrice']) ? $data['totalSalePrice'] : 0;
                $totalSaleQty = isset($data['totalSaleQty']) ? $data['totalSaleQty'] : 0;
                $totalReturnPrice = isset($data['totalReturnPrice']) ? $data['totalReturnPrice'] : 0;
                $totalReturnQty = isset($data['totalReturnQty']) ? $data['totalReturnQty'] : 0;
                $totalPurchasePrice = isset($data['totalPurchasePrice']) ? $data['totalPurchasePrice'] : 0;
                $totalPurchaseQty = isset($data['totalPurchaseQty']) ? $data['totalPurchaseQty'] : 0;
                
                $event->sheet->setCellValue('F' . $lastRow, currency($totalSalePrice) . "(" . $totalSaleQty . ")");
                $event->sheet->setCellValue('G' . $lastRow, currency($totalReturnPrice) . "(" . $totalReturnQty . ")");
                $event->sheet->setCellValue('H' . $lastRow, currency($totalPurchasePrice) . "(" . $totalPurchaseQty . ")");
                $event->sheet->getStyle('A' . $lastRow . ':H' . $lastRow)->getFont()->setBold(true);
            },
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
        $sheet->getColumnDimension('C')->setWidth(15);
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
        return 'Barcode Wise Product Report';
    }
}
