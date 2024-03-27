<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon">
            <img @style('width: 30px;') src="{{ asset('img/logos/kpn.png') }}" alt="kpn logo">
        </div>
        <div class="sidebar-brand-text mx-3">Performance Management</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menu
    </div>

       <!-- Nav Item - Dashboard -->
    <li class="nav-item">
    <a class="nav-link" href="{{ route('home') }}">
        <i class="fas fa-fw fa-chart-pie"></i>
        <span>Dashboard</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('tasks') }}">
            <i class="fas fa-fw fa-tasks"></i>
            <span>Tasks</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('goals') }}">
            <i class="fas fa-fw fa-flag-checkered"></i>
            <span>Goals</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="{{ Url('/') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Appraisal</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ Url('/') }}">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Calibration</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ Url('/reports') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Reports</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Admin
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-cog"></i>
            <span>Settings</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('schedules') }}">Schedule</a>
                <a class="collapse-item" href="{{ route('assignments') }}">Assignment</a>
                <a class="collapse-item" href="{{ route('roles') }}">Role</a>
                <a class="collapse-item" href="{{ route('layers') }}">Layer</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>