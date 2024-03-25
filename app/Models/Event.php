<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'service_id',
        'customer_id',
        'invoice_interval',
        'invoice_unit',
        'invoice_timing',
        'date_time_of_sending_invoice',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'date_time_of_sending_invoice' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function services()
    {
        return $this->hasMany(EventHasService::class);
    }

    public function validate()
    {
        return Validator::make(Request()->all(), [
            'title' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'services' => 'required|array|at_least_one_service',
            'descriptions' => 'array',
            'invoice_interval' => 'required|integer',
            'invoice_unit' => 'required|string|in:hour,day,week,month',
            'invoice_timing' => 'required|integer|in:1,2',
        ]);
    }

    public function saveEvent($oEvent)
    {
        $request = request();

        $oEvent->title = $request->title;
        $oEvent->start_date = $request->start_date;
        $oEvent->end_date = $request->end_date;
        $oEvent->customer_id = $request->customer_id;
        $oEvent->invoice_interval = $request->invoice_interval;
        $oEvent->invoice_unit = $request->invoice_unit;
        $oEvent->invoice_timing = $request->invoice_timing;
        $oEvent->date_time_of_sending_invoice = $this->calculateTimeOfSendingInvoice($oEvent);
        $oEvent->save();

        $aEventHasServiceIds = array_keys($request->services ?? []);

        foreach ($oEvent->services as $eventHasService) {
            $key = in_array($eventHasService->id, $aEventHasServiceIds);
            if ($key) {
                $eventHasService->event_id = $oEvent->id;
                $eventHasService->service_id = $request->services[$eventHasService->id];
                $eventHasService->description = $request->descriptions[$eventHasService->id];
                $eventHasService->save();
            } else {
                $eventHasService->delete();
            }
        }

        foreach ($request->new_services as $key => $serviceId) {
            EventHasService::create([
                'event_id' => $oEvent->id,
                'service_id' => $serviceId,
                'description' => $request->new_descriptions[$key],
            ]);
        }

        $invoiceSendingDate = $request->invoice_timing == 1 ?
            Carbon::parse($oEvent->start_date)->sub($request->invoice_interval, $request->invoice_unit) :
            Carbon::parse($oEvent->end_date)->add($request->invoice_interval, $request->invoice_unit);

        $oEvent->update(['date_time_of_sending_invoice' => $invoiceSendingDate]);

        return $oEvent;
    }

    public static function getCreateFormHtml()
    {
        $ooCustomers = Customer::all();
        $ooServices = Service::all();

        $resultHtml = view('calendar.create', [
            'ooCustomers' => $ooCustomers,
            'ooServices' => $ooServices
        ])->render();

        return response()->json(['success' => true, 'results' => $resultHtml]);
    }

    public static function getEditFormHtml($iId)
    {
        $oEvent = Event::find($iId);

        $ooCustomers = Customer::all();
        $ooServices = Service::all();

        $resultHtml = view('calendar.edit')->with([
            'oEvent' => $oEvent,
            'ooCustomers' => $ooCustomers,
            'ooServices' => $ooServices
        ])->render();

        return response()->json(['success' => true, 'results' => $resultHtml]);
    }

    private function calculateTimeOfSendingInvoice($oEvent)
    {
        $startDate = $oEvent->start_date;
        $endDate = $oEvent->end_date;

        $interval = $oEvent->invoice_interval;
        $intervalType = match ($oEvent->invoice_unit) {
            'hour' => 'hours',
            'week' => 'weeks',
            'month' => 'months',
            default => 'days',
        };

        if ($oEvent->invoice_timing == 1) {
            $sendingDateTime = $startDate->sub($interval, $intervalType);
        } else {
            $sendingDateTime = $endDate->add($interval, $intervalType);
        }

        return $sendingDateTime;
    }
}
