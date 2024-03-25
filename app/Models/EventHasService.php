<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventHasService extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'service_id',
        'description'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
