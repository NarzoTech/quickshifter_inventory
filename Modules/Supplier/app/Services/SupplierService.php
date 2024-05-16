<?php

namespace Modules\Supplier\app\Services;

use Illuminate\Http\Request;
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

    public function storeSupplier(Request $request)
    {
        $data = $request->except('_token');
        $data['created_by'] = auth()->id();
        return $this->supplier->create($data);
    }
}