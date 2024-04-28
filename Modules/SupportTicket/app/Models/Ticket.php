<?php

namespace Modules\SupportTicket\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $appends = ['unseen_for_user', 'unseen_for_admin'];

    public function getUnseenForUserAttribute()
    {
        return $this->unSeenUserMessage()->count();
    }

    public function getUnseenForAdminAttribute()
    {
        return $this->unSeenAdminMessage()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name', 'email', 'image', 'phone', 'address');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function unSeenUserMessage()
    {
        return $this->hasMany(TicketMessage::class)->where('unseen_user', 0);
    }

    public function unSeenAdminMessage()
    {
        return $this->hasMany(TicketMessage::class)->where('unseen_admin', 0);
    }
}
