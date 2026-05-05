<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['event_id','status','expires_at'];

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'reservation_seats');
    }
}