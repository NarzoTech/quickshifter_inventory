<?php

namespace Modules\Purchase\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Purchase\Database\factories\PurchaseDetailsFactory;

class PurchaseDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): PurchaseDetailsFactory
    {
        //return PurchaseDetailsFactory::new();
    }
}
