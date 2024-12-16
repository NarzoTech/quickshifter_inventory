<?php

namespace Modules\Attendance\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Attendance\app\Http\Requests\HolidaysRequest;
use Modules\Attendance\app\Models\HolidaySetup;

class HolidaysController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $holidays = HolidaySetup::paginate(20);
        return view('attendance::holidays.index', compact('holidays'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidaysRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['start_date'] = now()->parse($data['start_date']);
            $data['end_date'] = now()->parse($data['end_date']);
            HolidaySetup::create($data);

            $notification = [
                'messege' => 'Holiday created successfully',
                'alert-type' => 'success',
            ];
            saveLog('Holiday created successfully', 'info');
            return back()->with($notification);
        } catch (\Exception $e) {
            saveLog($e->getMessage(), 'error');
            $notification = [
                'messege' => $e->getMessage(),
                'alert-type' => 'error',
            ];
            return back()->with($notification);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidaysRequest $request, $id): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['start_date'] = now()->parse($data['start_date']);
            $data['end_date'] = now()->parse($data['end_date']);
            HolidaySetup::where('id', $id)->update($data);

            $notification = [
                'messege' => 'Holiday updated successfully',
                'alert-type' => 'success',
            ];
            saveLog('Holiday updated successfully', 'info');
            return back()->with($notification);
        } catch (\Exception $e) {
            saveLog($e->getMessage(), 'error');
            $notification = [
                'messege' => $e->getMessage(),
                'alert-type' => 'error',
            ];
            return back()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            HolidaySetup::find($id)->delete();
            $notification = [
                'message' => 'Holiday deleted successfully',
                'alert-type' => 'success',
            ];
            saveLog('Holiday deleted successfully', 'info');
            return back()->with($notification);
        } catch (\Exception $e) {
            saveLog($e->getMessage(), 'error');
            $notification = [
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ];
            return back()->with($notification);
        }
    }
}
