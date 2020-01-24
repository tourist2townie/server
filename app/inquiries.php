<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class inquiries extends Model
{
    use Notifiable,HasApiTokens;

    protected $fillable = [
        'tourist_id', 'guide_id', 'reason','description'
    ];
}
