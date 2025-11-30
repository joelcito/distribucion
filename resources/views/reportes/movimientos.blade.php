@extends('layouts.app')

@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
    <div class="container mt-4">

        <h3 class="mb-3">Reporte de Movimientos</h3>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="fecha_movimiento" class="form-label">Fecha</label>
                <input type="date" id="fecha_movimiento" class="form-control">
            </div>

            <div class="col-md-4">
                <label for="producto_id" class="form-label">Producto</label>
                <select id="producto_id" class="form-control">
                    <option value="">Seleccione un producto</option>
                    @foreach($productos as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button id="btnFiltrarMovimientos" class="btn btn-success me-2">Buscar</button>
                <button id="btnImprimirPDF" class="btn btn-danger">Imprimir PDF</button>
            </div>
        </div>

        <!-- Tabla de resultados -->
        <table class="table table-striped table-bordered" id="tablaMovimientos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Sucursal</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos cargados dinámicamente -->
            </tbody>
        </table>

    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {

            $('#btnFiltrarMovimientos').click(function () {
                let fecha = $('#fecha_movimiento').val();
                let producto_id = $('#producto_id').val();

                $.ajax({
                    url: "{{ route('reporte.movimientos.listado') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        fecha: fecha,
                        producto_id: producto_id
                    },
                    success: function (response) {
                        if (response.estado === 'success') {
                            let tbody = $('#tablaMovimientos tbody');
                            tbody.empty();
                            response.data.forEach((m, i) => {
                                tbody.append(`
                                            <tr>
                                                <td>${i + 1}</td>
                                                <td>${m.producto}</td>
                                                <td>${m.cantidad}</td>
                                                <td>${m.tipo}</td>
                                                <td>${m.fecha}</td>
                                                <td>${m.descripcion}</td>
                                                <td>${m.sucursal}</td>
                                                <td>${m.usuario}</td>
                                            </tr>
                                        `);
                            });
                        } else {
                            alert('No se encontraron registros.');
                            $('#tablaMovimientos tbody').empty();
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        alert('Ocurrió un error al cargar los datos.');
                    }
                });
            });

            $('#btnImprimirPDF').click(function () {
                let fecha = $('#fecha_movimiento').val();
                let producto_id = $('#producto_id').val();

                if (!fecha) {
                    alert('Seleccione una fecha para imprimir.');
                    return;
                }

                // Abrir PDF en nueva ventana
                window.open(`/reporte/imprimeMovimientosPorFechaYProducto/${fecha}/${producto_id}`, "_blank", "width=900,height=650");
            });


        });
    </script>
@endsection