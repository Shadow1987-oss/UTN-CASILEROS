{{-- Layout principal de la aplicación UTN Lockers.
     Incluye barra de navegación con menús según rol (admin/tutor/estudiante),
     badge de notificaciones no leídas y sección de contenido dinámica. --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'UTNAY Lockers')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('estilos/index.css') }}">
    @yield('estilos')
</head>

<body>
    <div class="wrapper">
        <!-- HEADER -->
        <header>
            <div class="content">
                <div class="brand">
                    <h1> UTNAY Lockers</h1>
                    <span>Asignación y control de casilleros</span>
                </div>
                <nav>
                    @section('menu')
                        @auth
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('dashboard') }}"
                                    class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Tablero</a>
                                <a href="{{ route('students.index') }}"
                                    class="{{ request()->routeIs('students.*') ? 'active' : '' }}">Estudiantes</a>
                                <a href="{{ route('lockers.index') }}"
                                    class="{{ request()->routeIs('lockers.*') ? 'active' : '' }}">Casilleros</a>
                                <a href="{{ route('assignments.index') }}"
                                    class="{{ request()->routeIs('assignments.*') ? 'active' : '' }}">Asignaciones</a>
                                <a href="{{ route('locker_requests.index') }}"
                                    class="{{ request()->routeIs('locker_requests.*') ? 'active' : '' }}">Solicitudes</a>
                                <a href="{{ route('reportes.index') }}"
                                    class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">Reportes</a>
                                @php
                                    $adminMoreActive =
                                        request()->routeIs('periods.*') ||
                                        request()->routeIs('careers.*') ||
                                        request()->routeIs('buildings.*') ||
                                        request()->routeIs('usuarios.*') ||
                                        request()->routeIs('sanciones.*') ||
                                        request()->routeIs('recibe.*');
                                @endphp
                                <div class="nav-more">
                                    <button type="button"
                                        class="nav-more-toggle {{ $adminMoreActive ? 'active' : '' }}">Más</button>
                                    <div class="nav-more-menu">
                                        <a href="{{ route('periods.index') }}"
                                            class="{{ request()->routeIs('periods.*') ? 'active' : '' }}">Períodos</a>
                                        <a href="{{ route('careers.index') }}"
                                            class="{{ request()->routeIs('careers.*') ? 'active' : '' }}">Carreras</a>
                                        <a href="{{ route('buildings.index') }}"
                                            class="{{ request()->routeIs('buildings.*') ? 'active' : '' }}">Edificios</a>
                                        <a href="{{ route('usuarios.index') }}"
                                            class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">Tutores</a>
                                        <a href="{{ route('sanciones.index') }}"
                                            class="{{ request()->routeIs('sanciones.*') ? 'active' : '' }}">Sanciones</a>
                                        <a href="{{ route('recibe.index') }}"
                                            class="{{ request()->routeIs('recibe.*') ? 'active' : '' }}">Recibos</a>
                                    </div>
                                </div>
                            @elseif (auth()->user()->role === 'tutor')
                                <a href="{{ route('dashboard') }}"
                                    class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Tablero</a>
                                <a href="{{ route('students.index') }}"
                                    class="{{ request()->routeIs('students.*') ? 'active' : '' }}">Estudiantes</a>
                                <a href="{{ route('lockers.index') }}"
                                    class="{{ request()->routeIs('lockers.*') ? 'active' : '' }}">Casilleros</a>
                                <a href="{{ route('assignments.index') }}"
                                    class="{{ request()->routeIs('assignments.*') ? 'active' : '' }}">Asignaciones</a>
                                <a href="{{ route('locker_requests.index') }}"
                                    class="{{ request()->routeIs('locker_requests.*') ? 'active' : '' }}">Solicitudes</a>
                                @php
                                    $tutorMoreActive = request()->routeIs('estadisticas.*');
                                @endphp
                                <div class="nav-more">
                                    <button type="button"
                                        class="nav-more-toggle {{ $tutorMoreActive ? 'active' : '' }}">Más</button>
                                    <div class="nav-more-menu">
                                        <a href="{{ route('estadisticas.index') }}"
                                            class="{{ request()->routeIs('estadisticas.*') ? 'active' : '' }}">Estadísticas</a>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('student.home') }}"
                                    class="{{ request()->routeIs('student.home') ? 'active' : '' }}">Mi casillero</a>
                            @endif
                            <a href="{{ route('notifications.index') }}"
                                class="nav-icon-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                                title="Notificaciones" aria-label="Notificaciones">🔔</a>
                            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                @csrf
                                <button class="btn secondary" type="submit" style="padding:8px 12px;">Salir</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                        @endauth
                    @show
                </nav>
            </div>
        </header>

        <!-- CONTENT -->
        <div class="content">
            <section>
                <div id="inicio"></div>
                <!-- ALERTS & MESSAGES -->
                @if (session('status'))
                    <div class="alert">{{ session('status') }}</div>
                @endif

                @if (session('message'))
                    <div class="alert">{{ session('message') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert error">
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul style="margin: 8px 0 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- CONTENT AREA -->
                <div id="contenido-principal">
                    @yield('contenido')
                </div>
            </section>
        </div>

        <!-- FOOTER -->
        <footer id="pie-pagina">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>UTNAY Lockers</h4>
                    <p>Sistema institucional para la asignación y control de casilleros.</p>
                </div>
                <div class="footer-section">
                    <h4>Accesos rápidos</h4>
                    @auth
                        @if (auth()->user()->role === 'estudiante')
                            <a href="{{ route('student.home') }}">Mi casillero</a>
                        @else
                            <a href="{{ route('dashboard') }}">Tablero</a>
                            <a href="{{ route('students.index') }}">Estudiantes</a>
                            <a href="{{ route('lockers.index') }}">Casilleros</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}">Iniciar sesión</a>
                    @endauth
                </div>
                <div class="footer-section">
                    <h4>Contacto</h4>
                    <p><strong>Versión:</strong> 1.0.0</p>
                    <p><strong>Contacto:</strong> lockers@utnay.edu.mx</p>
                    <p><strong>Teléfono:</strong> +52 (311) 211-0128</p>
                </div>
                <div class="footer-section">
                    <h4 style="text-align: center;">Síguenos</h4>
                    <div class="social-links" style="justify-content: center;">
                        <a href="https://www.facebook.com/UTNAY" target="_blank" title="Facebook">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="https://x.com/utnay_comer" target="_blank" title="Twitter">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/ut.nayarit/" target="_blank" title="Instagram">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/company/utnay" target="_blank" title="LinkedIn">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} UTNAY Lockers. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- Botón flotante ir arriba -->
    <button id="btn-arriba" onclick="scrollToTop()" title="Ir arriba">↑</button>

    <script>
        // Mostrar/ocultar botón al hacer scroll
        window.addEventListener('scroll', function() {
            const btn = document.getElementById('btn-arriba');
            if (window.pageYOffset > 300) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        });

        // Función para ir arriba
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>

    <script src="{{ asset('js/form.js') }}"></script>
    @yield('scripts')
</body>

</html>
