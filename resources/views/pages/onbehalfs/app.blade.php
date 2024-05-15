<x-app-layout>
    @section('title', $link)
    <x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <form id="categoryForm" action="{{ route('admin.onbehalf.content') }}" method="GET">
            <div class="d-flex align-items-end">
                <div class="form-group mr-3">
                    <label for="category">Year</label>
                    <select name="category" id="category" onchange="changeCategory(this.value)" class="form-control" @style('width: 120px')>
                        <option value="">select all</option>
                        <option value="Goals">Goals</option>
                        <option value="Performance">Performance</option>
                    </select>
                </div>
            </div>
        </form>
        <div class="d-flex align-items-end mb-2 pt-3">
          <div class="form-group mr-4 d-md-block d-none">
            <a href="#" class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalFilter"><i style="font-size: 0.85rem" class="fas fa-filter"></i> Filters</a>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
              </div>
              <input type="text" name="customsearch" id="customsearch" class="form-control border-left-0" placeholder="search.." aria-label="search" aria-describedby="search">
              <div class="d-sm-none input-group-append">
                <a href="#" class="input-group-text btn btn-light bg-white" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i></a>
              </div>
            </div>
          </div>
        </div>
        <div class="d-sm-flex align-items-center justify-content-start mb-3">
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">All Task</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">Draft</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">On Progress</button>
          <button class="btn btn-outline-primary badge-pill btn-sm px-4 mb-2 mr-3">Completed</button>
        </div>
        <!-- Content Row -->
        <div id="contentOnBehalf"></div>

        @include('pages.onbehalfs.filter')
    </div>
    
    </x-slot>
</x-app-layout>
<script src="{{ asset('js/goal-approval.js') }}"></script>