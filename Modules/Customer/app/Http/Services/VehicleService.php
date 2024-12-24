<?php

namespace Modules\Customer\app\Http\Services;

use Modules\Customer\app\Models\Vehicle;

class VehicleService
{
    public function __construct(private Vehicle $vehicle)
    {
    }
    public function store(array $data): void
    {
        $vehicle = $this->vehicle->create($data);
    }

    public function update(array $data, int $id): void
    {
        Vehicle::find($id)->update($data);
    }

    public function destroy(int $id): void
    {
        Vehicle::destroy($id);
    }
}
