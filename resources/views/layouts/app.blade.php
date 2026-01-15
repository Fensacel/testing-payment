<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'MyShop'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 flex flex-col min-h-screen">
        
        @include('partials.navbar')

        <main class="flex-grow">
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>

        @include('partials.footer')

        <script>
            // 1. Notifikasi UMUM (Termasuk Tambah Keranjang)
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session("success") }}', // Pesan dari Controller
                    showConfirmButton: false,
                    timer: 2000 // Hilang otomatis dalam 2 detik
                });
            @endif

            // 2. Notifikasi Login Berhasil
            @if(session('login_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Masuk!',
                    text: '{{ session("login_success") }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // 3. Notifikasi Register Berhasil
            @if(session('register_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Akun Dibuat!',
                    text: '{{ session("register_success") }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // 4. Notifikasi Error
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session("error") }}',
                });
            @endif
        </script>
        
    </body>
</html>