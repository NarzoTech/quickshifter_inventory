<?php

namespace Modules\Product\app\Services;

use Modules\Product\app\Models\UnitType;

class UnitTypeService
{

    public function getAll()
    {
        return UnitType::latest()->get();
    }

    public function getActiveAll()
    {
        return UnitType::latest()->where('status', 1)->get();
    }

    public function save($data)
    {
        $unit_type = new UnitType();
        $unit_type->name = $data['name'];
        $unit_type->description = $data['description'];
        $unit_type->status = $data['status'];
        $unit_type->save();
        return true;
    }

    public function update($data, $id)
    {
        $unit_type = UnitType::findOrFail($id);
        $unit_type->name = $data['name'];
        $unit_type->description = $data['description'];
        $unit_type->status = $data['status'];
        $unit_type->save();
        return true;
    }

    public function findById($id)
    {
        return UnitType::findOrFail($id);
    }

    public function delete($id)
    {
        $unit_type = UnitType::findOrFail($id);
        if (count($unit_type->products) > 0) {
            return "not_possible";
        }
        $unit_type->delete();

        return "possible";
    }
}
