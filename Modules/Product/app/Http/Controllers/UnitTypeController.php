<?php

namespace Modules\Product\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\LogActivity;
use App\Traits\RedirectHelperTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Services\UnitTypeService;

class UnitTypeController extends Controller
{
    use RedirectHelperTrait;
    protected $unitTypeService;

    public function __construct(UnitTypeService $unitTypeService)
    {
        $this->unitTypeService = $unitTypeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = $this->unitTypeService->getAll();
        $parentUnits = $this->unitTypeService->getParentUnits();
        return view('product::unit-types.index', compact('units', "parentUnits"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:unit_types,name',
            'ShortName' => 'required',
            'status' => 'required',
        ]);
        try {
            $unit = $this->unitTypeService->save($request);


            if ($request->ajax()) {
                return response()->json(['message' => 'Unit created successfully', 'unit' => $unit, 'status' => 200], 200);
            }
            return $this->redirectWithMessage(RedirectType::CREATE->value, "admin.unit.index");
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unit = $this->unitTypeService->findById($id);
        return $unit;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:unit_types,name,' . $id,
            'ShortName' => 'required',
            'status' => 'required',
        ]);
        try {
            $this->unitTypeService->update($request, $id);

            return $this->redirectWithMessage(RedirectType::UPDATE->value, "admin.unit.index");
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $result = $this->unitTypeService->delete($id);
            if ($result == "not_possible") {
                return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index");
            }

            return $this->redirectWithMessage(RedirectType::DELETE->value, "admin.unit.index");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index");
        }
    }

    public function unitByParent($id)
    {
        $unit = $this->unitTypeService->findById($id);

        return response()->json($unit, 200);
    }
}
