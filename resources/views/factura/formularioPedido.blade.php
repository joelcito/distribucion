@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

<div class="d-flex flex-column flex-column-fluid">
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
                            <button class="btn btn-sm btn-warning" title="Editar pedido"
                                onclick="editarPedido({{ $pedido->id }})">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-sm btn-danger btn-circle" title="Cancelar pedido"
                                onclick="cancelarPedido({{ $pedido->id }})">
                                <i class="fa fa-ban"></i>
                            </button>
                            <button class="btn btn-sm btn-info" title="Ver detalle"
                                onclick="verDetallePedido({{ $pedido->id }})">
                                <i class="fa fa-eye"></i>
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


<!--editar-->
<div class="modal fade" id="modalEditarPedido" tabindex="-1" aria-labelledby="modalEditarPedidoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Editar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pedido_id">
                <table class="table table-bordered" id="tablaProductosEditar">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-primary btn-sm" id="agregarProductoEditar">+ Agregar
                    Producto</button>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="guardarCambios">Guardar Cambios</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!--ver detalle-->
<div class="modal fade" id="modalDetallePedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="tablaDetallePedido">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@stop()

@section('js')

    @php
        if (!isset($productos)) {
            $productos = \App\Models\Producto::all();
        }
    @endphp

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



                } else {
                    var subTotal = (precio * cantidad).toFixed(2);



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
            $('#cliente_nombre_pedido').val(nombreusuario); // bloque de pedido
            $('#cliente_id_pedido').val(cliente); // bloque de pedido

            // Ocultar formulario de búsqueda
            $('#formulario_cliente_escogido').hide();

            // Mostrar botones de emisión
            $('#bloque-botones-emisiones').show();

            // Ocultar otros bloques
            $('#bloque_recibo').hide();
            $('#bloqueDatosFactura').hide();
            $('#bloque_facturacion').hide();
            $('#bloque_formulario_pedido').show();
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


        function seleccionarCliente(id, nombre) {
            document.getElementById('cliente_id_pedido').value = id;
            document.getElementById('cliente_nombre_pedido').value = nombre;
        }





        function prepararJSONProductos() {
            const productos = [];

            arrayProductoCar.forEach(function (p) {
                productos.push({
                    producto_id: p.servicio_id,
                    cantidad: p.cantidad,
                    precio: p.precio,
                    subTotal: p.subTotal,
                    descripcion_adicional: p.descripcion_adicional
                });
            });

            // Guardar JSON en el input hidden
            $('#productos').val(JSON.stringify(productos));
        }



        function mostrarFormularioPedido(tipo) {
            const bloque = document.getElementById('bloque_formulario_pedido');
            bloque.style.display = 'block';

            // Limpiar campos
            document.getElementById('tipo').value = tipo;
            document.getElementById('cliente_id_pedido').value = '';
            document.getElementById('cliente_nombre_pedido').value = '';
            document.getElementById('productos').value = '';
        }


        function guardarPedido() {
            prepararJSONProductos(); // Llenar el input con JSON válido

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
                        location.reload(); // Opcional: recarga para mostrar el pedido
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
                order: [
                    [0, 'desc']
                ],
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
                        // url: '/pedidos/' + id + '/cancelar',
                        url: "{{ url('pedidos') }}/" + id + "/cancelar",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
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

        const productosDisponibles = @json($productos);

        function editarPedido(id) {
            $.ajax({
                // url: '/pedidos/' + id + '/obtener',
                url: "{{ url('pedidos') }}/" + id + "/obtener",
                type: 'GET',
                success: function (res) {
                    if (res.estado) {
                        const pedido = res.pedido;
                        $('#pedido_id').val(pedido.id);

                        const tbody = $('#tablaProductosEditar tbody');
                        tbody.empty();

                        pedido.productos.forEach(prod => {
                            let opciones = '';
                            productosDisponibles.forEach(p => {
                                const selected = (p.id == prod.id) ? 'selected' : '';
                                opciones +=
                                    `<option value="${p.id}" ${selected}>${p.nombre}</option>`;
                            });

                            tbody.append(
                                `
                                            <tr>
                                                <td>
                                                    <select name="productos[][id]" class="form-control">
                                                        ${opciones}
                                                    </select>
                                                </td>
                                                <td><input type="number" name="productos[][cantidad]" class="form-control" value="${prod.cantidad}"></td>
                                                <td><button class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">X</button></td>
                                            </tr>
                                        `
                            );
                        });

                        $('#modalEditarPedido').modal('show');
                    } else {
                        Swal.fire('Error', res.mensaje, 'error');
                    }
                }
            });
        }


        $('#agregarProductoEditar').click(function () {
            $('#tablaProductosEditar tbody').append(
                `
                        <tr>
                            <td>
                                <select name="productos[][id]" class="form-control">
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="productos[][cantidad]" class="form-control" min="1" value="1"></td>
                            <td><button class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">X</button></td>
                        </tr>
                    `
            );
        });


        $('#guardarCambios').click(function () {
            const pedidoId = $('#pedido_id').val();
            const productos = [];

            $('#tablaProductosEditar tbody tr').each(function () {
                const productoId = $(this).find('select[name="productos[][id]"]').val();
                const cantidad = $(this).find('input[name="productos[][cantidad]"]').val();

                if (productoId && cantidad > 0) {
                    productos.push({
                        id: productoId,
                        cantidad: cantidad
                    });
                }
            });

            if (productos.length === 0) {
                Swal.fire('Atención', 'Debes agregar al menos un producto', 'warning');
                return;
            }

            $.ajax({
                url: '/pedidos/' + pedidoId + '/actualizar',
                type: 'PUT',
                data: JSON.stringify({
                    productos: productos
                }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.estado) {
                        Swal.fire('Éxito', res.mensaje, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.mensaje, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                }
            });
        });


        //ver detalle #

        function verDetallePedido(id) {
            $.ajax({
                // url: '/pedidos/' + id + '/obtener',
                url: "{{ url('pedidos') }}/" + id + "/obtener",
                type: 'GET',
                success: function (res) {
                    if (res.estado) {
                        const pedido = res.pedido;
                        const tbody = $('#tablaDetallePedido tbody');
                        tbody.empty();

                        pedido.productos.forEach(prod => {
                            tbody.append(`
                                        <tr>
                                            <td>${prod.nombre ?? prod.producto_id}</td>
                                            <td>${prod.cantidad}</td>
                                        </tr>
                                    `);
                        });

                        $('#modalDetallePedido').modal('show');
                    } else {
                        Swal.fire('Error', res.mensaje, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo obtener el detalle del pedido', 'error');
                }
            });
        }
    </script>
@endsection