<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistema de Ventas - Arevalo</title>

    <meta name="description" content="Sistema de gestión de ventas desarrollado por Arevalo. Controla clientes, productos, compras y ventas de forma simple y eficiente." />
    <meta name="author" content="Arevalo Sistemas" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<!-- 🔷 NAVBAR -->
<nav class="navbar navbar-expand-md bg-body-secondary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{route('panel')}}">
            <img src="{{ asset('assets/img/icon.png') }}" width="30">
            Arevalo Ventas
        </a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('panel')}}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Sobre el sistema</a>
                </li>
            </ul>

            <a href="{{route('login.index')}}" class="btn btn-primary">
                Ingresar
            </a>
        </div>
    </div>
</nav>

<!-- 🔷 HERO / CARRUSEL -->
<div id="carouselExample" class="carousel slide carousel-fade">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{asset('assets/img/img_carrusel_1.png')}}" class="d-block w-100">
        </div>
        <div class="carousel-item">
            <img src="{{asset('assets/img/img_carrusel_2.png')}}" class="d-block w-100">
        </div>
        <div class="carousel-item">
            <img src="{{asset('assets/img/img_carrusel_3.png')}}" class="d-block w-100">
        </div>
    </div>
</div>

<!-- 🔷 BENEFICIOS -->
<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Potenciá tu negocio con tecnología</h2>
        <p class="text-muted">Una solución moderna para gestionar tu empresa</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-info h-100">
                <div class="card-header text-center text-info fw-bold">
                    Con nuestro sistema
                </div>
                <div class="card-body">
                    <ul>
                        <li>Acceso 24/7 desde cualquier dispositivo</li>
                        <li>Automatización de ventas y stock</li>
                        <li>Reportes en tiempo real</li>
                        <li>Escalabilidad para tu negocio</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-danger h-100">
                <div class="card-header text-center text-danger fw-bold">
                    Sin sistema
                </div>
                <div class="card-body">
                    <ul>
                        <li>Dependencia de horarios</li>
                        <li>Errores manuales frecuentes</li>
                        <li>Falta de control de datos</li>
                        <li>Dificultad para crecer</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🔷 CTA -->
<section class="bg-body-secondary text-center py-5">
    <h2 class="mb-3">Transformá tu negocio hoy</h2>
    <p class="mb-4">Digitalizá tu gestión y tomá mejores decisiones</p>
    <a href="{{route('login.index')}}" class="btn btn-success btn-lg">
        Comenzar ahora
    </a>
</section>

<!-- 🔻 FOOTER PERSONALIZADO -->
<footer class="text-center text-white mt-4">
    <div class="container p-4">

        <p class="mb-2">Sistema de Ventas - Arevalo</p>

        <div class="mb-3">
            <a class="btn btn-outline-light btn-sm" href="#" target="_blank">Instagram</a>
            <a class="btn btn-outline-light btn-sm" href="#" target="_blank">LinkedIn</a>
            <a class="btn btn-outline-light btn-sm" href="#" target="_blank">GitHub</a>
        </div>

    </div>

    <div class="text-center p-3" style="background: rgba(0,0,0,0.2);">
        © {{ date('Y') }} Arevalo Sistemas - Todos los derechos reservados
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>