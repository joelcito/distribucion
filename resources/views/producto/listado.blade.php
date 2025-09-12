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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Código</label>
                                    <input type="text" class="form-control form-control-sm" id="codigo"
                                        name="codigo">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre"
                                        name="nombre">
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                            <option value="{{ $proveedor->idproveedores }}">{{ $proveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Precio Compra</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                        id="precio_compra" name="precio_compra">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Precio Venta</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm"
                                        id="precio_venta" name="precio_venta">
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
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxlg">
                <div class="card">
                    <div class="card-header flex-wrap bg-light-info py-4">
                        <div class="d-flex flex-stack">
                            <h3 class="fw-bold">Listado de Productos</h3>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto"
                                onclick="limpiarFormularioProducto()">Agregar Producto</button>
                        </div>
                    </div>
                    <div class="card-body" id="listadoProductos">
                        <!-- El listado se carga por AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
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
                success: function(response) {
                    if (response.estado && response.data.listado) {
                        $('#listadoProductos').html(response.data.listado);
                    } else {
                        $('#listadoProductos').html(
                            '<div class="alert alert-danger">No se pudo cargar el listado</div>');
                    }
                },
                error: function() {
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
                success: function(response) {
                    if (response.estado) {
                        $('#modalProducto').modal('hide');
                        cargarListadoProductos();
                    } else {
                        alert(response.message || 'Error al guardar');
                    }
                },
                error: function() {
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
            if (!confirm('¿Está seguro de eliminar el producto?')) return;
            $.ajax({
                url: '{{ route('producto.eliminarProducto') }}',
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        cargarListadoProductos();
                    } else {
                        alert(response.message || 'Error al eliminar');
                    }
                },
                error: function() {
                    alert('Error de conexión');
                }
            });
        }
    </script>
@endsection
