<?php

namespace Modules\PageBuilder\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomizeablePage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
    ];

    public function items(): ?HasMany
    {
        return $this->hasMany(CustomizeablePageItem::class)->orderBy('position', 'asc');
    }

    public function activeItems(): ?HasMany
    {
        return $this->hasMany(CustomizeablePageItem::class)->where('status', 1)->orderBy('position', 'asc');
    }
}
