@extends('layouts_.vertical', ['page_title' => 'Business Trip'])

@section('css')
    <style>
        .btn-action {
            margin-right: 10px;

        }

        .date-range {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .date-range label {
            margin-right: 10px;
        }

        .date-range input {
            margin-right: 10px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            align-content: center;
        }

        .table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .table th.rowspan,
        .table th.colspan {
            border-bottom: 2px solid #000;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tr:hover {
            background-color: #ddd;
        }

        .text-center {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col mt-3">
                <a href="/reimbursements" class="btn btn-warning btn-action" style="background: #AB2F2B !important; border: 0px;">
                    <i class="bi bi-caret-left-fill"></i> Kembali
                </a>
                <a href="<?php $_SERVER['PHP_SELF']; ?>" class="btn btn-info btn-action">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
                <a href="#" class="btn btn-success btn-action">
                    <i class="bi bi-plus-circle"></i> Add Data
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form class="date-range">
                    <label for="start-date">Departure Date:</label>
                    <input type="date" id="start-date" name="start-date" class="form-control" value="2024-07-02">
                    <label for="end-date">to</label>
                    <input type="date" id="end-date" name="end-date" class="form-control" value="2024-07-01">
                    <button type="submit" class="btn btn-primary"
                        style="background-color: #AB2F2B !important; border:0px;">Generate</button>
                </form>

                <div class="card mt-4">
                    <div class="card-body">
                        <h3 class="card-title">Data SPPD</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="3">No</th>
                                        <th rowspan="3">Nama</th>
                                        <th rowspan="3">Divisi</th>
                                        <th rowspan="3">No SPPD</th>
                                        <th colspan="2" class="text-center">Perjalanan Dinas</th>
                                        <th colspan="4" class="text-center">SPPD</th>
                                        <th rowspan="3">Status</th>
                                        <th rowspan="3">Export</th>
                                        <th rowspan="3">Action</th>
                                    </tr>
                                    <tr>
                                        <th>Mulai</th>
                                        <th>Kembali</th>
                                        <th rowspan="2" class="text-center">CA</th>
                                        <th rowspan="2" class="text-center">Ticket</th>
                                        <th rowspan="2" class="text-center">Hotel</th>
                                        <th rowspan="2" class="text-center">Taksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="15" class="text-center">No data available in table</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <select class="form-select" style="width: auto;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>records per page</span>
                            </div>
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" placeholder="Search...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination justify-content-end">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            @endsection
