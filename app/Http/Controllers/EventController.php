<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function store(Request $request)
    {
        $oEvent = new Event();

        return $this->_save($oEvent, $request);
    }

    public function update(Request $request, int $iId)
    {
        $oEvent = Event::findOrFail($iId);

        return $this->_save($oEvent, $request);
    }

    public function destroy($id)
    {
        $oEvent = Event::findOrFail($id);
        $oEvent->delete();

        return view('calendar.index')->with(['message' => 'Event deleted successfully']);
    }

    public function move(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $oEvent = Event::findOrFail($request->id);
        $oEvent->start_date = $request->start;
        $oEvent->end_date = $request->end;
        $oEvent->save();

        return response()->json(['message' => 'Event moved successfully']);
    }

    private function _save(Event $oEvent, Request $request)
    {
        $oValidator = $oEvent->validate();

        if ($oValidator->fails()) {
            return view('calendar.index')->with(['errors' => $oValidator->errors()], 422);
        }

        if (!$oEvent->saveEvent($oEvent)) {
            return view('calendar.index')->with(['errors' => 'Failed to save the event.'], 500);
        }

        return view('calendar.index');
    }

    public function __create()
    {
        return Event::getCreateFormHtml();
    }

    public function __edit($iId)
    {
        return Event::getEditFormHtml($iId);
    }
}
