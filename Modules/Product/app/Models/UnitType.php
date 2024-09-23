<?php

namespace Modules\Product\app\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;
    protected $table = "unit_types";

    protected $fillable = [
        "name",
        "description",
        "status",
        'ShortName',
        'base_unit',
        'operator',
        'operator_value',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(UnitType::class, 'base_unit', 'id');
    }
}
