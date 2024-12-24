<?php

namespace Modules\Purchase\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Purchase\app\Models\PurchaseReturnType;

class PurchaseReturnTypeController extends Controller
{

    use RedirectHelperTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $lists = PurchaseReturnType::all();
        return view('purchase::return-list', compact('lists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('purchase::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        $data['created_by'] = auth('admin')->id();
        PurchaseReturnType::create($data);

        return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.purchase.return.type.list', [], ['messege' => 'Purchase Return Type Created Successfully.', 'alert-type' => 'success']);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('purchase::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('purchase::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        $data['updated_by'] = auth('admin')->id();
        PurchaseReturnType::find($id)->update($data);

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.purchase.return.type.list', [], ['messege' => 'Purchase Return Type Updated Successfully.', 'alert-type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        PurchaseReturnType::find($id)->delete();
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.purchase.return.type.list', [], ['messege' => 'Purchase Return Type Deleted Successfully.', 'alert-type' => 'success']);
    }
}
