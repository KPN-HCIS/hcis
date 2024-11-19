{{-- Excel Health Coverage Limit --}}
<div class="modal fade" id="importExcelHealtCoverage" tabindex="-1" aria-labelledby="importExcelHealtCoverageLabel" aria-hidden="true">
    <div class="modal-dialog" style="wid">
        <form method="POST" action="{{ route('import.medical') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelHealtCoverage">Import Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="file" required>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('download-template') }}" class="btn btn-primary">Download Template</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
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
                confirmButtonText: 'OK',
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
                confirmButtonText: 'OK',
            });
        });
    </script>
@endif
