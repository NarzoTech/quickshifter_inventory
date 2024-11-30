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
            $user =  $user->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('discount', 'LIKE', '%' . request()->keyword . '%');
            });
        }
        if (request()->order_by) {
            $user = $user->orderBy('id', request()->order_by == 1 ? 'asc' : 'desc');
        }
        return $user;
    }

    public function store(array $data): void
    {
        $this->userGroup->create($data);
    }


    public function update(array $data, int $id): void
    {
        $this->userGroup->find($id)->update($data);
    }

    public function destroy(int $id)
    {
        return $this->userGroup->destroy($id);
    }
}
