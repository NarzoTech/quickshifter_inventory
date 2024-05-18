<?php

namespace Modules\Customer\app\Http\Services;

use Modules\Customer\app\Models\CustomerGroup;

class CustomerGroupService
{
    protected $customerGroup;

    public function __construct(CustomerGroup $customerGroup)
    {
        $this->customerGroup = $customerGroup;
    }

    public function getCustomerGroup()
    {
        $customer = $this->customerGroup;
        if (request()->keyword) {
            return $this->customerGroup->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('discount', 'LIKE', '%' . request()->keyword . '%');
            });
        }
        return $customer;
    }

    public function store(array $data): void
    {
        $this->customerGroup->create($data);
    }


    public function update(array $data, int $id)
    {
        $this->customerGroup->find($id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->customerGroup->destroy($id);
    }
}
