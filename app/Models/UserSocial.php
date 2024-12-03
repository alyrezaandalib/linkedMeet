<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    use Compoships;

    protected $primaryKey = ['user_id', 'social_network_id'];
    public $incrementing = false;

    protected $fillable = [
        "user_id",
        "social_network_id",
        "social_id",
        "access_token",
    ];
}
