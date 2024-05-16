<?php

namespace Modules\Supplier\app\Services;

use Modules\Supplier\app\Models\Supplier;

class SupplierService
{
    public function __construct(private Supplier $supplier)
    {
        
    }

    public function all()
    {
        return $this->supplier;
    }
}