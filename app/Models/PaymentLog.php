<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable=[
        'user_id',
        'providers',
        'request_payload_masked',
        'response_payload_masked',
        'status',
        'amount',
        'currency',
        'external_ref',
        'error_message',
    ];
    protected $casts=[
        'request_payload_masked'=>'array',
        'response_payload_masked'=>'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
