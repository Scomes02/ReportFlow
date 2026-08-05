<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Universitario - Portal Clínico</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
        }

        /* Navbar superior */
        .top-navbar {
            height: 60px;
            background-color: #1d3557;
            color: white;
        }

        /* Sidebar lateral */
        .sidebar {
            width: 240px;
            min-height: calc(100vh - 60px);
            background-color: #ffffff;
            border-right: 1px solid #e9ecef;
        }

        .sidebar .nav-link {
            color: #495057;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 4px;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: #1d3557;
        }

        .sidebar .nav-link.active {
            background-color: #e8f0fe;
            color: #1a73e8;
            font-weight: 600;
        }

        /* Contenedor del contenido principal */
        .main-content {
            flex-grow: 1;
            padding: 25px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR SUPERIOR -->
    <nav class="navbar top-navbar px-3 shadow-sm">
        <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
            <!-- Logo / Nombre de la Institución -->
            <a class="navbar-brand text-white fw-bold d-flex align-items-center gap-2 m-0" href="#">
                <i class="bi bi-hospital fs-4"></i>
                HOSPITAL UNIVERSITARIO
            </a>

            <!-- Búsqueda Central -->
            <div class="w-50">
                <input type="search" class="form-control form-control-sm rounded-pill px-3" placeholder="Buscar paciente o ID estudio...">
            </div>

            <!-- Datos del Usuario / Perfil -->
            <div class="d-flex align-items-center text-white gap-2">
                <div class="text-end d-none d-md-block" style="line-height: 1.2;">
                    <small class="d-block fw-bold mb-0">García</small>
                    <small class="text-white-50" style="font-size: 0.75rem;">Dr. Cardiología</small>
                </div>
                <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                    G
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENEDOR PRINCIPAL (SIDEBAR + TU TABLA) -->
    <div class="d-flex">
        
        <!-- SIDEBAR LATERAL -->
        <aside class="sidebar p-3 shadow-sm">
            <h6 class="text-uppercase text-muted fw-bold mb-3 px-2" style="font-size: 0.75rem;">Cardiología</h6>
            
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="{{ route('medico.estudios.index') }}" class="nav-link {{ request()->routeIs('medico.estudios.index') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Worklist (Pendientes)
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-folder2-open me-2"></i> Mis Informes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-graph-up me-2"></i> Mi Rendimiento
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a href="#" class="nav-link text-danger">
                        <i class="bi bi-trash me-2"></i> Papelera
                    </a>
                </li>
            </ul>
        </aside>

        <!-- SECCIÓN DE CONTENIDO (TU TABLA Y MODALES) -->
        <main class="main-content">
            <div class="container-fluid p-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4">Worklist de Estudios Pendientes</h4>

                        <!-- Tabla de Estudios -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Estudio</th>
                                        <th>Paciente</th>
                                        <th>Tipo de Estudio</th>
                                        <th>Fecha/Hora</th>
                                        <th>Técnico</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estudios as $estudio)
                                    <tr>
                                        <td>{{ $estudio->id }}</td>
                                        <td>{{ $estudio->paciente }}</td>
                                        <td>{{ $estudio->tipo_estudio }}</td>
                                        <td>{{ $estudio->fecha }}</td>
                                        <td>{{ $estudio->tecnico }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ $estudio->estado }}</span>
                                        </td>
                                        <td>
                                            <!-- Botón que dispara el modal del estudio específico -->
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalInformar-{{ $loop->index }}">
                                                Informar
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modales de Informe -->
            @foreach($estudios as $estudio)
            <div class="modal fade" id="modalInformar-{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Estudio: {{ $estudio->tipo_estudio }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <form action="{{ route('medico.estudios.informar', $estudio->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <!-- Cabecera de datos del paciente -->
                                <div class="bg-light p-3 rounded mb-3">
                                    <p class="mb-1"><strong>Paciente:</strong> {{ $estudio->paciente }}</p>
                                    <p class="mb-1"><strong>Edad:</strong> {{ $estudio->edad ?? 'N/A' }} años</p>
                                    <p class="mb-1"><strong>Fecha:</strong> {{ $estudio->fecha }}</p>
                                    <p class="mb-0"><strong>Técnico:</strong> {{ $estudio->tecnico }}</p>
                                </div>

                                <!-- Formulario / Campo del Informe -->
                                <div class="mb-3">
                                    <label for="informe-{{ $loop->index }}" class="form-label fw-bold">INFORME {{ strtoupper($estudio->tipo_estudio) }}</label>
                                    <textarea class="form-control" id="informe-{{ $loop->index }}" name="informe" rows="8" required placeholder="Escriba el resultado y la conclusión del estudio aquí..."></textarea>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="modal-footer d-flex justify-content-between border-0">
                                <button type="button" class="btn btn-warning text-white fw-bold">Rehacer</button>
                                <div>
                                    <button type="button" class="btn btn-danger me-2" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success fw-bold">Guardar y firmar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>