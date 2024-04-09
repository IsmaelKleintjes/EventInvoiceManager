<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<button type="button" class="btn btn-info btn-lg" id="newEventButton" data-toggle="modal" data-target="#formModal">Create event</button>

<div id='calendar'></div>

<div class="modal fade" id="formModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div id="form-html"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="eventDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="eventDetailsTitle" class="modal-title">Event Details</h2>
        <p id="eventDetails" class="modal-description"></p>
        <form id="editEventForm">
            <input type="hidden" id="editEventId" name="editEventId" value="">
            <button type="button" class="btn btn-info btn-lg modal-button" id="editEventButton" data-toggle="modal" data-target="#formModal">Edit Event</button>
        </form>
        <form id="deleteEventForm">
            <input type="hidden" id="deleteEventId" name="deleteEventId" value="">
            <button type="button" id="deleteEventButton" class="btn btn-danger btn-lg modal-button">Delete Event</button>
        </form>
    </div>
</div>


<script>
    const csrfToken = '{{ csrf_token() }}';

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth'
            },
            editable: true, // Enable draggable events
            events: '/api/events/fullcalendar',
            eventDrop: function(eventDropInfo) {
                updateEventPosition(eventDropInfo.event);
            },
            eventResize: function(eventResizeInfo) {
                updateEventPosition(eventResizeInfo.event);
            }
        });
        calendar.render();

        calendar.on('eventClick', function(info) {
            fetch('/api/events/' + info.event.id)
                .then(response => response.json())
                .then(event => {
                    // Display event details including start time and end time
                    var eventDetailsHtml = '<strong>Title:</strong> ' + event.title + '<br>' +
                        '<strong>Start Time:</strong> ' + new Date(event.start_date).toLocaleString() + '<br>' +
                        '<strong>End Time:</strong> ' + new Date(event.end_date).toLocaleString() + '<br>';

                    // console.log(event.allServices)
                    // // Loop over services and descriptions
                    // if (event.allServices.length > 0) {
                    //     eventDetailsHtml += '<strong>Services:</strong><br>';
                    //     for (var i = 0; i < event.allServices.length; i++) {
                    //         eventDetailsHtml += '<strong>Service:</strong> ' + event.allServices[i].name + '<br>';
                    //         eventDetailsHtml += '<strong>Description:</strong> ' + (event.descriptions[i] ? event.descriptions[i] : 'N/A') + '<br>';
                    //     }
                    // } else {
                    //     eventDetailsHtml += '<strong>No services added</strong>';
                    // }

                    // Set event details in the modal
                    $('#eventDetails').html(eventDetailsHtml);

                    // Set event ID for editing and deleting
                    $('#editEventId').val(event.id);
                    $('#deleteEventId').val(event.id);
                    $('#eventDetailsTitle').text('Event Details');

                    // Show the modal
                    $('#eventDetailsModal').show();
                })
                .catch(error => {
                    console.error('Error fetching event details:', error);
                });
        });


        // Initialize Flatpickr date and time pickers
        flatpickr('#start_date', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i', // Format: Year-Month-Day Hour:Minute
        });

        flatpickr('#end_date', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i', // Format: Year-Month-Day Hour:Minute
        });

        $('.close').on('click', function() {
            $('#eventDetailsModal').hide();
        });

        $(document).on('click', function(event) {
            if (event.target && event.target.id === 'add-service-row') {
                addServiceRow();
            }

            if (event.target && event.target.id === 'remove-service-row') {
                $(this).closest('.service-row').remove();
            }

            if (event.target && event.target.id === 'form') {
                $('#service-row-template').remove();
            }

            if ($(event.target).closest('.modal-content').length === 0) {
                $('#eventDetailsModal').hide();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-service-row')) {
                    event.target.closest('.service-row').remove();
                }
            });
        });

        $('#newEventButton').on('click', function() {
            loadEventCreateForm();
        });

        $('#editEventButton').on('click', function() {
            let iEventId = $('#editEventId').val();
            loadEventEditForm(iEventId);
        });

        $('#deleteEventButton').on('click', function() {
            var eventId = $('#deleteEventId').val();
            deleteEvent(eventId);
        });

        $('#service-descriptions').on('click', '.remove-service-row', function() {
            $(this).closest('.service-row').remove();
        });
    });

    function updateEventPosition(event) {
        const eventId = event.id;
        const url = `/events/${eventId}/move`;
        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
            },
            body: JSON.stringify({
                id: event.id,
                start: event.start,
                end: event.end
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to update event');
                }
            })
            .catch(error => {
                // Handle error
                console.error('Error updating event:', error);
            });
    }

    function loadEventCreateForm() {
        fetch('{{route('events.create.form')}}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load event form.');
                }
                return response.json(); // Parse JSON response
            })
            .then(data => {
                if (data.success && data.results) {
                    // Set innerHTML with HTML string
                    $('#form-html').html(data.results);
                    initFlatpickr();
                } else {
                    throw new Error('Failed to load event form.'); // Handle error case
                }
            })
            .catch(error => {
                console.error(error.message);
            });
    }

    function loadEventEditForm(iEventId) {
        fetch('/events/' + iEventId + '/edit')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load event form.');
                }
                return response.json(); // Parse JSON response
            })
            .then(data => {
                if (data.success && data.results) {
                    // Set innerHTML with HTML string
                    $('#form-html').html(data.results);
                    initFlatpickr();
                    $('#eventDetailsModal').hide();

                } else {
                    throw new Error('Failed to load event form.'); // Handle error case
                }
            })
            .catch(error => {
                console.error(error.message);
            });
    }

    function deleteEvent(eventId){
        if (confirm("Are you sure you want to delete this event?")) {
            fetch('/events/' + eventId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Failed to delete event.');
                }
                console.log('Event deleted successfully');

                location.reload();

                var calendar = $('#calendar').fullCalendar('getCalendar');
                var deletedEvent = calendar.getEventById(eventId);
                if (deletedEvent) {
                    deletedEvent.remove();
                }

                $('#eventDetailsModal').hide();
            })
                .catch(error => {
                    console.error('Error deleting event:', error);
                });
        }
    }

    function addServiceRow() {
        var $newRow = $('#service-row-template .service-row').clone().removeClass('d-none');
        $('#service-descriptions').append($newRow);
    }

    function initFlatpickr() {
        flatpickr('.datetimepicker', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i', // Format: Year-Month-Day Hour:Minute
        });
    }
</script>

<style>
    #calendar {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* New Modal */
    #eventDetailsModal {
        display: none;
        position: fixed;
        z-index: 1050; /* Ensure it appears above Bootstrap modals */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    /* Modal Content */
    #eventDetailsModal .modal-content {
        position: relative;
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 40%;
    }

    /* Close Button */
    #eventDetailsModal .close {
        color: #aaa;
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 28px;
    }

    #eventDetailsModal .close:hover,
    #eventDetailsModal .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Modal Title */
    #eventDetailsModal .modal-title {
        text-align: center;
    }

    /* Modal Description */
    #eventDetailsModal .modal-description {
        text-align: center;
    }

    /* Modal Button */
    #eventDetailsModal .modal-button {
        display: block;
        margin: 0 auto;
        margin-top: 10px;
    }

</style>
</x-app-layout>