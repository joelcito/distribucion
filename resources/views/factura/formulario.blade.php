@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card">
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h1
                                class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                                Formulario de Compra y Venta</h1>
                        </div>
                    </div>
                    <hr>
                    <div id="tabla_clientes">
                    </div>
                    <hr>
                    <div id="tabla_ventas">
                        <form id="formulario_venta">
                            <div class="row">
                                <div class="col-md-11">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="required fw-semibold fs-6 mb-2">Producto / Servicio</label>
                                            <select name="serivicio_id_venta" id="serivicio_id_venta"
                                                class="form-control form-control-sm" onchange="identificaSericio(this)"
                                                required>
                                                <option value="">SELECCIONE</option>
                                                @foreach ($servicios as $s)
                                                    <option value="{{ $s }}">{{ $s->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 visualizacion_m2">
                                            <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                            <input type="text" class="form-control form-control-sm" id="cantidad_venta"
                                                name="cantidad_venta" required onchange="calcularPrecioTotal()">
                                        </div>
                                        <div class="col-md-3 visualizacion_m2">
                                            <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                            <input type="text" class="form-control form-control-sm" id="precio_venta"
                                                name="precio_venta" onchange="calcularPrecioTotal()" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="required fw-semibold fs-6 mb-2">Total</label>
                                            <input type="number" class="form-control form-control-sm" id="total_venta"
                                                name="total_venta" value="0" min="1" required readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <div class="d-flex justify-content-center gap-2 w-100">
                                        <button class="btn btn-primary btn-circle btn-sm btn-icon" type="button"
                                            onclick="mostraBloqueMasDatosProdcuto()" title="Mostrar más opción">
                                            <i class="fa fa-note-sticky"></i> +
                                        </button>
                                        <button class="btn btn-success btn-circle btn-sm btn-icon" type="button"
                                            onclick="agregarProducto()" title="Agregar al Carro de compras"
                                            id="boton-agrega-producto">
                                            <i class="fa fa-xs fa-shopping-cart"></i> +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: none;" id="bloque_mas_datos_productos">
                                <div class="col-md-6">
                                    <label class="fw-semibold fs-6 mb-2">Descripcion Adicional</label>
                                    <textarea class="form-control form-control-sm" name="descripcion_adicional"
                                        id="descripcion_adicional" cols="30" rows="1"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class=" fw-semibold fs-6 mb-2">Numero Serie</label>
                                    <input type="number" class="form-control form-control-sm" id="numero_serie"
                                        name="numero_serie" min="1">
                                </div>
                                <div class="col-md-3">
                                    <label class=" fw-semibold fs-6 mb-2">Codigo Imei</label>
                                    <input type="number" class="form-control form-control-sm" id="codigo_imei"
                                        name="codigo_imei" min="1">
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div id="tabla_detalles" style="display: none;">
                            <h2 class="text-center">CARRITO DE COMPRAS</h2>
                            <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                                <table id="carrito" class="table align-middle table-row-dashed fs-6 gy-5">
                                    <thead>
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th>Servicio / Producto</th>
                                            <th>Medida</th>
                                            <th>Precio</th>
                                            <th>Cantidad </th>
                                            <th>Total</th>
                                            <th>Descuento</th>
                                            <th>Sub Total</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-semibold">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6">Descuento Adicional</th>
                                            <th colspan="2">Monto Total</th>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <input class="form-control form-control-sm" name="descuento_adicional"
                                                    id="descuento_adicional" type="number" value="0"
                                                    onchange="ejecutarDescuentoAdicional()">
                                            </td>
                                            <td colspan="2">
                                                <input class="form-control form-control-sm" name="monto_total"
                                                    id="monto_total" type="number" readonly value="0">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row" id="bloque_seleccionar_cliente" style="display: none">
                            <div class="col-md-11">
                                <button class="btn btn-info btn-sm w-100" onclick="mostrarFormularioClientes()"><span
                                        id="nombre_cliente"></span> <i class="fa fa-user-alt"></i></button>
                                <input type="hidden" name="cliente_id_escogido" id="cliente_id_escogido">
                            </div>
                            <div class="col-md-1">
                                <button title="Mostrar carro de compras" class="btn btn-dark btn-sm btn-circle btn-icon"
                                    onclick="mostrarCarritoVentas()"><i class="fa fa-shopping-basket"></i></button>
                                <button title="Agregar cliente" class="btn btn-primary btn-sm btn-circle btn-icon"
                                    onclick="modalAgregarCliente()"><i class="fa fa-user-plus"></i></button>
                            </div>
                        </div>
                        <form id="formulario_cliente_escogido" style="display: none">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="fs-6 fw-semibold form-label mb-2">Cedula / Nit</label>
                                    <input type="number" class="form-control form-control-sm buscar-persona"
                                        name="nit_escogido" id="nit_escogido">
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-6 fw-semibold form-label mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm buscar-persona"
                                        name="nombre_escogido" id="nombre_escogido">
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-6 fw-semibold form-label mb-2">Ap Paterno</label>
                                    <input type="text" class="form-control form-control-sm buscar-persona"
                                        name="ap_paterno_escogido" id="ap_paterno_escogido">
                                </div>
                                <div class="col-md-3">
                                    <label class="fs-6 fw-semibold form-label mb-2">Ap Materno</label>
                                    <input type="text" class="form-control form-control-sm buscar-persona"
                                        name="ap_materno_escogido" id="ap_materno_escogido">
                                </div>
                            </div>
                        </form>
                        <div id="tabla-clientes-buscados">

                        </div>
                    </div>
                    <hr>
                    <!-- <div class="row" id="bloque-botones-emisiones" style="display: none">
                        <div class="col-md-12">
                            <button class="btn btn-dark w-100 btn-sm" onclick="escogerVentaTipo('RECIBO')">TICKED
                                RECEPCION</button>
                        </div>
                    </div> -->

                    <div class="row" id="bloque-botones-emisiones" style="display: none">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-dark w-100 btn-sm"
                                onclick="mostrarFormularioPedido('RECIBO')">
                                REALIZAR PEDIDO
                            </button>
                        </div>
                    </div>

                    <hr>

                    <div id="bloque_formulario_pedido" style="display: none; margin-top: 20px;">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Cliente</label>
                                <input type="hidden" name="usuario_id" id="usuario_id" value="{{ auth()->user()->id }}">

                                <input type="text" id="cliente_nombre_pedido" class="form-control" readonly>
                                <input type="hidden" name="cliente_id" id="cliente_id_pedido">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Tipo de Pedido</label>
                                <select name="tipo" id="tipo" class="form-control" required>
                                    <option value="">Seleccione</option>
                                    <option value="RECIBO">RECIBO</option>
                                    <option value="FACTURA">FACTURA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Fecha</label>
                                <input type="date" id="fecha" name="fecha" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <input type="hidden" name="productos" id="productos">

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary w-100" onclick="guardarPedido()">Guardar
                                    Pedido</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>




    <hr>
    <hr>
    <h2 class="text-center">Listado de Pedidos</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm" id="kt_table_pedidos">
            <thead>
                <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>ESTADO</th>

                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    use App\Models\Pedido;
                    $pedidos = Pedido::with('cliente')->orderBy('id', 'desc')->get();
                @endphp
                @forelse ($pedidos as $pedido)
                    <tr class="text-center">
                        <td>{{ $pedido->id }}</td>
                        <td>{{ $pedido->cliente->nombres ?? 'N/A' }}</td>
                        <td>{{ $pedido->fecha->format('Y-m-d') ?? 'N/A' }}</td>
                        <td>{{ $pedido->tipo }}</td>
                        <td>{{ $pedido->estado }}</td>

                        <td>
                            <!-- <button class="btn btn-icon btn-sm btn-warning btn-circle"
                                                    onclick="editarPedido({{ $pedido->id }})"><i class="fa fa-edit"></i></button>-->
                            <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Cancelar pedido"
                                onclick="cancelarPedido({{ $pedido->id }})">
                                <i class="fa fa-ban"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-danger">No hay datos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        var arrayProductos = [];
        var arrayPagos = [];
        var table;
        var arrayProductoCar = [];

        $(document).ready(function () {

            $("#serivicio_id_venta, #documento_sector_siat_id_new_servicio, #actividad_economica_siat_id_new_servicio, #producto_servicio_siat_id_new_servicio, #unidad_medida_siat_id_new_servicio, #facturacion_datos_tipo_metodo_pago, #facturacion_datos_tipo_moneda, #tipo_documento")
                .select2();

            table = $('#carrito').DataTable({
                lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
                dom: '<"dt-head row"><"clear">t', // Use dom for basic layout
                language: {
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior'
                    },
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    emptyTable: 'No hay datos disponibles'
                },
                order: [],
                responsive: true
            });
            let debounceTimer;
            $('.buscar-persona').on('keyup', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    ajaxListadoClientes();
                }, 300); // Espera 300 ms antes de ejecutar la función
            });

            $('input[name="uso_cafc"]').on('change', function () {
                verificarRadioSeleccionado();
            });

            $('#nombre_cliente').text('SELECCIONAR CLIENTE')

        });

        function ajaxListadoServicios() {
            let datos = {}
            $.ajax({
                url: "{{ url('factura/ajaxListadoServicios') }}",
                method: "POST",
                data: datos,
                success: function (data) {
                    if (data.estado === 'success') {
                        $('#tabla_clientes').html(data.listado)
                    } else {

                    }
                }
            })
        }

        function identificaSericio(selected) {

            if (selected.value != '') {
                var json = JSON.parse(selected.value);

                console.log(json);

                let cantidad_venta = 1;
                let precio_venta = json.precio_venta;

                $('#cantidad_venta').val(cantidad_venta);
                $('#precio_venta').val((cantidad_venta * precio_venta));
                $('#total_venta').val(precio_venta * cantidad_venta);

                //     $('#precio_venta').val(json.precio_venta)
                //     $('#cantidad_venta').val(1)
                //     $('#total_venta').val((1 * json.precio_venta))
                //     $('#numero_serie').val(json.numero_serie)
                //     $('#codigo_imei').val(json.codigo_imei)
                //     // $('#stock_producto').val(json.stock === null ? 0 : json.stock)
                //     let stockGeneral ;

                //     $('#equivalente_unidad').val(json.equivalente_unidad)
                //     $('#cantidad_por_caja').val(json.cantidad_por_caja)

                //     if(json.unidad_medida_id == "{{ config('siat.metro_cuadrado') }}"){
                //         $('.visualizacion_m2').show('toogle')
                //         $('#medida_producto').attr("required", true);
                //         $('#metro2xcaja').attr("required", true);
                //         $('#nro_cajas').attr("required", true);
                //         $('#nro_piezas').attr("required", true);
                //         stockGeneral = json.stockM2;
                //         $('#stock_producto').val(stockGeneral === null ? 0 : stockGeneral)
                //     }else{
                //         $('.visualizacion_m2').hide('toogle')
                //         $('#medida_producto').attr("required", false);
                //         $('#metro2xcaja').attr("required", false);
                //         $('#nro_cajas').attr("required", false);
                //         $('#nro_piezas').attr("required", false);
                //         stockGeneral = json.stock;
                //         $('#stock_producto').val(stockGeneral === null ? 0 : stockGeneral)
                //     }

                //     if (stockGeneral > 0 || stockGeneral !== null) {
                //         $('#boton-agrega-producto').attr('disabled', false);
                //         $('#stock-bajo').text('');
                //         $('#stock_producto').removeClass('is-invalid');
                //     } else {
                //         $('#boton-agrega-producto').attr('disabled', true);
                //         $('#stock-bajo').text('Stock insuficiente!!');
                //         $('#stock_producto').addClass('is-invalid');
                //     }
                // } else {
                //     $('#precio_venta').val(0)
                //     $('#cantidad_venta').val(0)
                //     $('#total_venta').val(0)
                //     $('#numero_serie').val(null)
                //     $('#codigo_imei').val(null)
                //     $('#descripcion_adicional').val(null)
                //     $('#stock_producto').val(0)
                //     $('#medida_producto').val(null)
                //     $('#metro2xcaja').val(null)
                //     $('#equivalente_unidad').val(0)
                //     $('#cantidad_por_caja').val(0)
                //     $('#nro_cajas').val(0)
                //     $('#nro_piezas').val(0)

                //     $('#cantidad_venta').removeAttr('max');

            }
        }

        function agregarProducto() {
            // Validar antes de agregar
            let servicioSeleccionado = $('#serivicio_id_venta').val();
            let cantidad = parseFloat($('#cantidad_venta').val());
            let precio = parseFloat($('#precio_venta').val());

            if (!servicioSeleccionado) {
                Swal.fire('Error', 'Seleccione un producto', 'warning');
                return;
            }

            if (!cantidad || cantidad <= 0) {
                Swal.fire('Error', 'Ingrese una cantidad válida', 'warning');
                return;
            }

            if (!precio || precio <= 0) {
                Swal.fire('Error', 'Ingrese un precio válido', 'warning');
                return;
            }

            // Aquí continúa tu lógica original de agregar al carrito
            prepararJSONProducto(); // o tu lógica para armar el objeto del carrito
            // ...
        }


        function agregarProducto() {

            if ($("#formulario_venta")[0].checkValidity()) {

                var servicioDatos = JSON.parse($("#serivicio_id_venta").val());

                console.log(servicioDatos);

                let id = servicioDatos.id;
                var filaExistente = table.row("#producto-" + id);
                var precio = parseFloat($('#precio_venta').val()).toFixed(2);
                var cantidad = parseFloat($('#cantidad_venta').val());
                var total = parseFloat(precio * cantidad).toFixed(2);
                var subTotal = (precio * cantidad) - 0;
                var descripcion_adicional = $('#descripcion_adicional').val();
                var monto_total = $('#monto_total').val();


                let servicio = {
                    servicio_id: servicioDatos.id,
                    descripcion: servicioDatos.nombre,
                    precio: parseFloat(precio).toFixed(2),
                    numero_serie: $("#numero_serie").val(),
                    numero_imei: $("#codigo_imei").val(),
                    empresa_id: servicioDatos.empresa_id,
                    cantidad: parseFloat(cantidad),
                    total: parseFloat(total).toFixed(2),
                    descuento: parseFloat(0).toFixed(2),
                    subTotal: parseFloat(subTotal.toFixed(2)),
                    descripcion_adicional: descripcion_adicional
                }

                if (filaExistente.node()) {

                    // // Si el producto ya está en el carrito, aumenta la cantidad en 2
                    // var cantidadCell = $(filaExistente.node()).find('.cantidad');
                    // var cantidadActual = parseFloat(cantidadCell.text());
                    // var nuevaCantidad = cantidadActual + parseFloat(cantidad);
                    // cantidadCell.text(nuevaCantidad);

                    // // Actualiza el total
                    // nuevoTotal = nuevaCantidad * precio
                    // var totalCell = $(filaExistente.node()).find('.total');
                    // totalCell.text((nuevoTotal).toFixed(2));

                    // var subTotalCell = $(filaExistente.node()).find('.subTotal');
                    // var valorSubTotal = parseFloat(subTotalCell.text())
                    // var nuevoSubTotal = nuevoTotal - parseFloat($('#descuento_' + id).val())
                    // subTotalCell.text((nuevoSubTotal).toFixed(2));

                    // let servicio = arrayProductoCar.find(s => s.servicio_id === servicioDatos.id);
                    // if (servicio) {

                    //     servicio.cantidad = parseFloat(servicio.cantidad) + parseFloat(cantidad);
                    //     servicio.total = parseFloat(nuevoTotal);
                    //     servicio.subTotal = parseFloat(nuevoSubTotal);
                    //     servicio.descripcion_adicional = $('#descripcion_adicional').val();

                    //     let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    //     let descuentoAdicional = $('#descuento_adicional').val()

                    //     $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(parseFloat(sumaTotal) -
                    //         parseFloat(descuentoAdicional))
                    //     $('#monto_gift_card').attr('max', parseFloat(sumaTotal) - parseFloat(descuentoAdicional));

                    // } else {
                    //     Swal.fire({
                    //         icon: 'error',
                    //         title: "ERROR!",
                    //         text: "Error al actualizar el descuento.",
                    //         timer: 4000
                    //     })
                    // }

                } else {
                    var subTotal = (precio * cantidad).toFixed(2);

                    // let cant_piezaz_vender = '';
                    // let cant_piezaz_sobrantes = '';

                    // if(servicioDatos.unidad_medida_id == "{{ config('siat.metro_cuadrado') }}"){
                    //     // PARA EL CALCULO DE LAS CAJAS Y PIEZAS
                    //     let cantidadSolicitada = parseFloat(cantidad);
                    //     let equivalenteUnidadM2 = parseFloat(servicioDatos.equivalente_unidad);
                    //     let cantidadCaja = parseFloat(servicioDatos.cantidad_por_caja);

                    //     let cantidadTotalPiezas = cantidadSolicitada / equivalenteUnidadM2;
                    //     let cantidadTotalCajas = cantidadTotalPiezas / cantidadCaja;
                    //     let cantidadTotalPiezasSueltas = cantidadTotalPiezas % cantidadCaja;

                    //     if (Math.round(cantidadTotalPiezasSueltas) == cantidadCaja) {
                    //         cant_piezaz_vender = Math.floor((cantidadTotalCajas)) + 1;
                    //         cant_piezaz_sobrantes = 0;
                    //     } else {
                    //         cant_piezaz_vender = Math.floor((cantidadTotalCajas));
                    //         cant_piezaz_sobrantes = Math.round(cantidadTotalPiezasSueltas);
                    //     }
                    // }

                    table.row.add([
                        servicioDatos.nombre + " " + descripcion_adicional,
                        "UNIDAD",
                        precio,
                        "<span class='cantidad'>" + cantidad + "</span>",
                        "<span class='total'>" + total + "</span>",
                        '<input class="form-control form-control-sm" type="text" name="descuento_' + id +
                        '" id="descuento_' + id + '" value="0" onchange="ejecutarDescuento(this)">',
                        "<span class='subTotal'>" + subTotal + "</span>",
                        "<button class='eliminar btn btn-icon btn-danger btn-circle btn-sm' onclick='eliminarItem(" +
                        id + ")' ><i class='fa fa-trash'></button>"
                    ]).node().id = 'producto-' + id;
                    table.draw(false);

                    // AGREGAMOS AL CARRO LOS PRODUSTOS
                    arrayProductoCar.push(servicio);
                    // AGREGAMOS AL CARRO LOS PRODUSTOS

                    var monto_total_r = parseFloat(monto_total) + parseFloat(servicio.subTotal);

                    $('#monto_total, #monto_total_pagado, #monto_total_pagado_recibo').val(monto_total_r.toFixed(2));

                    //VALIDAMOS QUE EL MONTO DE GITCAD NO SOBREPASE EL MONTO DEL PRODUCTO
                    $('#monto_gift_card').attr('max', monto_total_r);

                    $('#cantidad_venta').val(0)
                    $('#precio_venta').val(0)
                    $('#total_venta').val(0)

                }

                // BORRAMOS LOS ITEM QUE AGREGAMOS
                $('#serivicio_id_venta').val(null).trigger('change');
                $('#tabla_detalles').show('toggle')
                $('#bloque_seleccionar_cliente').show('toggle')
                $("#cantidad_stock").val(0)

            } else {
                $("#formulario_venta")[0].reportValidity();
            }

        }

        function ejecutarDescuento(valor) {

            let valorDescuento = valor.value;
            let valorId = valor.id;
            let id = valorId.split("_")[1]
            var filaExistente = table.row("#producto-" + id);

            console.log("----------------------------------------------");
            console.log(arrayProductoCar);
            console.log("----------------------------------------------");

            if (filaExistente.node()) {
                var totalCell = $(filaExistente.node()).find('.total');
                var valorTotal = parseFloat(totalCell.text());
                var subTotalCell = $(filaExistente.node()).find('.subTotal');

                if (parseFloat(valorDescuento) > -1) {
                    if (valorDescuento < valorTotal) {
                        subTotalCell.text((valorTotal - valorDescuento).toFixed(2));
                        let servicio = arrayProductoCar.find(s => s.servicio_id === parseInt(id));
                        if (servicio) {
                            servicio.descuento = parseFloat(valorDescuento);
                            servicio.subTotal = parseFloat(servicio.total) - parseFloat(valorDescuento);

                            // EJECUTAMOS EL DESCUENTO
                            let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                            let descuentoAdicional = $('#descuento_adicional').val()
                            $('#monto_total, #monto_total_pagado').val(parseFloat(sumaTotal) - parseFloat(
                                descuentoAdicional))
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "ERROR!",
                                text: "Error al actualizar el descuento",
                                timer: 4000
                            })
                        }

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "ERROR!",
                            text: "El valor de descuento no debe ser mayor al valor Total",
                            timer: 4000
                        })
                        $('#descuento_' + id).val(valorTotal - parseFloat(subTotalCell.text()))
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "ERROR!",
                        text: "El valor de descuento debe ser mayor a 0!",
                        timer: 4000
                    })
                    $('#descuento_' + id).val(valorTotal - parseFloat(subTotalCell.text()))
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "ERROR!",
                    text: "Servicion no encontrado",
                    timer: 4000
                })
            }
        }

        function ajaxListadoClientes() {

            if (
                $('#nit_escogido').val().length > 3 ||
                $('#nombre_escogido').val().length >= 3 ||
                $('#ap_paterno_escogido').val().length > 3 ||
                $('#ap_materno_escogido').val().length > 3
            ) {
                let datos = $('#formulario_cliente_escogido').serializeArray();
                $.ajax({
                    url: "{{ url('factura/ajaxListadoClientesBusqueda') }}",
                    method: "POST",
                    data: datos,
                    success: function (data) {
                        if (data.estado) {
                            if (data.data.cantidad > 0)
                                $('#tabla-clientes-buscados').show('toogle')

                            $('#tabla-clientes-buscados').html(data.data.listado)
                        } else {

                        }
                    }
                })
            }
        }

        function mostraBloqueMasDatosProdcuto() {

            $('#bloque_mas_datos_productos').toggle('show')
        }

        // function escogerCliente(cliente, nombres, ap_paterno, ap_materno, cedula, nit, razon_social) {



        //     $('#cliente_id_escogido').val(cliente);

        //     $('#nombre_escogido').val('');
        //     $('#ap_paterno_escogido').val('');
        //     $('#ap_materno_escogido ').val('');
        //     $('#cedula_escogido').val('');

        //     $('#nit_factura').val(nit);
        //     $('#razon_factura').val(razon_social);

        //     $('#tabla-clientes-buscados').hide('toggle')

        //     let nombreusuario = "CLIENTE ESCOGIDO: " + cedula + " | " + nombres + " | " + ap_paterno + " | " + ap_materno;

        //     $('#nombre_cliente').text(nombreusuario)

        //     $('#formulario_cliente_escogido').toggle('hide');

        //     $('#bloque-botones-emisiones').show('toggle');

        //     $('#bloque_recibo').hide('toggle');
        //     $('#bloqueDatosFactura').hide('toggle');
        //     $('#bloque_facturacion').hide('toggle');
        // }


        function escogerCliente(cliente, nombres, ap_paterno, ap_materno, cedula, nit, razon_social) {

            // Guardar el id del cliente
            $('#cliente_id_escogido').val(cliente);

            // Limpiar campos de búsqueda
            $('#nombre_escogido').val('');
            $('#ap_paterno_escogido').val('');
            $('#ap_materno_escogido').val('');
            $('#cedula_escogido').val('');

            $('#nit_factura').val(nit);
            $('#razon_factura').val(razon_social);

            $('#tabla-clientes-buscados').hide();

            // Declarar la variable primero
            let nombreusuario = "CLIENTE ESCOGIDO: " + cedula + " | " + nombres + " | " + ap_paterno + " | " + ap_materno;

            // Mostrar el cliente en los bloques correspondientes
            $('#nombre_cliente').text(nombreusuario);
            $('#cliente_nombre_pedido').val(nombreusuario);  // bloque de pedido
            $('#cliente_id_pedido').val(cliente);            // bloque de pedido

            // Ocultar formulario de búsqueda
            $('#formulario_cliente_escogido').hide();

            // Mostrar botones de emisión
            $('#bloque-botones-emisiones').show();

            // Ocultar otros bloques
            $('#bloque_recibo').hide();
            $('#bloqueDatosFactura').hide();
            $('#bloque_facturacion').hide();
        }


        function escogerVentaTipo(tipo) {
            if (tipo === 'RECIBO') {
                $('#bloque_recibo').show('toggle');
                $('#bloqueDatosFactura').hide('toggle');
                $('#bloque_facturacion').hide('toggle');

                // Mostrar el bloque de pedido
                $('#bloque_pedido').show('toggle');
            } else {
                $('#bloqueDatosFactura').show('toggle');
                $('#bloque_facturacion').show('toggle');
                $('#bloque_recibo').hide('toggle');

                // Ocultar el bloque de pedido si no es RECIBO
                $('#bloque_pedido').hide('toggle');
            }
        }


        function escogerVentaTipo2(tipo) {
            if (tipo === 'RECIBO') {
                // Muestra el bloque de pedido
                $('#bloque_pedido').show();
                // También puedes ocultar otros bloques si quieres
                $('#bloque-botones-emisiones').hide();
            } else if (tipo === 'FACTURA') {
                // Lógica para FACTURA si la necesitas
                $('#bloque_pedido').hide();
            }
        }

        function mostrarFormularioClientes() {
            $('#formulario_cliente_escogido').toggle('show');
            $('#tabla_detalles').hide('toggle');
        }

        function muestraDatosFactura() {

            $('#bloqueDatosFactura').show('toogle')

        }


        function verificaTipoPago(select) {

            let arrayTarjeta = [2, 10, 16, 17, 18, 19, 20, 39, 40, 41, 42, 43, 82, 83, 84, 85, 87, 88, 89, 134, 135, 136,
                137, 139, 140, 141, 142, 143, 144, 145, 147, 148, 149, 150, 151, 152, 154, 155, 156, 157, 158, 160, 161,
                162, 163, 165, 166, 167, 169, 170, 171, 172, 173, 174, 175, 176, 177, 297
            ];
            let arrayGiftCard = [27, 30, 35, 64, 68, 76, 77, 304, 94, 102, 109, 115, 120, 124, 128, 129, 130, 182, 189, 195,
                200, 204, 217, 224, 225, 228, 232, 241, 246, 250, 261, 265, 269, 270, 271, 275, 279, 280, 281, 291, 292,
                293
            ];
            let arrayTarjetaGiftCard = [86, 138, 146, 153, 159, 164, 168, 223];
            let valor = parseInt(select.value);
            // if(valor == 2 || valor == 10  || valor == 83 || valor == 86){
            if (arrayTarjeta.includes(valor)) {
                $('#bloque-tipo-pago').show('toggle')
                $('#monto_gift_card').val(null)
                $('#bloque-gifr-card').hide('toggle')
                // }else if(valor == 27 || valor == 35){
            } else if (arrayGiftCard.includes(valor)) {
                $('#bloque-gifr-card').show('toggle')
                $('#numero_tarjeta').val(null)
                $('#bloque-tipo-pago').hide('toggle')
            } else if (arrayTarjetaGiftCard.includes(valor)) {
                $('#bloque-gifr-card').show('toggle')
                $('#bloque-tipo-pago').show('toggle')
            } else {
                $('#monto_gift_card').val(null)
                $('#numero_tarjeta').val(null)

                $('#bloque-gifr-card').hide('toggle')
                $('#bloque-tipo-pago').hide('toggle')
            }
        }

        function verificarNumeroTarjeta() {
            const input = document.getElementById("numero_tarjeta");
            let valor = input.value;
            valor = valor.replace(/\D/g, "");
            if (valor.length > 8) {
                input.value = masked;
            } else {
                input.value = valor; // Muestra el valor completo si es menor o igual a 8 dígitos
            }

        }

        function emitirFactura() {

            if ($("#formularioGeneraFactura")[0].checkValidity()) {

                if (arrayProductoCar.length > 0) {

                    $.ajax({
                        url: "{{ url('factura/emitirFacturaCv') }}",
                        method: "POST",
                        data: {
                            cliente_id: $('#cliente_id_escogido').val(),
                            carrito: arrayProductoCar,
                            facturacion_datos_tipo_metodo_pago: $('#facturacion_datos_tipo_metodo_pago').val(),
                            numero_tarjeta: $('#numero_tarjeta').val(),
                            facturacion_datos_tipo_moneda: $('#facturacion_datos_tipo_moneda').val(),
                            tipo_documento: $('#tipo_documento').val(),
                            nit_factura: $('#nit_factura').val(),
                            razon_factura: $('#razon_factura').val(),
                            tipo_facturacion: $('#tipo_facturacion').val(),
                            uso_cafc: $('input[name="uso_cafc"]:checked').val(),
                            numero_factura_cafc: $('#numero_factura_cafc').val(),
                            execpcion: $('#execpcion').is(':checked'),
                            complemento: $('#complemento').val(),
                            descuento_adicional: $('#descuento_adicional').val(),
                            monto_total: $('#monto_total').val(),
                            monto_gift_card: $('#monto_gift_card').val(),
                            cufd_offLine: $('#cufd_offLine').val(),
                            fecha_emision_offLine: $('#fecha_emision_offLine').val(),
                            hora_emision_offLine: $('#hora_emision_offLine').val(),
                            tipo_pago_pagado: $('#tipo_pago_pagado').val(),
                            realizo_pago: $('#realizo_pago').is(':checked'),
                            monto_total_pagado: $('#monto_total_pagado').val(),
                            monto_pagado: $('#monto_pagado').val(),
                            cambio_pagado: $('#cambio_pagado').val()
                        },
                        success: function (data) {
                            if (data.estado === "VALIDADA") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Excelente!',
                                    text: 'LA FACTURA FUE VALIDADA',
                                    timer: 3000
                                })
                                if (data.numero != null && data.numero != '') {
                                    window.open("{{ url('factura/generaPdfFacturaNewCv') }}/" + data.numero,
                                        "_blank", "width=800,height=600");
                                    window.location.reload();
                                } else {
                                    window.location.href = "{{ url('factura/listado') }}"
                                }
                            } else if (data.estado === "error_email") {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.text,
                                })
                                // Habilita el botón y oculta el icono de carga después de completar
                                boton.attr("disabled", false);
                                iconoCarga.hide();
                            } else if (data.estado === "OFFLINE") {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Exito!',
                                    text: 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
                                    timer: 2000
                                })
                                window.location.href = "{{ url('factura/listado') }}"
                            } else if (data.estado === "error_firma") {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.text,
                                    text: 'EMISION DE FACTURA RECHAZADO',
                                })
                                // Habilita el botón y oculta el icono de carga después de completar
                                boton.attr("disabled", false);
                                iconoCarga.hide();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.text + " " + data.data,
                                    text: 'LA FACTURA FUE RECHAZADA',
                                })
                                // Habilita el botón y oculta el icono de carga después de completar
                                boton.attr("disabled", false);
                                iconoCarga.hide();
                            }
                        },
                        error: function (error) {

                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "Debe tener al menos un producto agregado al carrito!",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    })
                }
            } else {
                $("#formularioGeneraFactura")[0].reportValidity();
            }
        }

        function ejecutarDescuentoAdicional() {

            let descuentoAdcional = parseFloat($('#descuento_adicional').val())
            let montoTotal = parseFloat($('#monto_total').val())

            if (descuentoAdcional > -1) {
                if (descuentoAdcional < montoTotal) {
                    let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    let descuentoAdicional = $('#descuento_adicional').val();
                    $('#monto_total, #monto_total_pagado').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: "Error",
                        text: 'El descuento Adicional no debe ser mayor al monto total!',
                    })
                    let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                    let descuentoAdicional = 0;
                    $('#monto_total, #monto_total_pagado').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                    $('#descuento_adicional').val(0);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "Error",
                    text: 'El descuento debe ser mayor a 0!',
                })
                let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
                let descuentoAdicional = 0;
                $('#monto_total, #monto_total_pagado').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional))
                $('#descuento_adicional').val(0);
            }
        }

        function bloqueCAFC() {
            if ($('#tipo_facturacion').val() === "offline") {

                let tipo_documento = $('#tipo_documento').val();
                let emision = $('#tipo_facturacion').val();

                verificarExcepcion(tipo_documento, emision);

            } else {
                $('#numero_factura_cafc').val(null)
                $('#bloque_cafc, #numero_fac_cafc').hide('toggle')
                $('#execpcion').prop('checked', false);

                $('#select_cufd_vigentes').html('')
                $('#bloque_cufd_offline').hide('toggle');

                // Marcar el radio button con value="No" usando name
                $('input[name="uso_cafc"][value="No"]').prop('checked', true);
            }
        }

        function verificarRadioSeleccionado() {
            var valorSeleccionado = $('input[name="uso_cafc"]:checked').val();
            if (valorSeleccionado === 'No') {

                $('#numero_fac_cafc').hide('toggle');
                $('#numero_factura_cafc').val(0)

            } else if (valorSeleccionado === 'Si') {
                $.ajax({
                    url: "{{ url('factura/sacaNumeroCafcUltimo') }}",
                    method: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if (data.estado) {
                            $("#numero_factura_cafc").val(data.data.numero);
                            $('#numero_fac_cafc').show('toggle');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: "Algo fallo"
                            })
                        }
                    }
                })
            }
        }

        function eliminarItem(id) {

            var fila = table.row("#producto-" + id);
            table.row(fila).remove().draw(false);
            arrayProductoCar = arrayProductoCar.filter(s => s.servicio_id !== id);
            let sumaTotal = arrayProductoCar.reduce((sum, current) => sum + current.subTotal, 0);
            let descuentoAdicional = $('#descuento_adicional').val();
            $('#monto_total, #monto_total_pagado').val(parseFloat(sumaTotal) - parseFloat(descuentoAdicional));

        }

        function mostrarCarritoVentas() {
            $('#tabla_detalles').toggle('show')
        }

        function calcularPrecioTotal() {
            let precio = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            let total = parseFloat(precio) * parseFloat(cantidad);

            // CALCULAMOS LAS CANTIDAD DE CAJAS Y PIEZAS
            let equivalente_unidadM2 = parseFloat($('#equivalente_unidad').val());
            let cantidad_por_caja = parseFloat($('#cantidad_por_caja').val());

            let cantidadTotalPiezas = parseFloat(cantidad) / equivalente_unidadM2;
            let cantidadTotalCajas = cantidadTotalPiezas / cantidad_por_caja;
            let cantidadTotalPiezasSueltas = cantidadTotalPiezas % cantidad_por_caja;

            if (Math.round(cantidadTotalPiezasSueltas) == cantidad_por_caja) {
                $('#nro_cajas').val(Math.floor(cantidadTotalCajas) + 1);
                $('#nro_piezas').val(0);
            } else {
                $('#nro_cajas').val(Math.floor(cantidadTotalCajas));
                $('#nro_piezas').val(cantidadTotalPiezasSueltas);
            }

            $('#total_venta').val(total.toFixed(2))
        }

        function modalAgregarCliente() {
            $('#modal_new_cliente').modal('show');
        }

        function guardarClienteEmpresa() {
            if ($("#formulario_new_cliente_empresa")[0].checkValidity()) {
                let datos = $('#formulario_new_cliente_empresa').serializeArray();
                $.ajax({
                    url: "{{ url('cliente/guardarClienteFactura') }}",
                    method: "POST",
                    data: datos,
                    success: function (data) {
                        if (data.estado) {
                            Swal.fire({
                                icon: 'success',
                                title: "EXITO!",
                                text: "SE REGISTRO CON EXITO",
                            })

                            console.log(data.data.cliente);

                            $('#cliente_id_escogido').val(data.data.cliente.id);

                            let cedula = $('#cedula_cliente_new_usuaio_empresa').val();
                            let nombres = $('#nombres_cliente_new_usuaio_empresa').val();
                            let ap_paterno = $('#ap_paterno_cliente_new_usuaio_empresa').val();
                            let ap_materno = $('#ap_materno_cliente_new_usuaio_empresa').val();

                            let nombreusuario = cedula + " | " + nombres + " | " + ap_paterno + " | " +
                                ap_materno;
                            $('#nombre_cliente').text(nombreusuario)

                            $('#nit_factura').val($('#nit_cliente_new_usuaio_empresa').val());
                            $('#razon_factura').val($('#razon_social_cliente_new_usuaio_empresa').val());

                            $('#bloqueDatosFactura, #bloque_facturacion').show('toggle');

                            //ajaxListado();
                        } else if (data.estado === 'error') {
                            Swal.fire({
                                icon: 'warning',
                                title: "ALTO!",
                                text: data.text,
                            })
                        } else {

                        }
                        $('#modal_new_cliente').modal('hide');
                    }
                })
            } else {
                $("#formulario_new_cliente_empresa")[0].reportValidity();
            }
        }

        function modalAgregarProducto() {
            $('#modal_new_servicio').modal('show');
        }

        function guardarNewServioEmpresa() {
            if ($("#formulario_new_servicio")[0].checkValidity()) {
                // let datos = $('#formulario_new_servicio').serializeArray();
                let formData = new FormData($("#formulario_new_servicio")[0]);
                $.ajax({
                    url: "{{ url('empresa/guardarNewServioEmpresaFormularioFacturacion') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.estado === 'success') {


                            Swal.fire({
                                icon: 'success',
                                title: "EXITO!",
                                text: "SE REGISTRO CON EXITO",
                            })

                            let nuevosServicios = data.servicio;

                            var select = $('#serivicio_id_venta');

                            // Vaciar el select
                            select.empty();

                            // Añadir la opción por defecto
                            select.append('<option value="">SELECCIONE</option>');

                            // Volver a llenar el select con las nuevas opciones
                            $.each(nuevosServicios, function (index, servicio) {
                                let d = JSON.stringify(servicio)
                                select.append('<option value=\'' + d + '\'>' + servicio.descripcion +
                                    '</option>');
                            });

                            //ajaxListado();
                        } else if (data.estado === 'error') {
                            Swal.fire({
                                icon: 'warning',
                                title: "ALTO!",
                                text: data.text,
                            })
                        }
                        $('#modal_new_servicio').modal('hide');
                    }
                })
            } else {
                $("#formulario_new_servicio")[0].reportValidity();
            }
        }

        function verificarExcepcion(tipo_documento, emision, uso_cafse) {
            if (emision === "offline") {
                if (tipo_documento == "5") { //VERIFICAMOS QUE SEA NIT
                    $('#execpcion').prop('checked', true);
                } else {
                    $('#execpcion').prop('checked', false);
                }
                $('#bloque_cafc').show('toggle')

                $.ajax({
                    url: "{{ url('eventoSignificativo/sacarCufdsPorTipoEvento') }}",
                    method: "POST",
                    data: {},
                    success: function (data) {
                        if (data.estado) {
                            // REMPLAZAR LOS CUFDS VIGENTES
                            $('#select_cufd_vigentes').html(data.data.select)
                            $('#bloque_cufd_offline').show('toggle');
                        } else {
                            $('#select_cufd_vigentes').html('')
                            $('#bloque_cufd_offline').hide('toggle');
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: data.text,
                            })
                        }
                    }
                })
            }
        }


        function modalAperturaCaja() {
            // $('#nombre').val('')
            $('#monto_apertura').val(0)
            $('#descripcion').val('')
            $('#modalAperturaCaja').modal('show')
        }

        function guardarAperturaCaja() {
            if ($('#formularioAperturaCaja')[0].checkValidity()) {
                $('#boton_abrir_caja').attr('disabled', true);
                let datos = $('#formularioAperturaCaja').serializeArray();
                $.ajax({
                    url: "{{ url('caja/guardarAperturaCaja') }}",
                    method: "POST",
                    data: datos,
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "EL REGISTRO FUE EXITOSO.",
                                icon: "success",
                                timer: 3000, // Se cierra en 3 segundos
                                showConfirmButton: false
                            });

                            location.reload();
                        } else {

                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.' + xhr,
                        });
                    }
                });
            } else {
                $('#formularioAperturaCaja')[0].reportValidity()
            }
        }

        function caluclarCambio(select) {

            let monto_total_pagado = parseFloat($('#monto_total_pagado').val())
            let monto_pagado = parseFloat(select.value)

            if (monto_pagado > monto_total_pagado) {
                $('#cambio_pagado').val(monto_pagado - monto_total_pagado)
            } else if (monto_pagado <= monto_total_pagado) {
                $('#cambio_pagado').val(0)
            }

            if (monto_pagado === 0) {
                $('#tipo_pago_pagado').prop('required', false)
                $('#realizo_pago').prop('required', false)
            } else {
                $('#tipo_pago_pagado').prop('required', true)
                $('#realizo_pago').prop('required', true)
            }

        }

        function modalCerrarCaja() {
            $('#modalCerrarCaja').modal('show')
        }

        function guardarCerrarCaja() {
            if ($('#formularioCerrarCaja')[0].checkValidity()) {
                $('#boton_cerrar_caja').attr('disabled', true);
                let datos = $('#formularioCerrarCaja').serializeArray();
                $.ajax({
                    url: "{{ url('caja/guardarCerrarCaja') }}",
                    method: "POST",
                    data: datos,
                    success: function (resultado) {
                        if (resultado.estado) {
                            Swal.fire({
                                title: "EL REGISTRO FUE EXITOSO.",
                                icon: "success",
                                timer: 3000, // Se cierra en 3 segundos
                                showConfirmButton: false
                            });

                            location.reload();
                        } else {

                        }
                    },
                    error: function (xhr) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.' + xhr,
                        });

                        // limpiarErorres();

                        // if (xhr.status === 422) {
                        //     let errores = xhr.responseJSON.errors;

                        //     for (let campo in errores) {
                        //         let mensaje = errores[campo][0];

                        //         let input = $(`[name="${campo}"]`);
                        //         input.addClass("is-invalid");
                        //         input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                        //     }
                        // } else {
                        //     Swal.fire({
                        //         icon: 'error',
                        //         title: 'Error',
                        //         text: 'Ocurrió un error inesperado.',
                        //     });
                        // }
                    }
                });
            } else {
                $()[0].reportValidity();
            }
        }

        function validarCampos() {
            let tipo_pago = $('#tipo_pago_pagado').val()

            if (tipo_pago === 'EFECTIVO' || tipo_pago === 'TRANSFERENCIA' || tipo_pago === 'QR') {
                $('#realizo_pago').prop('required', true);
                $('#monto_pagado').prop('required', true);
                $('#cambio_pagado').prop('required', true);
                $('#realizo_pago').prop('checked', true);
                $('#monto_pagado').attr('min', 1);
            } else {
                $('#realizo_pago').prop('required', false);
                $('#monto_pagado').prop('required', false);
                $('#cambio_pagado').prop('required', false);
                $('#realizo_pago').prop('checked', false);
                $('#monto_pagado').attr('min', 0);
            }
        }

        function validarCamposRecibo() {
            let tipo_pago = $('#tipo_pago_pagado_recibo').val()

            if (tipo_pago === 'EFECTIVO' || tipo_pago === 'TRANSFERENCIA' || tipo_pago === 'QR') {
                $('#realizo_pago_recibo').prop('required', true);
                $('#monto_pagado_recibo').prop('required', true);
                $('#cambio_pagado_recibo').prop('required', true);
                $('#realizo_pago_recibo').prop('checked', true);
                $('#monto_pagado_recibo').attr('min', 1);
            } else {
                $('#realizo_pago_recibo').prop('required', false);
                $('#monto_pagado_recibo').prop('required', false);
                $('#cambio_pagado_recibo').prop('required', false);
                $('#realizo_pago_recibo').prop('checked', false);
                $('#monto_pagado_recibo').attr('min', 0);
            }
        }

        function caluclarCambioRecibo(select) {

            let monto_total_pagado = parseFloat($('#monto_total_pagado_recibo').val())
            let monto_pagado = parseFloat(select.value)

            if (monto_pagado > monto_total_pagado) {
                $('#cambio_pagado_recibo').val(monto_pagado - monto_total_pagado)
            } else if (monto_pagado <= monto_total_pagado) {
                $('#cambio_pagado_recibo').val(0)
            }

            if (monto_pagado === 0) {
                $('#tipo_pago_pagado_recibo').prop('required', false)
                $('#realizo_pago_recibo').prop('required', false)
            } else {
                $('#tipo_pago_pagado_recibo').prop('required', true)
                $('#realizo_pago_recibo').prop('required', true)
            }
        }

        function emitirRecibo() {
            if ($("#formularioGeneraRecibo")[0].checkValidity()) {

                if (arrayProductoCar.length > 0) {

                    // // Obtén el botón y el icono de carga
                    // var boton = $("#boton_enviar_recibo");
                    // var iconoCarga = boton.find("i");
                    // // Deshabilita el botón y muestra el icono de carga
                    // boton.attr("disabled", true);
                    // iconoCarga.show();

                    $.ajax({
                        url: "{{ url('factura/emitirRecibo') }}",
                        method: "POST",
                        data: {
                            cliente_id: $('#cliente_id_escogido').val(),
                            carrito: arrayProductoCar,
                            nit_factura: $('#nit_factura').val(),
                            razon_factura: $('#razon_factura').val(),
                            descuento_adicional: $('#descuento_adicional').val(),
                            monto_total: $('#monto_total').val(),
                            tipo_pago_pagado: $('#tipo_pago_pagado_recibo').val(),
                            realizo_pago: $('#realizo_pago_recibo').is(':checked'),
                            monto_total_pagado: $('#monto_total_pagado_recibo').val(),
                            monto_pagado: $('#monto_pagado_recibo').val(),
                            cambio_pagado: $('#cambio_pagado_recibo').val()
                        },
                        success: function (data) {
                            if (data.estado) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Excelente!',
                                    text: 'EL TICKED FUE VALIDADA',
                                    timer: 3000
                                })
                                if (data.numero != null && data.numero != '') {
                                    window.open("{{ url('factura/generaPdfFacturaNewCv') }}/" + data.numero,
                                        "_blank", "width=800,height=600");
                                    window.location.reload();
                                } else {
                                    window.location.href = "{{ url('factura/listado') }}"
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: JSON.stringify(data),
                                    text: 'EL RECIBO RECHAZADA',
                                })
                                // Habilita el botón y oculta el icono de carga después de completar
                                boton.attr("disabled", false);
                                iconoCarga.hide();
                            }
                        },
                        error: function (error) {

                        }
                    })


                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "Debe tener al menos un producto agregado al carrito!",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    })
                }

            } else {
                $("#formularioGeneraRecibo")[0].reportValidity();
            }
        }

        function calcularCajasPiezas() {
            let nro_cajas = parseFloat($('#nro_cajas').val());
            let nro_piezas = parseFloat($('#nro_piezas').val());
            let cantidad_por_caja = parseFloat($('#cantidad_por_caja').val());
            let equivalente_unidad = parseFloat($('#equivalente_unidad').val());

            if (nro_piezas == cantidad_por_caja) {
                nro_piezas = 0;
                nro_cajas = nro_cajas + 1
                $('#nro_piezas').val(nro_piezas);
                $('#nro_cajas').val(nro_cajas);
            } else if (nro_piezas > cantidad_por_caja) {

                let calcula_nro_cajas = Math.floor(nro_piezas / cantidad_por_caja);
                let calcula_nro_piezas_sobrantes = nro_piezas % cantidad_por_caja;

                nro_piezas = calcula_nro_piezas_sobrantes;
                nro_cajas = nro_cajas + calcula_nro_cajas

                $('#nro_piezas').val(nro_piezas);
                $('#nro_cajas').val(nro_cajas);

            }

            let cantidad_piezas_totales = (cantidad_por_caja * nro_cajas) + nro_piezas;
            let cantidad_metro_cuadrado = cantidad_piezas_totales * equivalente_unidad;

            $('#cantidad_venta').val(cantidad_metro_cuadrado.toFixed(2));

            // PARA CALCULAR EL PRECIO
            let precio = $('#precio_venta').val();
            let cantidad = $('#cantidad_venta').val();
            let total = parseFloat(precio) * parseFloat(cantidad);
            $('#total_venta').val(total.toFixed(2))

        }




        function fijarCliente(select) {
            let clienteTexto = select.options[select.selectedIndex].text;
            let clienteId = select.value;

            if (clienteId === "") return;

            // Guardar en input hidden
            document.getElementById('cliente_id').value = clienteId;

            // Ocultar select y mostrar solo texto
            select.style.display = "none";
            document.getElementById('cliente_fijo').style.display = "block";
            document.getElementById('cliente_fijo').innerHTML = "Cliente seleccionado: <b>" + clienteTexto + "</b>";
        }

        function guardarPedido() {
            prepararJSONProductos(); // Convertimos el carrito al formato correcto

            let cliente_id = $('#cliente_id_pedido').val();
            let usuario_id = $('#usuario_id').val();
            let tipo = $('#tipo').val();
            let fecha = $('#fecha').val();
            let productos_json = $('#productos').val();

            if (!cliente_id || !tipo || !fecha) {
                Swal.fire('Error', 'Complete todos los campos', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('pedidos.store') }}",
                type: "POST",
                data: {
                    cliente_id: cliente_id,
                    tipo: tipo,
                    fecha: fecha,
                    productos: productos_json,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.estado) {
                        Swal.fire('Éxito', 'Pedido guardado correctamente', 'success');
                        $('#bloque_formulario_pedido').hide();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Error interno al guardar el pedido', 'error');
                }
            });
        }


        $(document).ready(function () {
            $('#kt_table_pedidos').DataTable({
                lengthMenu: [10, 25, 50, 100],
                scrollX: true,
                responsive: true,
                language: {
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior'
                    },
                    search: 'Buscar:',
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    emptyTable: 'No hay datos disponibles'
                },
                order: [[0, 'desc']],
            });
        });
        //cancelar pedido

        function cancelarPedido(id) {
            Swal.fire({
                title: '¿Desea cancelar este pedido?',
                text: "Esto reingresará los productos al stock",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/pedidos/' + id + '/cancelar',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            if (res.estado) {
                                Swal.fire('Cancelado', res.mensaje, 'success');


                                $('#pedido-row-' + id).find('td:nth-child(5)').text('CANCELADO');
                            } else {
                                Swal.fire('Error', res.mensaje, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                        }
                    });
                }
            });
        }

    </script>
@endsection