@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Data Declaration (Admin)</h4>
                        <a href="{{ route('businessTrip.admin') }}" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/deklarasi/admin/status/{{ $n->id }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="no_sppd" class="form-label">No SPPD</label>
                                <input type="text" class="form-control bg-light" id="no_sppd" name="no_sppd"
                                    value="{{ $n->no_sppd }}" readonly>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="mulai" class="form-label">Start Date</label>
                                    <input type="date" class="form-control bg-light" id="mulai" name="mulai"
                                        value="{{ $n->mulai }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="kembali" class="form-label">End Date</label>
                                    <input type="date" class="form-control bg-light" id="kembali" name="kembali"
                                        value="{{ $n->kembali }}" readonly>
                                </div>
                            </div>

                            <!-- View-only Table -->
                            <div class="mb-2">
                                <h5 class="mb-2">Estimated Down Payment</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Summary</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Uang Saku</td>
                                                <td>2 Hari</td>
                                                <td>Rp. 80,000</td>
                                                <td>Rp. 160,000</td>
                                            </tr>
                                            <tr>
                                                <td>Transportation</td>
                                                <td>2 Hari</td>
                                                <td>Rp. 80,000</td>
                                                <td>Rp. 160,000</td>
                                            </tr>
                                            <tr>
                                                <td>Uang Makan</td>
                                                <td>2 Hari</td>
                                                <td>Rp. 80,000</td>
                                                <td>Rp. 160,000</td>
                                            </tr>
                                            <tr>
                                                <td>Penginapan</td>
                                                <td>2 Hari</td>
                                                <td>Rp. 80,000</td>
                                                <td>Rp. 160,000</td>
                                            </tr>
                                            <tr>
                                                <td>Entertainment</td>
                                                <td>2 Hari</td>
                                                <td>Rp. 80,000</td>
                                                <td>Rp. 160,000</td>
                                            </tr>
                                            <tr>
                                                <td>Others</td>
                                                <td>2 Hari</td>
                                                <td>Rp. 80,000</td>
                                                <td>Rp. 160,000</td>
                                            </tr>
                                            <!-- Add more rows as needed -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3">Total Down Payment</th>
                                                <th>Rp. 160,000</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Input Table -->
                            <div class="mb-2">
                                <h5 class="mb-2">Realization Money (Declaration)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Hari</th>
                                                <th>Harga</th>
                                                <th>Total</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Uang Saku</td>
                                                <td><input type="number" class="form-control" name="uang_saku_jumlah"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_harga"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_total"
                                                        readonly></td>
                                                <td><input type="text" class="form-control" name="uang_saku_keterangan">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Transportation</td>
                                                <td><input type="number" class="form-control" name="uang_saku_jumlah"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_harga"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_total"
                                                        readonly></td>
                                                <td><input type="text" class="form-control" name="uang_saku_keterangan">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Uang Makan</td>
                                                <td><input type="number" class="form-control" name="uang_saku_jumlah"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_harga"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_total"
                                                        readonly></td>
                                                <td><input type="text" class="form-control" name="uang_saku_keterangan">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Penginapan</td>
                                                <td><input type="number" class="form-control" name="uang_saku_jumlah"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_harga"></td>
                                                <td><input type="number" class="form-control" name="uang_saku_total"
                                                        readonly></td>
                                                <td><input type="text" class="form-control" name="uang_saku_keterangan">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Entertainment</td>
                                                <td><input type="number" class="form-control" name="uang_saku_jumlah">
                                                </td>
                                                <td><input type="number" class="form-control" name="uang_saku_harga">
                                                </td>
                                                <td><input type="number" class="form-control" name="uang_saku_total"
                                                        readonly></td>
                                                <td><input type="text" class="form-control"
                                                        name="uang_saku_keterangan"></td>
                                            </tr>
                                            <tr>
                                                <td>Others</td>
                                                <td><input type="number" class="form-control" name="uang_saku_jumlah">
                                                </td>
                                                <td><input type="number" class="form-control" name="uang_saku_harga">
                                                </td>
                                                <td><input type="number" class="form-control" name="uang_saku_total"
                                                        readonly></td>
                                                <td><input type="text" class="form-control"
                                                        name="uang_saku_keterangan"></td>
                                            </tr>
                                            <!-- Add more rows for other items -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3">Total Realized Money</th>
                                                <th><input type="number" class="form-control" name="total_realisasi"
                                                        readonly></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-3 mb-2">
                                <label for="uploaded-file" class="form-label">Uploaded Proof</label>
                                <div id="uploaded-file">
                                    <!-- Message when no proof is submitted -->
                                    <p id="no-proof-message" style="color: red;">No proof has been uploaded.</p>

                                    <!-- If the uploaded file is an image -->
                                    <img id="uploaded-image" src="#" alt="Uploaded Image"
                                        style="max-width: 100%; display: none;">

                                    <!-- If the uploaded file is a PDF -->
                                    <iframe id="uploaded-pdf" src="#"
                                        style="width: 100%; height: 500px; display: none;"></iframe>

                                    <!-- Link to download the uploaded file -->
                                    <a id="uploaded-link" href="#" target="_blank" style="display: none;">Download
                                        File</a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Accept Status</label>
                                <select class="form-select" name="accept_status" id="accept-status" required>
                                    <option value="" selected disabled>--- Choose Acceptance Status ---</option>
                                    <option value="Verified">Verified</option>
                                    <option value="Doc Accepted">Doc Accepted</option>
                                    <option value="Return/Refund">Return/Refund</option>
                                </select>
                            </div>
                            <div class="mb-3" id="refund-amount-div" style="display: none;">
                                <label for="refund-amount" class="form-label">Refund Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="refund_amount" id="refund-amount" class="form-control" placeholder="ex: 10X.XXX">
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary rounded-pill">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
    <script>
        document.getElementById('refund-amount').addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
            if (value) {
                value = new Intl.NumberFormat('id-ID').format(value); // Format the number
                this.value = value;
            } else {
                this.value = '';
            }
        });
        document.getElementById('accept-status').addEventListener('change', function() {
            var refundDiv = document.getElementById('refund-amount-div');
            if (this.value === 'Return/Refund') {
                refundDiv.style.display = 'block';
            } else {
                refundDiv.style.display = 'none';
            }
        });

        document.getElementById('struk').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const fileURL = URL.createObjectURL(file);

            if (file) {
                // Hide the "no proof" message
                document.getElementById('no-proof-message').style.display = 'none';

                if (file.type.startsWith('image/')) {
                    document.getElementById('uploaded-image').src = fileURL;
                    document.getElementById('uploaded-image').style.display = 'block';
                    document.getElementById('uploaded-pdf').style.display = 'none';
                } else if (file.type === 'application/pdf') {
                    document.getElementById('uploaded-pdf').src = fileURL;
                    document.getElementById('uploaded-pdf').style.display = 'block';
                    document.getElementById('uploaded-image').style.display = 'none';
                }

                document.getElementById('uploaded-link').href = fileURL;
                document.getElementById('uploaded-link').style.display = 'block';
            } else {
                // Show the "no proof" message if no file is selected
                document.getElementById('no-proof-message').style.display = 'block';
            }
        });
    </script>
@endsection
