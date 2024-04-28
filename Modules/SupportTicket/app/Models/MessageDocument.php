<?php

namespace Modules\SupportTicket\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\SupportTicket\Database\factories\MessageDocumentFactory;

class MessageDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): MessageDocumentFactory
    {
        //return MessageDocumentFactory::new();
    }
}
