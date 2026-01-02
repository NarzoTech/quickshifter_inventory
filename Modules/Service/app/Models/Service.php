<?php

namespace Modules\Service\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Service\Database\factories\ServiceFactory;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'image',
        'description',
        'status',
    ];

    protected $appends = ['singleImage'];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function getSingleImageAttribute()
    {
        $image = $this->image;

        // Skip invalid paths (temp files, null, empty)
        if (!$image || str_starts_with($image, '/tmp/') || str_starts_with($image, 'C:\\')) {
            return asset('backend/img/service.png');
        }

        if (file_exists(public_path($image))) {
            return asset($image);
        }

        return asset('backend/img/service.png');
    }
}
