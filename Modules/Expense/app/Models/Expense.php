<?php
namespace Modules\Expense\app\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounts\app\Models\Account;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'date',
        'payment_type',
        'account_id',
        'account_details',
        'payment_status',
        'note',
        'amount',
        'expense_type_id',
        'sub_expense_type_id',
        'created_by',
        'updated_by',
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id')->withDefault();
    }

    public function subExpenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'sub_expense_type_id')->withDefault();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }
}
