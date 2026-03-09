<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal da Transparência</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        $client = env('CLIENT_NAME', 'default');
    @endphp
    <link rel="shortcut icon" href="{{ asset('img/' . $client . '.png') }}" type="image/x-icon">
    <style>
        /* Estilos personalizados para aproximar o design da imagem */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fcfcfc;
        }

        .top-bar {
            font-size: 0.85rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .nav-link {
            color: #555;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #d9534f;
            border-bottom: 2px solid #d9534f;
        }

        /* Cores dos Cards de Resumo */
        .card-green {
            background-color: #4CAF50;
            color: white;
        }

        .card-red {
            background-color: #E53935;
            color: white;
        }

        .card-orange {
            background-color: #FFB300;
            color: white;
        }

        .card-blue {
            background-color: #42A5F5;
            color: white;
        }

        .summary-card {
            padding: 20px;
            text-align: right;
            border-radius: 0;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .summary-card h2 {
            margin-bottom: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }

        .summary-card p {
            margin-bottom: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Rodapé */
        footer {
            background-color: #333;
            color: #ccc;
            font-size: 0.9rem;
        }

        footer h5 {
            color: white;
            font-weight: normal;
            margin-bottom: 15px;
        }

        .footer-bottom {
            background-color: #222;
            font-size: 0.8rem;
            padding: 15px 0;
        }

        /* Estilo para permitir submenus laterais */
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
            border-radius: 0;
            display: none;
            /* Escondido por padrão */
        }

        /* Mostra o submenu ao passar o mouse no item pai */
        .dropdown-submenu:hover>.dropdown-menu {
            display: block;
        }

        /* Estilização para ficar igual à imagem (Laranja) */
        .dropdown-item:hover {
            background-color: #f15a24;
            /* Laranja da imagem */
            color: white;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            background-color: #f15a24;
        }

        /* Ajuste na seta do submenu */
        .dropdown-submenu>a::after {
            display: inline-block;
            float: right;
            margin-top: 5px;
            content: "\f105";
            /* Ícone de seta do Font Awesome */
            font-family: "Font Awesome 6 Free";
            font-weight: 600;
            border: none;
        }
    </style>
</head>

<body>
    @include('layouts.partials.topbar')

    @include('layouts.partials.navbar')

    <main class="py-4">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/highcharts.js') }}"></script>
    
    <script>
        // Selecionamos o nosso carrossel pelo ID
        const myCarousel = document.getElementById('portalCarousel');

        // Este evento do Bootstrap dispara toda vez que um slide começa a transição
        myCarousel.addEventListener('slide.bs.carousel', event => {

            // Buscamos todos os elementos que têm classes de animação dentro do carrossel
            const animatedElements = event.target.querySelectorAll('.animate__animated');

            animatedElements.forEach(el => {
                // Descobrimos qual era a animação original (ex: animate__fadeInLeft)
                const animationClass = Array.from(el.classList).find(cl => cl.startsWith('animate__'));

                // Removemos a animação para "resetar" o elemento
                el.classList.remove(animationClass);

                // Forçamos o navegador a registrar a remoção e reiniciamos a animação
                // Isso é um truque técnico (void el.offsetWidth) para reiniciar o ciclo de CSS
                void el.offsetWidth;
                el.classList.add(animationClass);
            });
        });
    </script>

</body>

</html>
