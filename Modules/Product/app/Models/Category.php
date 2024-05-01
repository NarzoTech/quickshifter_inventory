<?php

namespace Modules\Product\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Media\app\Models\Media;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_id',  'status', 'name'
    ];

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    // Define the relationship with Product through ProductCategory
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

}
