<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserChat extends Model
{
    use Compoships, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'array';
    protected $primaryKey = ['user_id', 'partner_user_id'];

    protected $fillable = [
        'user_id',
        'partner_user_id',
        'message',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_user_id');
    }
}
