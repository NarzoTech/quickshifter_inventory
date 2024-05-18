<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Http\Requests\VehicleRequest;
use Modules\Customer\app\Models\Vehicle;

class VehicleController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private Vehicle $vehicle)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::paginate(20);

        return view('customer::vehicle.index', compact('vehicles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request): RedirectResponse
    {
        try {
            $this->vehicle->create($request->validated());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.vehicle.index', [], ['messege' => 'Vehicle created successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.vehicle.index', [], ['messege' => 'Vehicle creation failed', 'alert-type' => 'error']);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, $id): RedirectResponse
    {
        try {
            Vehicle::find($id)->update($request->validated());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.vehicle.index', [], ['messege' => 'Vehicle updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.vehicle.index', [], ['messege' => 'Vehicle update failed', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            Vehicle::destroy($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.vehicle.index', [], ['messege' => 'Vehicle deleted successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.vehicle.index', [], ['messege' => 'Vehicle deletion failed', 'alert-type' => 'error']);
        }
    }
}
