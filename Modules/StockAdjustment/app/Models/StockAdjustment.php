<?php

namespace Modules\StockAdjustment\app\Models;

use App\Models\Admin;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Expense\app\Models\Expense;
use Modules\Product\app\Models\Product;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice',
        'product_id',
        'quantity',
        'reason',
        'date',
        'note',
        'unit_cost',
        'total_loss',
        'expense_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_loss' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault();
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id')->withDefault();
    }

    public function stockRecord()
    {
        return $this->hasOne(Stock::class, 'stock_adjustment_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by')->withDefault();
    }

    public function getReasonLabelAttribute()
    {
        return ucfirst($this->reason);
    }
}
