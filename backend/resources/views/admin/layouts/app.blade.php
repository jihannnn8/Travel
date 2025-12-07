<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Tag Meta untuk SEO dan Kompatibilitas Browser --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    {{-- Judul Halaman --}}
    <title>Travelo - Dashboard</title>

    {{-- File CSS dari Luar --}}
    {{-- FontAwesome untuk ikon-ikon --}}
    <link href="{{ asset('admin_assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    {{-- Google Fonts - Font Nunito untuk tulisan --}}
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    {{-- Bootstrap 5 - Framework CSS untuk tampilan yang bagus --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    {{-- Ikon Website (Favicon) --}}
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin_assets/img/logo_icon.png') }}">
    @stack('styles')

    {{-- CSS Khusus untuk Tema Admin --}}
    <link href="{{ asset('admin_assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">
    {{-- Pembungkus Utama Halaman --}}
    <div id="wrapper">

        {{-- Menu Samping Kiri --}}
        @include('admin.layouts.sidebar')
        {{-- Akhir dari Menu Samping --}}

        {{-- Area Konten Utama --}}
        <div id="content-wrapper" class="d-flex flex-column">

            {{-- Bagian Konten Atas --}}
            <div id="content">

                {{-- Bar Navigasi Atas --}}
                @include('admin.layouts.navbar')
                {{-- Akhir dari Bar Navigasi --}}

                {{-- Wadah Konten Halaman Utama --}}
                <div class="container-fluid">

                    {{-- Bagian Header Halaman --}}
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
                    </div>

                    {{-- Area Konten yang Berubah-ubah --}}
                    @yield('contents')

                    {{-- Baris Konten --}}
                    {{-- Bagian tambahan bisa ditambahkan di sini --}}

                </div>
                {{-- Akhir dari wadah konten --}}

            </div>
            {{-- Akhir dari Konten Utama --}}

            {{-- Bagian Footer --}}
            @include('admin.layouts.footer')
            {{-- Akhir dari Footer --}}

        </div>
        {{-- Akhir dari Pembungkus Konten --}}

    </div>
    {{-- Akhir dari Pembungkus Halaman --}}

    {{-- Tombol Scroll ke Atas --}}
    {{-- Tombol mengambang untuk kembali ke atas halaman --}}
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    {{-- Form Logout yang Tersembunyi --}}
    {{-- Form untuk keluar dari sistem dengan cara yang aman --}}
    <form id="keluar-app" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    {{-- Akhir dari form logout --}}

    {{-- jQuery - Library JavaScript untuk mengatur elemen halaman --}}
    <script src="{{ asset('admin_assets/vendor/jquery/jquery.min.js') }}"></script>

    {{-- Bootstrap Bundle - Termasuk Bootstrap JS dan Popper.js --}}
    <script src="{{ asset('admin_assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- Popper.js - Untuk posisi tooltip dan popover --}}
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    {{-- Bootstrap 5 JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"
        integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous">
    </script>

    <script src="{{ asset('admin_assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <script src="{{ asset('admin_assets/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/rt.js/Charchat.min.js') }}"></script>
    <script>
        function previewFoto() {
            const foto = document.querySelector('input[name="foto"]');

            const fotoPreview = document.querySelector('.foto-preview');

            fotoPreview.style.display = 'block';

            const fotoReader = new FileReader();

            fotoReader.readAsDataURL(foto.files[0]);

            fotoReader.onload = function(fotoEvent) {
                fotoPreview.src = fotoEvent.target.result;

                fotoPreview.style.width = '100%';
            }
        }
    </script>

    @stack('scripts')
</body>

</html>
