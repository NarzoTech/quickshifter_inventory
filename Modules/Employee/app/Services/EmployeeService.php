<?php

namespace Modules\Employee\app\Services;

use Modules\Employee\app\Models\Employee;

class EmployeeService
{
    public function __construct(private Employee $employee)
    {
    }

    public function all()
    {
        return $this->employee;
    }

    public function find($id)
    {
        return $this->employee->find($id);
    }
    public function store(array $data)
    {
        $this->employee->create($data);
    }

    public function update($id, array $data)
    {
        $this->employee->find($id)->update($data);
    }

    public function destroy($id)
    {
        $this->employee->find($id)->delete();
    }
}
