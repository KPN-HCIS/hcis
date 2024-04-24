<x-app-layout>
  @section('title', 'Reports')
  <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
      <div class="d-sm-flex align-items-end mb-4">
        <div class="form-group mr-4">
          <label for="report_type">Report Name:</label>
          <select class="form-control" name="report_type" id="report_type">
            <option value="">select report</option>
            <option value="Goal">Goal</option>
          </select>
        </div>  
        <div class="form-group">
          <button id="generate" class="btn btn-primary px-4 shadow">Generate</button> 
        </div>
        <div class="form-group ml-auto">
          <a href="{{ route('export.employee') }}" id="export" class="btn btn-outline-secondary px-4 shadow disabled"><i class="fas fa-arrow-circle-down"></i> Download</a> 
        </div>   
      </div>
      <!-- Content -->
        {{-- @include('reports.goal') --}}
      <div id="report_content">
          @yield('report_content')
      </div>
  </div>
  </x-slot>
</x-app-layout>