<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Product\app\Models\Product;

class StockReconcile extends Command
{
    protected $signature = 'stock:reconcile
                            {--fix : Automatically fix discrepancies by updating products.stock from stocks table}
                            {--product= : Only reconcile a specific product ID}';

    protected $description = 'Compare products.stock with SUM(in_quantity) - SUM(out_quantity) from stocks table and report/fix discrepancies';

    public function handle(): int
    {
        $this->info('Starting stock reconciliation...');
        $this->newLine();

        $query = Product::query();
        if ($productId = $this->option('product')) {
            $query->where('id', $productId);
        }

        $products = $query->get();
        $discrepancies = [];
        $fixed = 0;

        foreach ($products as $product) {
            // Get raw stock value from DB (bypasses number_format accessor)
            $rawStock = (int) DB::table('products')->where('id', $product->id)->value('stock');

            // Calculate expected stock from stocks table
            $stockData = DB::table('stocks')
                ->where('product_id', $product->id)
                ->selectRaw('COALESCE(SUM(in_quantity), 0) as total_in, COALESCE(SUM(out_quantity), 0) as total_out')
                ->first();

            $expectedStock = (int) ($stockData->total_in - $stockData->total_out);

            if ($rawStock !== $expectedStock) {
                $diff = $rawStock - $expectedStock;
                $discrepancies[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'products_stock' => $rawStock,
                    'calculated_stock' => $expectedStock,
                    'difference' => $diff,
                ];

                if ($this->option('fix')) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['stock' => $expectedStock]);
                    $fixed++;
                }
            }
        }

        if (empty($discrepancies)) {
            $this->info('No discrepancies found. All product stock values match the stocks table.');
            return self::SUCCESS;
        }

        $this->warn('Found ' . count($discrepancies) . ' discrepancies:');
        $this->newLine();

        $this->table(
            ['Product ID', 'Name', 'SKU', 'products.stock', 'Calculated Stock', 'Difference'],
            array_map(function ($d) {
                return [
                    $d['id'],
                    mb_substr($d['name'], 0, 30),
                    $d['sku'],
                    $d['products_stock'],
                    $d['calculated_stock'],
                    ($d['difference'] > 0 ? '+' : '') . $d['difference'],
                ];
            }, $discrepancies)
        );

        if ($this->option('fix')) {
            $this->newLine();
            $this->info("Fixed {$fixed} product(s) — updated products.stock to match stocks table.");
        } else {
            $this->newLine();
            $this->comment('Run with --fix to automatically correct these discrepancies.');
        }

        return self::SUCCESS;
    }
}
