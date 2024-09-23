<?php

namespace Modules\Supplier\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Customer\app\Http\Requests\UserGroupRequest;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;

class SupplierGroupController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private UserGroupService $userGroup)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplierGroups = $this->userGroup->getUserGroup()->where('type', 'supplier')->paginate(request()->get('par-page') ? request()->get('par-page') : 20);
        return view('supplier::group.index', compact('supplierGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserGroupRequest $request): RedirectResponse
    {
        try {
            $this->userGroup->store($request->validated());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.supplierGroup.index', [], ['messege' => 'Supplier group created successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.supplierGroup.index', [], ['messege' => 'Supplier group creation failed', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('supplier::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('supplier::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserGroupRequest $request, $id): RedirectResponse
    {
        try {
            $this->userGroup->update($request->validated(), $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.supplierGroup.index', [], ['messege' => 'Supplier group updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.supplierGroup.index', [], ['messege' => 'Supplier group update failed', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->userGroup->destroy($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.supplierGroup.index', [], ['messege' => 'Supplier group deleted successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.supplierGroup.index', [], ['messege' => 'Supplier group delete failed', 'alert-type' => 'error']);
        }
    }
}
