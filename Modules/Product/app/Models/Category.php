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
        'parent_id', 'slug', 'image', 'status', 'top_category',
    ];

    // public function products()
    // {
    //     return $this->hasMany(Product::class, 'category_id', 'id');
    // }

    public function translation(): ?HasOne
    {
        return $this->hasOne(ProductCategoryTranslation::class,'product_category_id','id')->where('lang_code', getSessionLanguage());
    }

    public function getTranslation($code): ?ProductCategoryTranslation
    {
        return $this->hasOne(ProductCategoryTranslation::class)->where('lang_code', $code)->first();
    }

    public function translations(): ?HasMany
    {
        return $this->hasMany(ProductCategoryTranslation::class, 'product_category_id');
    }

    public function getNameAttribute(): ?string
    {
        return $this->translation?->name;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translation?->description;
    }

    public function getImageUrlAttribute(): ?string
    {
        $img = Media::find($this->image)?->path;

        return asset($img);
    }
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
