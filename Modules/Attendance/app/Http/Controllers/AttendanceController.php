<?php

namespace Modules\Attendance\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;



use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Modules\Attendance\app\Models\Attendance;
use Modules\Attendance\app\Models\WeekendSetup;
use Modules\Employee\app\Services\EmployeeService;

class AttendanceController extends Controller
{
    public function __construct(private EmployeeService $employee)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // checkAdminHasPermissionAndThrowException('attendance.list');

        $employees = $this->employee->all()->paginate(20);

        return view('attendance::index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // checkAdminHasPermissionAndThrowException('attendance.create');


        $employees = $this->employee->all()->paginate(20);
        return view('attendance::create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // checkAdminHasPermissionAndThrowException(['attendance.store', 'attendance.update']);

        $request->validate([
            'date' => 'required',
            'employee_id' => 'required',
            'employee_id.*' => 'required|numeric',
            'attendance' => 'required',
            'attendance.*' => 'required|in:absent,present',
        ], [
            'date.required' => __('Date is required'),
            'employee_id.required' => __('Employee is required'),
            'attendance.required' => __('Attendance is required'),
        ]);
        $date = $request->date;
        $employees = $request->employee_id;
        $attendances = $request->attendance;

        // get all attendances for the date
        $attendancesList = Attendance::where('date', now()->parse($date))->pluck('employee_id')->toArray();




        foreach ($employees as $key => $employee) {
            // check if member has already taken attendance for the date
            if (in_array($employee, $attendancesList)) {
                // update attendance
                $attendance = Attendance::where('date', now()->parse($date))->where('employee_id', $employee)->first();
                $attendance->update(['status' => $attendances[$key]]);
                continue;
            }

            Attendance::create([
                'date' => now()->parse($date),
                'status' => $attendances[$key],
                'employee_id' => $employee
            ]);
        }

        return response()->json(['message' => __('Attendance Taken'), 'success' => true]);
    }

    public function weekDays()
    {
        $days = WeekendSetup::all();
        return view('attendance::weekdays', compact('days'));
    }
    public function weekDaysUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required|boolean',
            'is_weekend' => 'required|boolean'
        ]);
        WeekendSetup::updateOrCreate(['id' => $id], $request->except('_token'));
        return back()->with(['messege' => 'Weekend days updated successfully', 'alert-type' => 'success']);
    }
}
