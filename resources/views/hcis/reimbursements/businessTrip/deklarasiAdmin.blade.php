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
                        <h4 class="mb-0">Deklarasi Data</h4>
                        <a href="{{ route('businessTrip.admin') }}" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/update/{{ $n->id }}" method="POST"
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
                            <div class="mb-4">
                                <h5 class="mb-3">Estimasi Uang Muka</h5>
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
                                                <th colspan="3">Total Uang Muka</th>
                                                <th>Rp. 160,000</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Input Table -->
                            <div class="mb-4">
                                <h5 class="mb-3">Realization Money (Declaration)</h5>
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
                                                <th colspan="3">Total Uang Realisasi</th>
                                                <th><input type="number" class="form-control" name="total_realisasi"
                                                        readonly></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="struk" class="form-label">Upload Proof</label>
                                <input type="file" id="struk" name="struk" accept="image/*,application/pdf" class="form-control">
                            </div>

                            <div class="text-end mr-3">
                                <button type="submit" class="btn btn-outline-primary">Decline</button>
                                <button type="submit" class="btn btn-primary">Accept</button>
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
        // Add any necessary JavaScript for calculations or interactivity
    </script>
@endsection
