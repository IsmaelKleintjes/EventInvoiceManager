<div class="form-group">
    <label for="title">Title:</label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
           value="{{ isset($oEvent) ? $oEvent->title : old('title') }}" required>
    @error('title')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

<div class="form-group">
    <label for="start_date">Start Date and Time:</label>
    <input type="text" class="form-control datetimepicker @error('start_date') is-invalid @enderror" id="start_date"
           name="start_date" value="{{ isset($oEvent) ? $oEvent->start_date->format('Y-m-d H:i') : old('start_date') }}"
           required>
    @error('start_date')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>

<div class="form-group">
    <label for="end_date">End Date and Time:</label>
    <input type="text" class="form-control datetimepicker @error('end_date') is-invalid @enderror" id="end_date"
           name="end_date" value="{{ isset($oEvent) ? $oEvent->end_date->format('Y-m-d H:i') : old('end_date') }}"
           required>
    @error('end_date')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>

<div class="form-group">
    <label for="customer_id">Customer:</label>
    <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
        @foreach($ooCustomers as $oCustomer)
            <option
                value="{{ $oCustomer->id }}" {{ isset($oEvent) && $oEvent->customer_id == $oCustomer->id ? 'selected' : '' }}>
                {{ $oCustomer->name }} ({{ $oCustomer->email }})
            </option>
        @endforeach
    </select>
    @error('customer_id')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>

<div class="form-group" id="service-descriptions">
    <label for="services">Services:</label>
    @if(isset($oEvent->services))
        @foreach($oEvent->services as $key => $oEventHasService)
            @if($oEventHasService->description == null)
                @continue
            @endif
            <div class="row mb-3 service-row">
                <div class="col-md-6">
                    <label for="service">Service:</label>
                    <select class="form-control" name="services[{{$oEventHasService->id}}]" required>
                        @foreach($ooServices as $oService)
                            <option value="{{ $oService->id }}" {{ $oEventHasService->service_id == $oService->id ? 'selected' : '' }}>{{ $oService->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="description">Description:</label>
                    <textarea class="form-control" name="descriptions[{{$oEventHasService->id}}]" rows="3">{{ isset($oEventHasService->description) ? $oEventHasService->description : '' }}</textarea>
                    <div class="remove-service-row">Remove row</div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="form-group">
    <button type="button" class="btn btn-primary" id="add-service-row">Add Service</button>
</div>

<div class="d-none" id="service-row-template">
    <div class="row mb-3 service-row">
        <div class="col-md-6">
            <label for="service">Service:</label>
            <select  class="form-control" name="new_services[]">
                @foreach($ooServices as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="description">Description:</label>
            <textarea class="form-control" name="new_descriptions[]" rows="3"></textarea>
            <div class="remove-service-row">Remove row</div>
        </div>
    </div>
</div>

<div class="form-group">
    <label>Invoice will be sent:</label>
    <div class="row">
        <div class="col-md-4">
            <select class="form-control" id="invoice_interval" name="invoice_interval">
                @for ($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}" {{ isset($oEvent) && $oEvent->invoice_interval == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-control" id="invoice_unit" name="invoice_unit">
                <option value="hour" {{ isset($oEvent) && $oEvent->invoice_unit == 'hour' ? 'selected' : '' }}>Hour(s)</option>
                <option value="day" {{ isset($oEvent) && $oEvent->invoice_unit == 'day' ? 'selected' : '' }}>Day(s)</option>
                <option value="week" {{ isset($oEvent) && $oEvent->invoice_unit == 'week' ? 'selected' : '' }}>Week(s)</option>
                <option value="month" {{ isset($oEvent) && $oEvent->invoice_unit == 'month' ? 'selected' : '' }}>Month(s)</option>
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-control" id="invoice_timing" name="invoice_timing">
                <option value="1" {{ isset($oEvent) && $oEvent->invoice_timing == 1 ? 'selected' : '' }}>Before</option>
                <option value="2" {{ isset($oEvent) && $oEvent->invoice_timing == 2 ? 'selected' : '' }}>After</option>
            </select>
        </div>
    </div>
    <label>event.</label>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">{{ isset($oEvent) ? 'Update' : 'Create' }}</button>
</div>
