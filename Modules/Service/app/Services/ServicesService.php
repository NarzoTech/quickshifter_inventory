<?php

namespace Modules\Service\app\Services;

use Modules\Service\app\Models\Service;


class ServicesService
{
    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function all()
    {
        $service = $this->service;

        if (request()->keyword) {
            $service = $service->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request()->keyword . '%');
            });
        }

        if (request()->order_by) {
            $service = $service->orderBy('id', request()->order_by == 1 ? 'asc' : 'desc');
        }
        return $service;
    }

    public function store(array $data): void
    {
        $this->service->create($data);
    }
}
