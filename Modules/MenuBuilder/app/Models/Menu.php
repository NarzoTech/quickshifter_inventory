<?php

namespace Modules\MenuBuilder\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'route',
        'status',
        'order',
        'parent_id',
    ];

    protected $appends = ['title'];

    public function getTitleAttribute()
    {
        if ($code = request('code')) {
            return $this->getTranslation($code)?->title;
        }

        return $this->translation?->title;
    }

    public function getSubMenusAttribute()
    {
        return $this->where('parent_id', $this->id)->orderBy('order', 'asc')->get();
    }

    public function translation(): ?HasOne
    {
        return $this->hasOne(MenuTranslation::class)->where('lang_code', getSessionLanguage());
    }

    public function getTranslation($code): ?MenuTranslation
    {
        return $this->hasOne(MenuTranslation::class)->where('lang_code', $code)->first();
    }

    public function translations(): ?HasMany
    {
        return $this->hasMany(MenuTranslation::class, 'menu_id');
    }
}
