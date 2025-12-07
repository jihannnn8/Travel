{{-- Bar Navigasi Atas --}}
{{-- Bar navigasi dengan warna putih dan bayangan untuk tampilan yang menonjol --}}
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    {{-- Tombol Sembunyikan Menu Samping di Mobile --}}
    {{-- Tombol untuk menyembunyikan/menampilkan menu samping di layar kecil --}}
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    {{-- Item Navigasi di Sisi Kanan --}}
    <ul class="navbar-nav ml-auto">

        {{-- Dropdown Pencarian di Mobile --}}
        {{-- Menu pencarian yang hanya muncul di layar kecil --}}
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            {{-- Menu Dropdown Pencarian --}}
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        {{-- Garis Pemisah Vertikal --}}
        {{-- Garis pemisah antara notifikasi dan info pengguna --}}
        <div class="topbar-divider d-none d-sm-block"></div>

        {{-- Dropdown Profil Pengguna --}}
        {{-- Menu untuk informasi pengguna dan menu pengguna --}}
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                {{-- Tampilan nama dan peran pengguna --}}
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ auth()->user()->name }}
                    <br>
                    <small>{{ auth()->user()->role }}</small>
                </span>
                {{-- Foto profil pengguna --}}
                <img class="img-profile rounded-circle"
                    src="{{ auth()->user()->foto ? asset('storage/' . auth()->user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=1976D2&color=fff&size=128' }}">
            </a>

            {{-- Menu Dropdown Pengguna --}}
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                {{-- Link profil --}}
                <a class="dropdown-item" href="{{ route('profile') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href=""
                    onclick="event.preventDefault(); document.getElementById('keluar-app').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
