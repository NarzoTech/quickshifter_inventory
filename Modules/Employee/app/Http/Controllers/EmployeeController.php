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
    public function __construct(private EmployeeService $employee) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = $this->employee->all()->paginate(20);
        $employees->appends(request()->query());

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
            $data['join_date'] = now()->parse($request->join_date);
            if ($request->hasFile('image')) {
                $data['image'] = file_upload($request->file('image'));
            }
            $this->employee->store($data);
            saveLog('Employee added successfully');
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['messege' => 'Employee added successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {

            saveLog($e->getMessage(), 'error');
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
        $employee = $this->employee->find($id);
        return view('employee::edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, $id): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['join_date'] = now()->parse($request->join_date);
            if ($request->hasFile('image')) {
                $data['image'] = file_upload($request->file('image'), oldFile: $this->employee->find($id)->image);
            }
            $this->employee->update($id, $data);
            saveLog('Employee updated successfully');
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['messege' => 'Employee updated successfully', 'alert-type' => 'success']);
        } catch (\Throwable $th) {

            saveLog($th->getMessage(), 'error');
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['messege' => $th->getMessage(), 'alert-type' => 'danger']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->employee->destroy($id);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.employee.index', [], ['messege' => 'Employee deleted successfully', 'alert-type' => 'success']);
    }

    public function status($id)
    {
        $this->employee->changeStatus($id);
        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['messege' => 'Employee status updated successfully', 'alert-type' => 'success']);
    }
}
