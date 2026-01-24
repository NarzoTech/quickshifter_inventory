<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class TransactionLoggerService
{
    private string $purchaseLogPath;
    private string $salesLogPath;

    public function __construct()
    {
        // Define log file paths
        $logDirectory = storage_path('logs/transactions');
        
        // Create directory if it doesn't exist
        if (!File::exists($logDirectory)) {
            File::makeDirectory($logDirectory, 0755, true);
        }

        $this->purchaseLogPath = $logDirectory . '/purchase_transactions.log';
        $this->salesLogPath = $logDirectory . '/sales_transactions.log';
    }

    /**
     * Log purchase transaction
     *
     * @param string $action - create, update, delete
     * @param array $data - transaction data
     * @param mixed $purchase - purchase model instance
     * @return void
     */
    public function logPurchase(string $action, array $data, $purchase = null): void
    {
        $logEntry = $this->formatPurchaseLog($action, $data, $purchase);
        $this->writeToFile($this->purchaseLogPath, $logEntry);
    }

    /**
     * Log sales transaction
     *
     * @param string $action - create, update, delete
     * @param array $data - transaction data
     * @param mixed $sale - sale model instance
     * @return void
     */
    public function logSale(string $action, array $data, $sale = null): void
    {
        $logEntry = $this->formatSaleLog($action, $data, $sale);
        $this->writeToFile($this->salesLogPath, $logEntry);
    }

    /**
     * Format purchase log entry
     */
    private function formatPurchaseLog(string $action, array $data, $purchase): string
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $userId = auth('admin')->id() ?? 'system';
        $userName = auth('admin')->user()->name ?? 'System';

        // Handle purchase_date - could be string or Carbon instance
        $purchaseDate = $data['purchase_date'] ?? null;
        if (!$purchaseDate && $purchase && $purchase->purchase_date) {
            $purchaseDate = $purchase->purchase_date instanceof \Carbon\Carbon
                ? $purchase->purchase_date->format('Y-m-d')
                : $purchase->purchase_date;
        }

        $logData = [
            'timestamp' => $timestamp,
            'action' => strtoupper($action),
            'user_id' => $userId,
            'user_name' => $userName,
            'purchase_id' => $purchase->id ?? 'N/A',
            'invoice_number' => $purchase->invoice_number ?? $data['invoice_number'] ?? 'N/A',
            'supplier_id' => $data['supplier_id'] ?? $purchase->supplier_id ?? 'N/A',
            'warehouse_id' => $data['warehouse_id'] ?? $purchase->warehouse_id ?? 'N/A',
            'purchase_date' => $purchaseDate ?? 'N/A',
            'total_amount' => $data['total_amount'] ?? $purchase->total_amount ?? 0,
            'paid_amount' => $purchase->paid_amount ?? ($data['total_amount'] - $data['due_amount']) ?? 0,
            'due_amount' => $data['due_amount'] ?? $purchase->due_amount ?? 0,
            'payment_status' => $purchase->payment_status ?? 'N/A',
            'payment_type' => is_array($data['payment_type'] ?? null) ? implode(', ', $data['payment_type']) : (is_array($purchase->payment_type ?? null) ? implode(', ', $purchase->payment_type) : ($purchase->payment_type ?? 'N/A')),
            'items' => $data['items'] ?? $purchase->items ?? 0,
            'memo_no' => $data['memo_no'] ?? $purchase->memo_no ?? 'N/A',
            'reference_no' => $data['reference_no'] ?? $purchase->reference_no ?? 'N/A',
            'note' => $data['note'] ?? $purchase->note ?? 'N/A',
        ];

        // Add product details if available
        if (isset($data['product_id']) && is_array($data['product_id'])) {
            $products = [];
            foreach ($data['product_id'] as $index => $productId) {
                $products[] = [
                    'product_id' => $productId,
                    'quantity' => $data['quantity'][$index] ?? 0,
                    'unit_price' => $data['unit_price'][$index] ?? 0,
                    'selling_price' => $data['selling_price'][$index] ?? 0,
                    'total' => $data['total'][$index] ?? 0,
                    'profit' => $data['profit'][$index] ?? 0,
                ];
            }
            $logData['products'] = $products;
        } elseif ($purchase && $purchase->purchaseDetails) {
            $products = [];
            foreach ($purchase->purchaseDetails as $detail) {
                $products[] = [
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product->name ?? 'N/A',
                    'quantity' => $detail->quantity,
                    'purchase_price' => $detail->purchase_price,
                    'sale_price' => $detail->sale_price,
                    'sub_total' => $detail->sub_total,
                    'profit' => $detail->profit,
                ];
            }
            $logData['products'] = $products;
        }

        return $this->formatLogEntry($logData);
    }

    /**
     * Format sale log entry
     */
    private function formatSaleLog(string $action, array $data, $sale): string
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $userId = auth('admin')->id() ?? 'system';
        $userName = auth('admin')->user()->name ?? 'System';

        // Handle sale_date - could be string or Carbon instance
        $saleDate = $data['sale_date'] ?? null;
        if (!$saleDate && $sale && isset($sale->order_date) && $sale->order_date) {
            $saleDate = $sale->order_date instanceof \Carbon\Carbon
                ? $sale->order_date->format('Y-m-d')
                : $sale->order_date;
        }

        // Handle due_date - could be string or Carbon instance
        $dueDate = $data['due_date'] ?? null;
        if (!$dueDate && $sale && isset($sale->due_date) && $sale->due_date) {
            $dueDate = $sale->due_date instanceof \Carbon\Carbon
                ? $sale->due_date->format('Y-m-d')
                : $sale->due_date;
        }

        $logData = [
            'timestamp' => $timestamp,
            'action' => strtoupper($action),
            'user_id' => $userId,
            'user_name' => $userName,
            'sale_id' => $sale->id ?? 'N/A',
            'invoice_number' => $sale->invoice ?? 'N/A',
            'customer_id' => $data['order_customer_id'] ?? $sale->customer_id ?? 'N/A',
            'warehouse_id' => $sale->warehouse_id ?? 1,
            'sale_date' => $saleDate ?? 'N/A',
            'sub_total' => $data['sub_total'] ?? $sale->total_price ?? 0,
            'discount_amount' => $data['discount_amount'] ?? $sale->order_discount ?? 0,
            'total_tax' => $data['total_tax'] ?? $sale->total_tax ?? 0,
            'grand_total' => $data['total_amount'] ?? $sale->grand_total ?? 0,
            'paid_amount' => $sale->paid_amount ?? (isset($data['paying_amount']) ? array_sum($data['paying_amount']) : 0),
            'due_amount' => $sale->due_amount ?? 0,
            'receive_amount' => $data['receive_amount'] ?? $sale->receive_amount ?? 0,
            'return_amount' => $data['return_amount'] ?? $sale->return_amount ?? 0,
            'payment_method' => is_array($data['payment_type'] ?? null) ? implode(', ', $data['payment_type']) : (is_array($sale->payment_method ?? null) ? implode(', ', $sale->payment_method) : ($sale->payment_method ?? 'N/A')),
            'payment_status' => $sale->payment_status ?? 'N/A',
            'quantity' => $sale->quantity ?? 0,
            'due_date' => $dueDate ?? 'N/A',
            'note' => $data['remark'] ?? $sale->sale_note ?? 'N/A',
        ];

        // Add cart/product details if available
        if (isset($data['cart']) && is_array($data['cart'])) {
            $products = [];
            foreach ($data['cart'] as $item) {
                $products[] = [
                    'type' => $item['type'] ?? 'N/A',
                    'product_id' => $item['id'] ?? 'N/A',
                    'name' => $item['name'] ?? 'N/A',
                    'sku' => $item['sku'] ?? 'N/A',
                    'quantity' => $item['qty'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'purchase_price' => $item['purchase_price'] ?? 0,
                    'selling_price' => $item['selling_price'] ?? 0,
                    'sub_total' => $item['sub_total'] ?? 0,
                    'source' => $item['source'] ?? 'N/A',
                ];
            }
            $logData['products'] = $products;
        } elseif ($sale && $sale->products) {
            $products = [];
            foreach ($sale->products as $detail) {
                $products[] = [
                    'product_id' => $detail->product_id,
                    'service_id' => $detail->service_id,
                    'sku' => $detail->product_sku,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'purchase_price' => $detail->purchase_price,
                    'selling_price' => $detail->selling_price,
                    'sub_total' => $detail->sub_total,
                    'source' => $detail->source,
                ];
            }
            $logData['products'] = $products;
        }

        return $this->formatLogEntry($logData);
    }

    /**
     * Format log entry as readable string
     */
    private function formatLogEntry(array $logData): string
    {
        $separator = str_repeat('=', 100);
        $lines = [$separator];
        
        foreach ($logData as $key => $value) {
            if ($key === 'products') {
                $lines[] = 'Products:';
                if (is_array($value)) {
                    foreach ($value as $index => $product) {
                        $lines[] = '  Product #' . ($index + 1) . ':';
                        foreach ($product as $pKey => $pValue) {
                            $lines[] = '    ' . str_pad($pKey . ':', 20) . $pValue;
                        }
                    }
                }
            } else {
                $formattedKey = ucwords(str_replace('_', ' ', $key));
                $lines[] = str_pad($formattedKey . ':', 20) . $value;
            }
        }
        
        $lines[] = $separator;
        $lines[] = ''; // Empty line for readability
        
        return implode(PHP_EOL, $lines);
    }

    /**
     * Write log entry to file
     */
    private function writeToFile(string $filePath, string $content): void
    {
        File::append($filePath, $content);
    }

    /**
     * Get purchase log file path
     */
    public function getPurchaseLogPath(): string
    {
        return $this->purchaseLogPath;
    }

    /**
     * Get sales log file path
     */
    public function getSalesLogPath(): string
    {
        return $this->salesLogPath;
    }

    /**
     * Read purchase logs
     */
    public function readPurchaseLogs(int $lines = 100): string
    {
        return $this->readLastLines($this->purchaseLogPath, $lines);
    }

    /**
     * Read sales logs
     */
    public function readSalesLogs(int $lines = 100): string
    {
        return $this->readLastLines($this->salesLogPath, $lines);
    }

    /**
     * Read last N lines from a file
     */
    private function readLastLines(string $filePath, int $lines): string
    {
        if (!File::exists($filePath)) {
            return 'Log file not found.';
        }

        $file = new \SplFileObject($filePath, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - $lines);
        
        $file->seek($startLine);
        $content = '';
        while (!$file->eof()) {
            $content .= $file->current();
            $file->next();
        }
        
        return $content;
    }
}
