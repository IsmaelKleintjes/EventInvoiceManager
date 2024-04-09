<form class="form" data-parsley-validate action="/events" method="POST" id="submitByActionForm">
    @method('POST')
    @csrf
    @include('calendar._form', ['ooCustomers' => $ooCustomers])
</form>
