@extends('layouts.app') {{-- Reemplaza por la plantilla de tu equipo --}}

@section('content')
<div class="container-fluid py-4">
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

                        <!-- Modal de Informe (Se genera para cada estudio) -->
                        <div class="modal fade" id="modalInformar-{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Estudio: {{ $estudio->tipo_estudio }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <form action="{{ route('medicos.estudios.informar', $estudio->id) }}" method="POST">
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
                                                <label for="informe" class="form-label fw-bold">INFORME {{ strtoupper($estudio->tipo_estudio) }}</label>
                                                <textarea class="form-control" name="informe" rows="8" required placeholder="Escriba el resultado y la conclusión del estudio aquí..."></textarea>
                                            </div>
                                        </div>

                                        <!-- Botones de Acción (Igual al prototipo) -->
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection