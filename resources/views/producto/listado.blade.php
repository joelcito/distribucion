@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="fw-bold">FORMULARIO DE PRODUCTO</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioProducto">
                        <input type="hidden" name="id" id="id" value="0">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre" name="nombre">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Proveedor</label>
                                    <select class="form-control form-control-sm" id="proveedores_idproveedores"
                                        name="proveedores_idproveedores">
                                        <!-- Opciones de proveedores -->
                                        <option value="">Seleccione un proveedor</option>
                                        @php
                                            $proveedores = \App\Models\Proveedor::all();
                                        @endphp
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Categorias</label>
                                    <select class="form-control form-control-sm" id="categoria_id" name="categoria_id">
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach ($categotias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success w-100" onclick="guardarProducto()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal Stock de Sucursal-->
    <div class="modal fade" id="modalStockSucursal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">CANTIDAD STOCK POR SUCUSAL DEL PRODUCTO: <span class="text-info"
                            id="nombre_producto"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <div id="tabla_stock">
                    </div>
                </div>
                <div class="modal-footer">
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>


    <div class="modal fade" id="modalStockSucursalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">INGRESO DE STOCK: <span class="text-info" id="nombre_producto_stock"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioStockSucursal">
                        <input type="hidden" name="producto_id" id="producto_id">
                        <input type="hidden" name="sucursal_id" id="sucursal_id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Sucursal</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre_sucursal"
                                        name="nombre_sucursal" @readonly(true)>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ingreso la cantidad</label>
                                    <input type="number" min="1" step="any" class="form-control form-control-sm"
                                        id="cantidad_ingreso" name="cantidad_ingreso">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha de Registro</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha" name="fecha"
                                        value="{{ date('Y-m-d') }}" @readonly(true)>
                                    <div class="text-danger error-message" id="error-fecha"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="fw-semibold fs-6 mb-2">Descripcion</label>
                                <textarea class="form-control form-control-sm" name="descripcion" id="descripcion" cols="30"
                                    rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="lotes" class="form-label">Lote</label>
                                <input type="text" class="form-control" id="lotes" name="lotes">
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento">
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarIngreso()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>




    <div class="modal fade" id="modalSalidaSucursalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">SALIDA DE STOCK: <span class="text-info" id="nombre_producto_stock"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioSalidaSucursal">
                        <input type="hidden" name="producto_id" id="producto_id_salida">
                        <input type="hidden" name="sucursal_id" id="sucursal_id_salida">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 required">Sucursal</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre_sucursal_salida"
                                        name="nombre_sucursal" @readonly(true)>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ingreso la cantidad</label>
                                    <input type="number" min="1" step="any" class="form-control form-control-sm"
                                        id="cantidad_salida" name="cantidad_salida">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha de Registro</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha_salida" name="fecha"
                                        value="{{ date('Y-m-d') }}" @readonly(true)>
                                    <div class="text-danger error-message" id="error-fecha"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="fw-semibold fs-6 mb-2">Descripcion</label>
                                <textarea class="form-control form-control-sm" name="descripcion" id="descripcion_salida"
                                    cols="30" rows="3"></textarea>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarSalida()">Guardar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!--TRANFERENCIAS-->
    <div class="modal fade" id="modalTransferenciaSucursal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="fw-bold">TRANSFERENCIA DE STOCK: <span class="text-info"
                            id="nombre_producto_transferencia"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioTransferencia">
                        <input type="hidden" name="producto_id" id="producto_id_transferencia">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="fw-semibold">Sucursal Origen</label>
                                <select id="select_origen" class="form-control form-control-sm">
                                    <option value="">Seleccione una sucursal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold">Sucursal Destino</label>
                                <select id="select_destino" class="form-control form-control-sm">
                                    <option value="">Seleccione una sucursal</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="fw-semibold">Cantidad</label>
                                <input type="number" min="1" step="any" class="form-control form-control-sm"
                                    id="cantidad_transferencia">
                            </div>
                        </div>
                        <!-- <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label class="fw-semibold">Descripción</label>
                                                    <textarea class="form-control form-control-sm" id="descripcion_transferencia"
                                                        rows="3"></textarea>
                                                </div>
                                            </div> -->
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success w-100" onclick="guardarTransferencia()">Guardar
                                    Transferencia</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxlg">
                <div class="card shadow-sm">
                    <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold">Listado de Productos</h3>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalProducto" onclick="limpiarFormularioProducto()">
                                <i class="fa fa-plus"></i> Agregar Producto
                            </button>
                        </div>
                    </div>

                    <div class="card-body py-4" id="listadoProductos">
                        <!-- El listado se carga por AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(document).ready(function () {
            cargarListadoProductos();
        });

        function cargarListadoProductos() {
            $.ajax({
                url: '{{ route('producto.ajaxListado') }}',
                type: 'POST',
                data: {},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.estado && response.data.listado) {
                        $('#listadoProductos').html(response.data.listado);
                    } else {
                        $('#listadoProductos').html(
                            '<div class="alert alert-danger">No se pudo cargar el listado</div>');
                    }
                },
                error: function () {
                    $('#listadoProductos').html('<div class="alert alert-danger">Error de conexión</div>');
                }
            });
        }

        function limpiarFormularioProducto() {
            $('#id').val(0);
            $('#codigo').val('');
            $('#nombre').val('');
            $('#proveedores_idproveedores').val('');
            $('#precio_compra').val('');
            $('#precio_venta').val('');
        }

        function guardarProducto() {
            var id = $('#id').val();
            var codigo = $('#codigo').val();
            var nombre = $('#nombre').val();
            var proveedores_idproveedores = $('#proveedores_idproveedores').val();
            var precio_compra = $('#precio_compra').val();
            var precio_venta = $('#precio_venta').val();
            $.ajax({
                url: '{{ route('producto.guardarProducto') }}',
                type: 'POST',
                data: {
                    id: id,
                    codigo: codigo,
                    nombre: nombre,
                    proveedores_idproveedores: proveedores_idproveedores,
                    precio_compra: precio_compra,
                    precio_venta: precio_venta
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.estado) {
                        $('#modalProducto').modal('hide');
                        cargarListadoProductos();
                    } else {
                        alert(response.message || 'Error al guardar');
                    }
                },
                error: function () {
                    alert('Error de conexión');
                }
            });
        }

        function editarProducto(producto) {
            $('#id').val(producto.idproductos);
            $('#codigo').val(producto.codigo);
            $('#nombre').val(producto.nombre);
            $('#proveedores_idproveedores').val(producto.proveedores_idproveedores);
            $('#precio_compra').val(producto.precio_compra);
            $('#precio_venta').val(producto.precio_venta);
            $('#modalProducto').modal('show');
        }



        function eliminarProducto(id) {
            Swal.fire({
                title: "¿Quieres eliminar este producto?",
                text: "¡Ya no podrás recuperarlo!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, borrar!",
                cancelButtonText: "No, cancelar!",
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('producto.eliminarProducto') }}',
                        type: 'POST',
                        data: { id: id },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.estado) {
                                cargarListadoProductos();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'El producto fue eliminado correctamente',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Error al eliminar el producto'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error de conexión'
                            });
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        "Cancelado",
                        "La operación fue cancelada",
                        "error"
                    );
                }
            });
        }


        //ADICIONAR STOCK
        function adicionarStockSucursal(producto) {

            $('#nombre_producto').html('')
            $('#tabla_stock').html('');

            $('#nombre_producto_salida').html('')
            $.ajax({
                url: "{{ route('producto.ajaxStockSucursal') }}",
                method: "POST",
                data: {
                    producto_id: producto.id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (resultado) {

                    if (resultado.estado) {
                        $('#nombre_producto').html(producto.nombre);
                        $('#tabla_stock').html(resultado.data.listado);

                        $('#nombre_producto_salida').html(producto.nombre);

                        $('#modalStockSucursal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                    });
                }
            })
        }

        function adicionarStockSucursalProducto(sucursal, producto) {
            $('#nombre_producto_stock').html('')
            limpiarErorres();

            $('#nombre_producto_stock').html(producto.nombre)
            $('#nombre_sucursal').val(sucursal.nombre)
            $('#producto_id').val(producto.id)
            $('#sucursal_id').val(sucursal.id)
            $('#cantidad_ingreso').val('')
            $('#descripcion').val('')
            $('#lotes').val('')
            $('#fecha_vencimiento').val('')
            $('#modalStockSucursalProducto').modal('show')

        }

        function limpiarErorres() {
            $('.error-message').html('');
            $('.is-invalid').removeClass('is-invalid');
        }


        function guardarIngreso() {
            // Tomar los valores del modal
            var producto_id = $('#producto_id').val();
            var sucursal_id = $('#sucursal_id').val();
            var ingreso = $('#cantidad_ingreso').val();
            var descripcion = $('#descripcion').val();
            var lotes = $('#lotes').val();
            var fecha_vencimiento = $('#fecha_vencimiento').val();
            var fecha = $('#fecha').val(); // fecha de registro

            // Validaciones básicas
            if (!producto_id || !sucursal_id || !ingreso) {
                Swal.fire('Error', 'Debe completar los campos obligatorios', 'error');
                return;
            }

            // Enviar Ajax al controlador
            $.ajax({
                url: '{{ route("movimientos.guardarIngreso") }}', // Ajusta tu ruta
                type: 'POST',
                data: {
                    producto_id: producto_id,
                    sucursal_id: sucursal_id,
                    ingreso: ingreso,
                    descripcion: descripcion,
                    lotes: lotes,
                    fecha_vencimiento: fecha_vencimiento,
                    fecha: fecha
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.estado) {
                        // Cerrar ambos modales
                        $('#modalStockSucursalProducto').modal('hide');
                        $('#modalStockSucursal').modal('hide');

                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Ingreso registrado correctamente',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Opcional: recargar tabla de stock aquí
                        // cargarTablaStock();
                    } else {
                        Swal.fire('Error', response.message || 'No se pudo guardar el ingreso', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Ocurrió un error de conexión', 'error');
                }
            });
        }


        //SALIDAS STOCK
        function adicionarSalidaSucursal(producto) {

            $('#nombre_producto').html('')
            $('#tabla_stock').html('');

            $('#nombre_producto_salida').html('')
            $.ajax({
                url: "{{ route('producto.ajaxStockSucursal') }}",
                method: "POST",
                data: {
                    producto_id: producto.id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (resultado) {

                    if (resultado.estado) {
                        $('#nombre_producto').html(producto.nombre);
                        $('#tabla_stock').html(resultado.data.listado);
                        $('#nombre_producto_salida').html(producto.nombre);
                        $('#modalStockSucursal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                    });
                }
            })
        }

        function adicionarSalidaSucursalProducto(sucursal, producto) {
            $('#nombre_producto_stock').html('')
            limpiarErorres();

            $('#nombre_producto_stock').html(producto.nombre)
            $('#nombre_sucursal_salida').val(sucursal.nombre)
            $('#producto_id').val(producto.id)
            $('#sucursal_id').val(sucursal.id)
            $('#cantidad_salida').val('')
            $('#descripcion').val('')
            $('#fecha_salida').val(new Date().toISOString().split('T')[0]);

            $('#modalSalidaSucursalProducto').modal('show')

        }


        function guardarSalida() {

            var producto_id = $('#producto_id').val();
            var sucursal_id = $('#sucursal_id').val();
            var salida = $('#cantidad_salida').val();
            var descripcion = $('#descripcion').val();
            var lotes = $('#lotes').val();
            var fecha_vencimiento = $('#fecha_vencimiento').val();
            var fecha = $('#fecha_salida').val();


            if (!producto_id || !sucursal_id || !salida) {
                Swal.fire('Error', 'Debe completar los campos obligatorios', 'error');
                return;
            }


            $.ajax({
                url: '{{ route("movimientos.guardarSalida") }}',
                type: 'POST',
                data: {
                    producto_id: producto_id,
                    sucursal_id: sucursal_id,
                    salida: salida,
                    descripcion: descripcion,
                    lotes: lotes,
                    fecha_vencimiento: fecha_vencimiento,
                    fecha: fecha
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.estado) {

                        $('#modalSalidaSucursalProducto').modal('hide');
                        $('#modalStockSucursal').modal('hide');


                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Salida registrada correctamente',
                            timer: 1500,
                            showConfirmButton: false
                        });


                        // cargarTablaStock();
                    } else {
                        Swal.fire('Error', response.message || 'No se pudo guardar la salida', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Ocurrió un error de conexión', 'error');
                }
            });
        }


        function transferenciaSucursal(producto) {
            limpiarErroresTransferencia();

            $('#nombre_producto_transferencia').html(producto.nombre);
            $('#producto_id_transferencia').val(producto.id);
            $('#cantidad_transferencia').val('');
            $('#descripcion_transferencia').val('');


            $('#select_origen').html('<option value="">Seleccione una sucursal</option>');
            $('#select_destino').html('<option value="">Seleccione una sucursal</option>');


            $.ajax({
                url: '{{ route("sucursal.ajaxListado") }}',
                type: 'POST', // POST
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.estado) {
                        res.data.forEach(sucursal => {
                            $('#select_origen').append(`<option value="${sucursal.id}">${sucursal.nombre}</option>`);
                            $('#select_destino').append(`<option value="${sucursal.id}">${sucursal.nombre}</option>`);
                        });
                        $('#modalTransferenciaSucursal').modal('show');
                    } else {
                        Swal.fire('Error', 'No se pudieron obtener las sucursales', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error de conexión al obtener sucursales', 'error');
                }
            });
        }

        function limpiarErroresTransferencia() {
            $('#select_origen').val('');
            $('#select_destino').val('');
            $('#cantidad_transferencia').val('');
            // $('#descripcion_transferencia').val('');
        }

        function guardarTransferencia() {
            var producto_id = $('#producto_id_transferencia').val();
            var sucursal_origen = $('#select_origen').val();
            var sucursal_destino = $('#select_destino').val();
            var cantidad = $('#cantidad_transferencia').val();
            //var descripcion = $('#descripcion_transferencia').val();
            var fecha = new Date().toISOString().split('T')[0];

            if (!producto_id || !sucursal_origen || !sucursal_destino || !cantidad) {
                Swal.fire('Error', 'Debe completar todos los campos', 'error');
                return;
            }

            $.ajax({
                url: '{{ route("movimientos.transferencia") }}',
                type: 'POST',
                data: {
                    producto_id: producto_id,
                    sucursal_origen: sucursal_origen,
                    sucursal_destino: sucursal_destino,
                    cantidad: cantidad,
                    //   descripcion: descripcion,
                    fecha: fecha
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.estado) {
                        $('#modalTransferenciaSucursal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Transferencia registrada correctamente',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', response.message || 'No se pudo guardar la transferencia', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error de conexión', 'error');
                }
            });
        }

    </script>
@endsection