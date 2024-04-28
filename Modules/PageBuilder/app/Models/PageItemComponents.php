<?php

namespace Modules\PageBuilder\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageItemComponents extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file',
    ];
}
