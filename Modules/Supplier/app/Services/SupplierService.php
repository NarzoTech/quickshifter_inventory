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
        $data['date'] = now()->parse($request->date);
        return $this->supplier->create($data);
    }

    public function updateSupplier(Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = auth()->id();
        $data['date'] = now()->parse($request->date);
        return $this->supplier->where('id', $id)->update($data);
    }

    public function deleteSupplier($id)
    {
        return $this->supplier->where('id', $id)->delete();
    }
}
