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
                <div class="mb-3">
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                    <i class="bi bi-caret-left-fill"></i> Kembali
                </a> --}}
                </div>
                <div class="card">
                    <div class="card-header d-flex bg-primary text-white justify-content-between">
                        <h4 class="mb-0">Deklarasi Data</h4>
                        <a href="{{ url()->previous() }}" type="button" class="btn-close btn-close-white"></a>
                    </div>
                    <div class="card-body">
                        <form action="/businessTrip/update/{{ $n->id }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="no_sppd" class="form-label">No SPPD</label>
                                <input type="text" class="form-control bg-light" id="no_sppd" name="no_sppd"
                                    style="cursor:not-allowed;" value="{{ $n->no_sppd }}" readonly>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control datepicker" id="mulai" name="mulai"
                                        placeholder="Tanggal Mulai" value="{{ $n->mulai }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="kembali" class="form-label">Tanggal Kembali</label>
                                    <input type="date" class="form-control datepicker" id="kembali" name="kembali"
                                        placeholder="Tanggal Kembali" value="{{ $n->kembali }}" readonly>
                                </div>
                                <form action="/deklarasi/upload" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-6 mt-3">
                                        <label for="struk" class="form-label">Upload Bukti</label>
                                        <input type="file" id="struk" name="struk" accept="image/*"
                                            class="form-control">
                                    </div>
                                </form>
                            </div>
                            {{-- TABLE DE --}}
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
