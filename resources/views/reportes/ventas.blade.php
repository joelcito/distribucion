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

        <div class="mb-3">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" id="fecha_inicio" class="form-control">
        </div>
        <div class="mb-3">
            <label for="fecha_fin">Fecha Fin</label>
            <input type="date" id="fecha_fin" class="form-control">
        </div>

        <button id="btnFiltrar" class="btn btn-primary mb-3">Filtrar</button>
        <button id="btnImprimirPDF" class="btn btn-danger mb-3">Imprimir PDF</button>

        <table class="table table-bordered" id="tablaVentas">
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
            <tbody></tbody>
        </table>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {

            function cargarVentas() {
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
                    success: function (res) {
                        let tbody = $('#tablaVentas tbody');
                        tbody.empty();

                        if (res.estado === 'success' && res.data.length > 0) {
                            res.data.forEach((v, index) => {
                                tbody.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${v.sucursal}</td>
                                    <td>${v.cliente}</td>
                                    <td>${v.fecha}</td>
                                    <td>${parseFloat(v.monto).toFixed(2)}</td>
                                    <td>${v.usuario}</td>
                                </tr>
                            `);
                            });
                        } else {
                            tbody.append('<tr><td colspan="6" class="text-center">No se encontraron registros.</td></tr>');
                        }
                    },
                    error: function (err) {
                        console.error(err);
                        alert('Error al cargar los datos.');
                    }
                });
            }

            $('#btnFiltrar').click(cargarVentas);

            $('#btnImprimirPDF').click(function () {
                let fecha_inicio = $('#fecha_inicio').val();
                let fecha_fin = $('#fecha_fin').val();

                if (!fecha_inicio || !fecha_fin) {
                    alert('Seleccione un rango de fechas.');
                    return;
                }

                window.open(`/reporte/imprimeReporteVentas/${fecha_inicio}/${fecha_fin}`, "_blank", "width=900,height=650");
            });

        });
    </script>
@endsection