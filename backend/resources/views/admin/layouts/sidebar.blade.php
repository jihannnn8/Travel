{{-- Menu Navigasi Samping --}}
{{-- Menu samping dengan warna hijau(success) dan tema gelap --}}
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
        {{-- <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-solid fa-hand-holding-heart"></i>
        </div> --}}
        <div class="sidebar-brand-text mx-3">Travelo</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-solid fa-house-user"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-solid fa-users"></i>
            <span>Users</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('destinations.index') }}">
            <i class="fas fa-solid fa-globe"></i>
            <span>Destination</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('bookings.index') }}">
            <i class="fas fa-solid fa-book"></i>
            <span>Bookings</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
