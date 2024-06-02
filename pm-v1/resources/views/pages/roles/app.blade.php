<x-app-layout>
@section('title', $link)
<x-slot name="content">
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-start mb-4">
            <a class="btn btn-outline-primary rounded-pill btn-sm px-4 mr-3 mb-2 {{ $active=='create' ? 'active':'' }}" href="{{ route('roles.create') }}">Create Role</a>
            <a class="btn btn-outline-primary rounded-pill btn-sm px-4 mr-3 mb-2 {{ $active=='manage' ? 'active':'' }}" href="{{ route('roles.manage') }}">Manage Role</a>
            <a class="btn btn-outline-primary rounded-pill btn-sm px-4 mr-3 mb-2 {{ $active=='assign' ? 'active':'' }}" href="{{ route('roles.assign') }}">Assign Users</a>
        </div>
        <!-- Content Row -->
        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
        <div id="subContent"></div>
    </div>
</x-slot>
</x-app-layout>
