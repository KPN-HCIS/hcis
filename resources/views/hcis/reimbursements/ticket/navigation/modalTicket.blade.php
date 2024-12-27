<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
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

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white" id="detailModalLabel">Detail Information</h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <h6 id="detailTypeHeader" class="mb-3"></h6>
                <div id="detailContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary rounded-pill"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h1 class="modal-title fs-5 text-white" id="bookingModalLabel">Booking Detail - <label id="book_no_tkt" style="font-weight: bold"></label></h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="bookingForm">@csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="booking_code">Booking Code</label>
                            <input name="booking_code" id="booking_code" class="form-control" placeholder="Write Booking Code ..." required>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label" for="booking_price">Ticket Price</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input class="form-control form-control-sm" name="booking_price" id="booking_price" type="text" value="0"
                                    onfocus="this.value = this.value === '0' ? '' : this.value;"
                                    oninput="formatNumber(this)"
                                    onblur="removeFormatting(this)">
                            </div>
                        </div>
                        <input type="hidden" name="book_no_id" id="book_no_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="action_tkt_book" value="Submit" class=" btn btn-primary btn-pill px-4 me-2">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Booking Approval --}}
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="approvalModalLabel">Approval Business Trip Update - {{$transaction->id}}<span
                        id="modalSPPD"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="approveForm" action="{{ '' }}" method="POST">@csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Manager L1 -->
                        <div class="col-md-12 mb-3">
                            <div
                                class="d-flex flex-column align-items-start p-2 mr-2">
                                <label class="col-form-label mb-2 text-dark">Approval Request:</label>

                                <!-- Manager L1 Name & Buttons -->
                                <div class="mb-3 w-100">
                                    <div>
                                        <strong>Manager L1:</strong>
                                        <span id="managerL1Name"></span>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-start" id="l1ActionContainer">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>

                                <!-- Manager L2 Name & Buttons -->
                                <div class="mb-3 w-100">
                                    <div>
                                        <strong>Manager L2:</strong>
                                        <span id="managerL2Name"></span>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-start" id="l2ActionContainer">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary rounded-pill"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Success --}}
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: "Success!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonColor: "#9a2a27",
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                `,
                confirmButtonColor: "#9a2a27",
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent form from submitting immediately

                const transactionId = button.getAttribute('data-id');
                const form = document.getElementById(`deleteForm_${transactionId}`);
                const noSppd = document.getElementById(`no_sppd_${transactionId}`).value;

                Swal.fire({
                    title: `Do you want to delete this request?\n (${noSppd})`,
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#AB2F2B", // Confirm button color
                    cancelButtonColor: "#CCCCCC", // Cancel button color
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Only submit the form if the user confirms
                    }
                });
            });
        });
    });

    function showPendingAlert() {
        Swal.fire({
            title: 'Cannot Add Data!',
            text: 'You still have 2 Outstanding CA Please Check Your Request or Declaration',
            icon: 'warning',
            confirmButtonColor: "#9a2a27",
            confirmButtonText: 'Ok'
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const bookingModal = document.getElementById('bookingModal');
        bookingModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;
            const noId = button.getAttribute('data-no-id');
            const noTkt = button.getAttribute('data-no-tkt');

            // Isi form modal dengan nilai dari tombol
            const bookNoTktLabel = document.getElementById('book_no_tkt');
            const bookNoIdInput = document.getElementById('book_no_id');
            const bookingForm = document.getElementById('bookingForm');

            bookNoTktLabel.textContent = noTkt;
            bookNoIdInput.value = noId;

            // Set action form dengan ID yang dinamis
            bookingForm.action = `{{ url('/ticket/admin/booking') }}/${noId}`;
        });
    });

    const bookingModal = document.getElementById('bookingModal');
    bookingModal.addEventListener('hidden.bs.modal', function () {
        // Mengatur nilai default input kembali seperti semula
        document.getElementById('bookingForm').reset();
        document.getElementById('booking_price').value = '0';
    });

    function formatNumber(element) {
        let num = element.value.replace(/\./g, '');

        element.value = num.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function removeFormatting(element) {
        element.value = element.value.replace(/\./g, '');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const approvalModal = document.getElementById('approvalModal');

        if (approvalModal) {
            approvalModal.addEventListener('show.bs.modal', function (event) {
                // Ambil data dari tombol yang memicu modal
                const button = event.relatedTarget;
                const btId = button.getAttribute('data-id');
                const status = button.getAttribute('data-status');
                const managerL1Name = button.getAttribute('data-manager-l1') || 'Unknown';
                const managerL2Name = button.getAttribute('data-manager-l2') || 'Unknown';

                console.log('Button Data:', { id: btId, status, managerL1Name, managerL2Name });

                // Update modal dengan data manager
                document.getElementById('managerL1Name').textContent = managerL1Name;
                document.getElementById('managerL2Name').textContent = managerL2Name;

                // Kontainer untuk aksi dan data approval
                const l1Container = document.getElementById('l1ActionContainer');
                const l2Container = document.getElementById('l2ActionContainer');

                // Hapus konten sebelumnya
                l1Container.innerHTML = '';
                l2Container.innerHTML = '';

                // Fungsi untuk mengisi kontainer aksi
                function populateContainer(container, status, layer) {
                    if (status === `Pending ${layer}`) {
                        container.innerHTML = `
                            <button type="button" class="btn btn-success btn-sm rounded-pill me-2">Approve</button>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#rejectReasonForm">Reject</button>
                        `;
                    } else {
                        container.innerHTML = `<div id="approvalData${layer}" class="w-100"></div>`;
                    }
                }

                // Isi kontainer aksi untuk L1 dan L2
                populateContainer(l1Container, status, 'L1');
                populateContainer(l2Container, status, 'L2');

                // Ambil data approval dari server (Laravel Blade)
                const approvals = @json($approvalTicket) || [];
                console.log('Approval Data:', approvals);
                console.log('tkt_id:', btId);

                // Filter data approval berdasarkan btId
                const filteredApprovals = approvals.filter(approval => approval.tkt_id === btId);
                console.log('Filtered:', filteredApprovals);
            });
        }
    });
</script>
