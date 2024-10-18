@extends('layouts_.vertical', ['page_title' => 'Medical'])

@section('css')
    <style>
        th {
            color: white !important;
            text-align: center;
        }

        table {
            white-space: nowrap;
            width: 100%;
        }

        tr.sticky {
            position: sticky;
            top: 0;
            z-index: 1;
            background: var(--stickyBackground);
        }

        th.sticky,
        td.sticky {
            position: sticky;
            left: 0;
            background: var(--stickyBackground);
        }

        table.dataTable>tbody>tr.child ul.dtr-details {
            width: 100%;
            vertical-align: middle !important;
        }

        table.dataTable>tbody>tr.child ul.dtr-details>li {
            display: flex;
            align-items: center !important;
        }

        table.dataTable>tbody>tr.child span.dtr-title {
            min-width: 120px !important;
            max-width: 120px !important;
            text-wrap: wrap !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Breadcrumb Section -->
            <div class="col-md-6 d-flex align-items-center">
                <ol class="breadcrumb mb-0" style="align-items: center; padding-left: 0;">
                    <li class="breadcrumb-item" style="font-size: 18px;">
                        <a href="/reimbursements">
                            {{ $parentLink }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ $link }}
                    </li>
                </ol>
            </div>
            @include('hcis.reimbursements.businessTrip.modal')

            <!-- Button Section -->
            <div class="col-md-6 d-flex justify-content-center justify-content-md-end align-items-center">
                <a href="{{ route('export.excel') }}" class="btn btn-outline-success rounded-pill btn-action me-1">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> Export to Excel
                </a>
                <a href="{{ route('medical-form.add') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-plus-circle"></i> Add Medical
                </a>
            </div>
        </div>
        <div class="row">
            <div class="card shadow-none p-1 py-3">
                <div class="d-flex justify-content-center">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                                aria-selected="true">History</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                                aria-selected="false">Plafon Medical</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact"
                                aria-selected="false">Family Data</button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        @include('hcis.reimbursements.medical.historyMedical')
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        @include('hcis.reimbursements.medical.plafonMedical')
                    </div>
                    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                        @include('hcis.reimbursements.medical.familyData')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="rejectReasonModalLabel">Rejection Information</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Rejected by</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="rejectedBy"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <strong>Rejection reason</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="rejectionReason"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <strong>Rejection date</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="rejectionDate"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary rounded-pill"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/js/medical/medical.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'), {
                keyboard: true,
                backdrop: 'static'
            });

            const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    rejectModal.hide();
                });
            });

            // Commenting out the formatDate function as it may not be needed for rejection info
            /*
            function formatDate(dateTimeString) {
                var date = new Date(dateTimeString);
                var day = ('0' + date.getDate()).slice(-2);
                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                var year = date.getFullYear();
                var hours = ('0' + date.getHours()).slice(-2);
                var minutes = ('0' + date.getMinutes()).slice(-2);
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            }
            */

            window.showRejectInfo = function(transactionId) {
                var rejectMedic = {!! json_encode($rejectMedic) !!}; // Adjusted variable for rejected medical records
                var employeeName = {!! json_encode($employeeName) !!}; // Add this line

                // Find the rejected medical record for the given transaction ID (no_medic)
                var rejectionInfo = rejectMedic[transactionId]; // Access directly with key
                if (rejectionInfo) {
                    var rejectedBy = employeeName[rejectionInfo.employee_id] || 'N/A'; // Retrieve fullname
                    document.getElementById('rejectedBy').textContent = ': ' +
                    rejectedBy; // Update rejected by text
                    document.getElementById('rejectionReason').textContent = ': ' + (rejectionInfo
                        .reject_info || 'N/A'); // Update rejection reason
                    // Commenting out rejection date display as it may not be needed
                    /*
                    var rejectionDate = rejectionInfo.date ? formatDate(rejectionInfo.date) : 'N/A'; // Use date from the record
                    document.getElementById('rejectionDate').textContent = ': ' + rejectionDate; // Update rejection date
                    */
                    rejectModal.show();
                } else {
                    console.error('Rejection information not found for transaction ID:', transactionId);
                }
            };

            // Commenting out the modal hidden event listener as it may not be needed
            /*
            document.getElementById('rejectReasonModal').addEventListener('hidden.bs.modal', function() {
                // console.log('Modal closed');
            });
            */
        });
    </script>

    </script>
@endsection
