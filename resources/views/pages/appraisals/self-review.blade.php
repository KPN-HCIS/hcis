<div class="form-group mb-3">
    <h4 class="mb-3">
        Objektif Kerja
    </h4>
    <input type="hidden" name="formData[{{ $formIndex }}][formName]" value="{{ $name }}">
    <table class="table table-striped table-bordered m-0 mb-4">
        <tbody>
        @forelse ($goalData as $index => $data)
        <tr>
            <td scope="row fs-16">
                <div class="row p-2">
                    <div class="col-lg col-sm-12 p-2">
                        <div class="form-group">
                            <h5>KPI {{ $index + 1 }}</h5>
                            <p class="mt-1 mb-0 text-muted">{{ $data['kpi'] }}</p>
                        </div>
                    </div>
                    <div class="col-lg col-sm-12 p-2">
                        <div class="form-group">
                            <h5>Target</h5>
                            <p class="mt-1 mb-0 text-muted">{{ $data['target'] }} {{ is_null($data['custom_uom']) ? $data['uom']: $data['custom_uom'] }}</p>
                        </div>
                    </div>
                    <div class="col-lg col-sm-12 p-2">
                        <div class="form-group">
                            <h5>Type</h5>
                            <p class="mt-1 mb-0 text-muted">{{ $data['type'] }}</p>
                        </div>
                    </div>
                    <div class="col-lg col-sm-12 p-2">
                        <div class="form-group">
                            <h5>Weightage</h5>
                            <p class="mt-1 mb-0 text-muted">{{ $data['weightage'] }}%</p>
                        </div>
                    </div>
                    <div class="col-lg col-sm-12 p-2">
                        <div class="form-group">
                            <h5>Score</h5>
                            <select class="form-select" name="formData[{{ $formIndex }}][{{ $index }}][score]" id="score" required>
                                <option value="" disabled >select</option>
                                <option value="1"selected>1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                            <div class="text-danger error-message"></div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        @empty
        <p>No form data available.</p>
        @endforelse
        </tbody>
    </table>
    @if (auth()->user()->employee_id != $goal->employee_id)
    <div class="row">
        <div class="col-md-5">
            <label for="messages">Komentar</label>
            <textarea class="form-control" name="messages" id="messages" rows="3" placeholder="masukkan komentar anda.."></textarea>
        </div>
    </div>
    @endif
</div>