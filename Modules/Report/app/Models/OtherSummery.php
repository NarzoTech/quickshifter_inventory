<?php

namespace Modules\Report\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Report\Database\factories\OtherSummeryFactory;
use Modules\Supplier\app\Models\Supplier;

class OtherSummery extends Model
{
    use HasFactory;

    protected $table = 'other_summeries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'supplier_id',
        'date',
        'description',
        'memo_number',
        'amount',
        'paid',
        'due',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id')->withDefault(['name' => 'Guest']);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault(['name' => 'Guest']);
    }
}
