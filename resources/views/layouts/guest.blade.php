<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Fachri - MyShop</title>
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                font-family: 'Inter', sans-serif;
            }
            
            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient 15s ease infinite;
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease-out;
            }
            
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            
            .input-modern {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .input-modern:focus {
                transform: translateY(-2px);
                box-shadow: 0 12px 24px -10px rgba(99, 102, 241, 0.3);
            }
            
            .btn-modern {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .btn-modern:hover {
                transform: translateY(-2px);
                box-shadow: 0 20px 40px -12px rgba(99, 102, 241, 0.5);
            }
            
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
            }

            /* Smooth page transition on navigation */
            .page-leave {
                opacity: 0;
                transform: translateY(8px);
                transition: opacity 240ms ease, transform 240ms ease;
            }
        </style>
    </head>
    <body class="antialiased">
        {{ $slot }}

        <script>
            (function() {
                const isModifier = (e) => e.metaKey || e.ctrlKey || e.shiftKey || e.altKey;
                document.addEventListener('click', function(e) {
                    const a = e.target.closest('a[data-smooth]');
                    if (!a) return;
                    const href = a.getAttribute('href');
                    if (!href || isModifier(e) || a.target === '_blank') return;
                    e.preventDefault();
                    document.body.classList.add('page-leave');
                    setTimeout(function() { window.location.href = href; }, 250);
                });
            })();
        </script>
    </body>
</html>
