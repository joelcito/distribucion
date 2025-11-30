@extends('layouts.app')

@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('content')
    <div class="container mt-4">
        <h3 class="mb-3">Reporte de Stock</h3>

        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" id="fecha_stock" class="form-control">
            </div>
            <div class="col-md-3">
                <button id="btnFiltrarStock" class="btn btn-success">Filtrar</button>
                <button id="btnImprimirStock" class="btn btn-danger">Imprimir PDF</button>
            </div>
        </div>

        <table class="table table-bordered" id="tablaStock">
            <thead>
                <tr>
                    <th>Producto</th>
                    @foreach($sucursales as $sucursal)
                        <th>{{ $sucursal->nombre }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <!-- Se llena dinámicamente -->
            </tbody>

        </table>
    </div>
@endsection

@section('js')
    <script>
        $('#btnFiltrarStock').click(function () {
            let fecha = $('#fecha_stock').val();
            if (!fecha) { alert('Seleccione una fecha'); return; }

            $.ajax({
                url: "{{ route('reporte.stock.listar') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    fecha: fecha
                },
                success: function (response) {
                    let tbody = $('#tablaStock tbody');
                    tbody.empty();

                    let totals = {};

                    response.sucursales.forEach(s => totals[s.id] = 0);

                    response.productos.forEach(p => {
                        let row = `<tr><td>${p.producto}</td>`;
                        response.sucursales.forEach(s => {
                            let cantidad = p.sucursales[s.id] ?? 0;
                            row += `<td>${cantidad}</td>`;
                            totals[s.id] += cantidad;
                        });
                        row += '</tr>';
                        tbody.append(row);
                    });

                    // Totales
                    response.sucursales.forEach(s => {
                        $(`#total-${s.id}`).text(totals[s.id]);
                    });
                }
            });
        });

        $('#btnImprimirStock').click(function () {
            let fecha = $('#fecha_stock').val();
            if (!fecha) { alert('Seleccione una fecha'); return; }

            window.open(`/reporte/stock/pdf/${fecha}`, "_blank", "width=900,height=650");
        });
    </script>
@endsection