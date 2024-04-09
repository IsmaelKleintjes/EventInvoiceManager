    <form class="form" data-parsley-validate action="/events/{{$oEvent->id}}" method="POST" id="submitByActionForm">
        @method('PUT')
        @csrf
        @include('calendar._form', ['oEvent' => $oEvent])
    </form>
