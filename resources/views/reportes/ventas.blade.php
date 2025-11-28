@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')

    <div class="container mt-4">

        <h3 class="mb-3">Reporte de Ventas</h3>

        <!-- Botón para abrir modal de filtrado -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalFiltrarVentas">
            Filtrar por Fecha
        </button>

        <!-- Modal -->
        <div class="modal fade" id="modalFiltrarVentas" tabindex="-1" aria-labelledby="modalFiltrarVentasLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filtrar Ventas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formFiltrarVentas">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success">Filtrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de resultados -->
        <table class="table table-striped table-bordered" id="tablaVentas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sucursal</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargan dinámicamente por AJAX -->
            </tbody>
        </table>

    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function () {

            $('#formFiltrarVentas').on('submit', function (e) {
                e.preventDefault();

                let fecha_inicio = $('#fecha_inicio').val();
                let fecha_fin = $('#fecha_fin').val();

                $.ajax({
                    url: "{{ route('reporte.ventas.listado') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        fecha_inicio: fecha_inicio,
                        fecha_fin: fecha_fin
                    },
                    success: function (response) {
                        console.log(response); // Depuración

                        // Validar que data sea un array
                        if (response.estado === 'success' && Array.isArray(response.data)) {
                            let tbody = $('#tablaVentas tbody');
                            tbody.empty(); // Limpiar tabla

                            response.data.forEach((venta, index) => {
                                tbody.append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${venta.sucursal?.nombre ?? ''}</td>
                                        <td>${venta.cliente?.nombre ?? ''}</td>
                                        <td>${venta.fecha}</td>
                                        <td>${parseFloat(venta.total).toFixed(2)}</td>
                                        <td>${venta.usuario_creador?.name ?? ''}</td>
                                    </tr>
                                `);
                            });

                            // Cerrar modal
                            $('#modalFiltrarVentas').modal('hide');

                        } else {
                            alert('No se encontraron registros para el rango de fechas seleccionado.');
                            $('#tablaVentas tbody').empty();
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        alert('Ocurrió un error al cargar los datos. Revisa la consola para más detalles.');
                    }
                });

            });

        });
    </script>
@endsection