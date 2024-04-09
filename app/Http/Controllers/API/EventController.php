<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function getById(int $iId)
    {
        return json_encode(Event::find($iId));
    }

    public function getAll()
    {
        return json_encode(Event::all());
    }

    public function getAllForFullCalendar()
    {
        return Event::all()->map(function($oEvent) {
            return [
                'id' => $oEvent->id,
                'title' => $oEvent->title,
                'start' => $oEvent->start_date,
                'end' => $oEvent->end_date,
            ];
        });
    }
}
