<?php

namespace Modules\Service\app\Services;

use Illuminate\Http\Request;
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

    public function store(Request $request): void
    {
        $filename = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = file_upload($image);
        }

        $this->service->create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $filename,
            'price' => $request->price,
            'status' => $request->status,
            'category_id' => $request->category_id
        ]);
    }

    public function update(int $id, Request $request)
    {
        $service = $this->service->find($id);
        $filename = $service->image;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = file_upload($image, oldFile: $service->image);
        }
        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $filename,
            'price' => $request->price,
            'status' => $request->status,
            'category_id' => $request->category_id
        ]);
    }

    public function destroy(int $id)
    {
        $service = $this->service->find($id);
        if ($service->image) {
            delete_file($service->image);
        }
        $service->delete();
    }
}
