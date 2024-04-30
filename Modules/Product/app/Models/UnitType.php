<?php

namespace Modules\Product\app\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;
    protected $table = "unit_types";

    protected $fillable = ["name", "description", "status", "created_by", "updated_by"];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_type_id', 'id');
    }
}
