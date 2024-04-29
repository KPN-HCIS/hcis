<x-app-layout>
  @section('title', 'Reports')
  <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
      <div class="d-sm-flex align-items-end mb-2 pt-3">
          <div class="form-group mr-4">
            <a href="#" class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i></a>
          </div> 
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
              </div>
              <input type="text" name="customsearch" id="customsearch" class="form-control border-left-0" placeholder="search.." aria-label="search" aria-describedby="search">
            </div>   
          </div>
        <div class="form-group ml-auto">   
          <form id="exportForm" action="{{ route('export') }}" method="POST">
            @csrf
            <input type="hidden" name="export_report_type" id="export_report_type">
            <input type="hidden" name="export_group_company" id="export_group_company">
            <input type="hidden" name="export_company" id="export_company">
            <input type="hidden" name="export_location" id="export_location">
            <a id="export" onclick="exportExcel()" class="btn btn-outline-secondary px-4 shadow disabled"><i class="fas fa-arrow-circle-down"></i> Download</a> 
          </form>    
        </div>   
      </div>
      <!-- Content -->
        {{-- @include('reports.goal') --}}
      <div id="report_content">
          @yield('report_content')
      </div>
      @include('reports.filter')
  </div>
  </x-slot>
</x-app-layout>