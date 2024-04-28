<?php

namespace Modules\PageBuilder\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomizeablePageItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'component_name',
        'position',
        'status',
    ];

    public function component()
    {
        return $this->belongsTo(PageItemComponents::class, 'component_name', 'file');
    }
}
