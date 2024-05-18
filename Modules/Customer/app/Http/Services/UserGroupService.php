<?php

namespace Modules\Customer\app\Http\Services;

use Modules\Customer\app\Models\UserGroup;

class UserGroupService
{
    protected $userGroup;

    public function __construct(UserGroup $userGroup)
    {
        $this->userGroup = $userGroup;
    }

    public function getUserGroup()
    {
        $user = $this->userGroup;
        if (request()->keyword) {
            return $this->userGroup->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('discount', 'LIKE', '%' . request()->keyword . '%');
            });
        }
        return $user;
    }

    public function store(array $data): void
    {
        $this->userGroup->create($data);
    }


    public function update(array $data, int $id)
    {
        $this->userGroup->find($id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->userGroup->destroy($id);
    }
}
