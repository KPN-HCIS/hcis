<x-app-layout>
    @section('title', 'Schedule')
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-center">
            <div class="card col-md-8">
                <div class="card-header bg-white justify-content-start">
                    <div class="form-inline text-lg mr-4">
                        <a href="{{ route('schedules') }}" class="close mr-3">
                            <span aria-hidden="true">&times;</span>
                        </a>
                        <span class="modal-title" id="viewFormEmployeeLabel">Schedule</span>
                    </div>
                    <div class="input-group-md">
                        <input type="text" id="employee_name" class="form-control" placeholder="Search employee.." hidden>
                    </div>
                </div>
                <div class="card-body" @style('overflow-y: auto;')>
                    <div class="container-fluid py-3">
                        <form action="" method="post">
                            <div class="row my-2">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="name">Schedule Name</label>
                                        <input type="text" class="form-control bg-light" placeholder="Enter name.." id="name">
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="name">Employee Type</label>
                                        <input type="text" class="form-control bg-light" placeholder="Select some options.." id="name">
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="start">Start Date</label>
                                        <input type="date" class="form-control bg-light" id="start" placeholder="mm/dd/yyyy">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="end">End Date</label>
                                        <input type="date" class="form-control bg-light" id="end" placeholder="mm/dd/yyyy">
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkbox_reminder">
                                            <label class="custom-control-label" for="checkbox_reminder">Reminder</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="reminders" hidden>
                                <div class="row">
                                    <div class="col-md">
                                        <label for="repeatDays">Repeat On</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-group w-75" id="repeatDays" role="group" aria-label="Repeat Days">
                                            <button type="button" class="btn btn-outline-primary btn-sm day-button">Mon</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm day-button">Tue</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm day-button">Wed</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm day-button">Thu</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm day-button">Fri</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm day-button">Sat</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm day-button">Sun</button>
                                            <button type="button" class="btn btn-primary btn-sm" id="select-all">Select All</button>
                                        </div>          
                                    </div>
                                </div>
                                <div class="row my-4">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="messages">Messages</label>
                                            <textarea name="messages" id="messages" rows="5" class="form-control bg-light" placeholder="Enter message.."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md d-sm-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary shadow px-4">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </x-slot>
</x-app-layout>