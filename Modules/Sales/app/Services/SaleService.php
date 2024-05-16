<?php

namespace Modules\Sales\app\Services;

use Modules\Sales\app\Models\Sale;

class SaleService
{
    public function __construct(private Sale $sale)
    {
    }
    public function getSales()
    {
        return $this->sale;
    }
    public function createSale(array $data): Sale
    {
        return Sale::create($data);
    }

    public function updateSale(Sale $sale, array $data): Sale
    {
        $sale->update($data);
        return $sale;
    }

    public function deleteSale(Sale $sale): void
    {
        $sale->delete();
    }
}