<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class packages extends Model
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'province', 'guide_id', 'tourist_id', 'date', 'days', 'category'
    ];
}
