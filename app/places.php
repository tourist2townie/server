<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class places extends Model
{
    use Notifiable,HasApiTokens;

    protected $fillable = [
        'place', 'guide_id','guide_name'
    ];
}
