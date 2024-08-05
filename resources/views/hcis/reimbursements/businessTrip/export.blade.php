@extends('layouts_.vertical', ['page_title' => 'Business Trip'])
@section('content')
<style>
    body {
        background-color: #f8f9fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        max-width: 600px;
        padding: 20px;
        box-sizing: border-box;
    }

    .card {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 20px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        box-sizing: border-box;
    }

    h2 {
        font-size: 1.5rem;
        color: #333;
        margin-top: 0;
        margin-bottom: 15px;
    }

    .download-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }

    .download-item:last-child {
        border-bottom: none;
    }

    .progress-bar {
        flex-grow: 1;
        height: 6px;
        background-color: #007bff;
        margin-left: 10px;
        border-radius: 3px;
    }

    .download-button {
        display: block;
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        text-align: center;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }

    .download-button:hover {
        background-color: #0056b3;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn-secondary,
    .btn-primary {
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        float: right;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Download Files for: {{ $data->nama }}</h2>
        </div>
        <div class="card-body">
            <div class="download-item">
                <input type="checkbox" id="file1" checked>
                <label for="file1">SPPD Document</label>
                <div class="progress-bar"></div>
            </div>
            <!-- Add more download items as needed -->
            <form action="{{ route('export', ['id' => $data->id]) }}" method="POST">
                @csrf
                <p>Are you sure you want to download the file?</p>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-end">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('closeButton').addEventListener('click', function() {
        window.history.back();
    });
</script>
@endsection
