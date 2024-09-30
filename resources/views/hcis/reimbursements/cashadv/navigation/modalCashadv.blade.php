@if (request()->routeIs('cashadvancedDeklarasi'))
    <div class="modal fade" id="modalExtend" tabindex="-1" aria-labelledby="modalExtendLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-center fs-5" id="modalExtendLabel">Extending End Date - <label id="ext_no_ca">3123333123</label></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('cashadvanced.extend') }}">@csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="start">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control bg-light" placeholder="mm/dd/yyyy" readonly>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="end">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control bg-light" placeholder="mm/dd/yyyy" readonly>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="total">Total Days</label>
                                <div class="input-group">
                                    <input class="form-control bg-light" id="totaldays" name="totaldays" type="text" min="0" value="0" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">days</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <p class="text-center mt-2">--<b>Changing too</b>--</p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="new_start">Start Date</label>
                                <input type="date" name="ext_start_date" id="ext_start_date" class="form-control bg-light" placeholder="mm/dd/yyyy" readonly>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="new_end">New End Date</label>
                                <input type="date" name="ext_end_date" id="ext_end_date" class="form-control" placeholder="mm/dd/yyyy" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="new_total">New Total Days</label>
                                <div class="input-group">
                                    <input class="form-control bg-light" id="ext_totaldays" name="ext_totaldays" type="text" min="0" value="0" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">days</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label" for="reason">Reason</label>
                                <textarea name="ext_reason" id="ext_reason" class="form-control" required></textarea>
                            </div>
                            <input type="hidden" name="no_id" id="no_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="action_ca_submit" value="Pending" class="btn btn-primary" id="extendButton">Extending</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@elseif (request()->routeIs('approval.cashadvancedForm'))
    <div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
        <div class="modal-dialog" style="wid">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-center fs-5" id="modalRejectLabel">Rejection Reason - <label id="reject_no_ca"></label></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('approval.cashadvancedApproved',$transactions->id) }}">@csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label" for="reason">Reasons for Refusal</label>
                                <textarea name="reject_info" id="reject_info" class="form-control" required></textarea>
                            </div>
                            <input type="hidden" name="reject_no_id" id="reject_no_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="action_ca_reject" value="Reject" class=" btn btn-primary btn-pill px-4 me-2">Reject</button>
                        {{-- <button type="submit" name="action_ca_submit" value="Pending" class="btn btn-primary" id="rejectButton">Extending</button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
@elseif (request()->routeIs('approval.cashadvancedFormDeklarasi'))
    <div class="modal fade" id="modalRejectDec" tabindex="-1" aria-labelledby="modalRejectDecLabel" aria-hidden="true">
        <div class="modal-dialog" style="wid">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-center fs-5" id="modalRejectDecLabel">Rejection Reason - <label id="rejectDec_no_ca"></label></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('approval.cashadvancedDeclare',$transactions->id) }}">@csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label" for="reason">Reasons for Refusal</label>
                                <textarea name="reject_reason" id="reject_reason" class="form-control" required></textarea>
                            </div>
                            <input type="hidden" name="rejectDec_no_id" id="rejectDec_no_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="action_ca_reject" value="Reject" class=" btn btn-primary btn-pill px-4 me-2">Reject</button>
                        {{-- <button type="submit" name="action_ca_submit" value="Pending" class="btn btn-primary" id="rejectButton">Extending</button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- Table Deklarasi --}}
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: "Success!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonColor: "#9a2a27",
                confirmButtonText: 'Ok'
            });
        });
    </script>
@endif
<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', () => {
            const transactionId = button.getAttribute('data-id');
            const form = document.getElementById(`delete-form-${transactionId}`);

            Swal.fire({
                title: "Do you want to delete this transaction?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0c63e4",
                cancelButtonColor: "#9a2a27",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    function showPendingAlert() {
        Swal.fire({
            title: 'Cannot Add Data!',
            text: 'You still have 2 Pending CA or Some CA still Waiting for Refund',
            icon: 'warning',
            confirmButtonColor: "#9a2a27",
            confirmButtonText: 'Ok'
        });
    }
</script>

{{-- Form Add --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.submit-button').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault(); // Prevent immediate form submission

                const form = document.getElementById('cashadvancedForm');

                // Check if the form is valid before proceeding
                if (!form.checkValidity()) {
                    form.reportValidity(); // Show validation messages if invalid
                    return; // Exit if the form is not valid
                }

                // Retrieve the values from the input fields
                const totalBtPerdiem = document.getElementById('total_bt_perdiem').value;
                const totalBtPenginapan = document.getElementById('total_bt_penginapan').value;
                const totalBtTransport = document.getElementById('total_bt_transport').value;
                const totalBtLainnya = document.getElementById('total_bt_lainnya').value;

                // Create a message with the input values, each on a new line with bold titles
                const inputSummary = `
                    <strong>Total BT Perdiem:</strong> ${totalBtPerdiem}<br>
                    <strong>Total BT Penginapan:</strong> ${totalBtPenginapan}<br>
                    <strong>Total BT Transport:</strong> ${totalBtTransport}<br>
                    <strong>Total BT Lainnya:</strong> ${totalBtLainnya}<br>
                `;

                // Show SweetAlert confirmation with the input summary
                Swal.fire({
                    title: "Do you want to submit this request?",
                    html: `You won't be able to revert this!<br><br>${inputSummary}`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#AB2F2B",
                    cancelButtonColor: "#CCCCCC",
                    confirmButtonText: "Yes, submit it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a hidden input field to hold the action value
                        const input = document.createElement('input');
                        input.type =
                            'hidden'; // Hidden input so it doesn't show in the form
                        input.name = button.name; // Use the button's name attribute
                        input.value = button.value; // Use the button's value attribute

                        form.appendChild(input); // Append the hidden input to the form
                        form.submit(); // Submit the form only if confirmed
                    }
                });
            });
        });
    });
</script>
