@extends('layouts_.vertical', ['page_title' => 'Document'])

@section('css')
    <style>
        .preview-container {
            background: white;
            padding: 20px;
            min-height: 500px;
            border: 1px solid #ddd;
        }
        
        .form-panel {
            position: sticky;
            top: 20px;
        }
        
        .preview-panel {
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Styling untuk konten DOCX */
        #previewContainer {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        
        #previewContainer p {
            margin-bottom: 1em;
        }
    </style>
@endsection

@section('content')
    <br>

    <div class="container-fluid py-3">
        <!-- Breadcrumb -->
        <div>
            <h4 class="mb-0 mt-2">{{ $parentLink }}</h4>
        </div>
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{ $parentLink }}</li>
                <li class="breadcrumb-item">📁</li>
                <li class="breadcrumb-item active">📄 {{ $letter_name }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Left Panel - Form -->
            <form action="{{ route('docGenerator.download') }}" method="POST" id="templateForm" class="d-flex">
                @csrf
                <input type="hidden" name="template_path" value="{{ $template_path }}">
                {{-- {{dd($template_path);}} --}}
                <input type="hidden" name="letter_name" value="{{ $letter_name }}">
                <input type="hidden" name="action" id="formAction" value="save">

                <!-- Input Fields -->
                <div class="col-md-4 p-1">
                    <div class="card">
                        <div class="card-body" style="height: 600px; overflow-y: auto;">
                            {{-- <div class="form-group mb-3">
                                <label for="letter_name">Letter Name:</label>
                                <input type="text" class="form-control preview-update" name="letter_name" id="letter_name" value="{{ $letter_name }}">
                            </div> --}}
                            @if (!empty($placeholders))
                                @foreach ($placeholders as $placeholder)
                                    <div class="form-group mb-3">
                                        <label for="{{ $placeholder }}">{{ ucfirst($placeholder) }}:</label>
                                        <input type="text" 
                                            name="{{ $placeholder }}" 
                                            id="{{ $placeholder }}" 
                                            required 
                                            class="form-control preview-update"
                                            data-placeholder="${{ $placeholder }}" 
                                            placeholder="Enter {{ ucfirst($placeholder) }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Panel - DOCX Preview -->
                <div class="col-md-8 p-1">
                    <div class="card preview-panel">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Preview Document</h5>
                        </div>
                        <div class="card-body">
                            <div id="previewContainer" class="preview-container"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="position-fixed bottom-0 end-0 p-3">
            <a href="{{ route('docGenerator') }}" class="btn btn-secondary me-2">Close</a>
            <button class="btn btn-primary" type="submit" form="templateForm">Generate Document</button>
            <button class="btn btn-primary" type="button" id="previewButton">Preview Generate</button>
        </div>
    </div>    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>
@endsection

@push('scripts')
    <script>
        let docxContent = '';

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function updatePreview() {
            if (!docxContent) return;

            let updatedContent = docxContent;
            const inputs = document.querySelectorAll('.preview-update');

            inputs.forEach(input => {
                const placeholder = input.dataset.placeholder;
                const value = input.value.trim() || placeholder;
                const regex = new RegExp(escapeRegExp(placeholder), 'g');
                updatedContent = updatedContent.replace(regex, value);
            });

            const previewContainer = document.getElementById('previewContainer');
            previewContainer.innerHTML = updatedContent;
        }

        function loadPreview() {
            fetch('{{ asset($template_path) }}')
                .then(response => response.arrayBuffer())
                .then(arrayBuffer => mammoth.convertToHtml({ arrayBuffer }))
                .then(result => {
                    docxContent = result.value;
                    updatePreview();
                    setupEventListeners();
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('previewContainer').innerHTML = 
                        `<div class="alert alert-danger">Error loading preview: ${error.message}</div>`;
                });
        }

        function setupEventListeners() {
            document.querySelectorAll('.preview-update').forEach(input => {
                input.addEventListener('input', updatePreview);
            });
        }

        document.addEventListener('DOMContentLoaded', loadPreview);
    </script>
    <script>
        document.getElementById('previewButton').addEventListener('click', function() {
            // Get all form data
            const formData = new FormData(document.getElementById('templateForm'));
            
            // Send AJAX request
            fetch('{{ route("docGenerator.preview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Load preview using mammoth
                    fetch('/' + data.preview_path)
                        .then(response => response.arrayBuffer())
                        .then(arrayBuffer => {
                            return mammoth.convertToHtml({ arrayBuffer: arrayBuffer });
                        })
                        .then(result => {
                            document.getElementById('previewContainer').innerHTML = result.value;
                        });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('previewContainer').innerHTML = 'Error generating preview';
            });
        });
    </script>
@endpush