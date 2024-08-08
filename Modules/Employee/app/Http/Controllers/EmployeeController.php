<?php

namespace Modules\Employee\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Employee\app\Http\Requests\EmployeeRequest;
use Modules\Employee\app\Services\EmployeeService;

class EmployeeController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private EmployeeService $employee)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = $this->employee->all()->paginate(20);
        return view('employee::index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employee::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = file_upload($request->file('image'));
            }
            $this->employee->store($data);

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['messege' => 'Employee added successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['messege' => $e->getMessage(), 'alert-type' => 'danger']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('employee::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('employee::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
