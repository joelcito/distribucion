@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
    <div class="modal fade" id="modalProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="fw-bold">FORMULARIO DE PROVEEDOR</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioProveedor">
                        <input type="hidden" name="id" id="id" value="0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre"
                                        name="nombre">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">NIT</label>
                                    <input type="text" class="form-control form-control-sm" id="nit"
                                        name="nit">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Razón Social</label>
                                    <input type="text" class="form-control form-control-sm" id="razon_social"
                                        name="razon_social">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Dirección</label>
                                    <input type="text" class="form-control form-control-sm" id="direccion"
                                        name="direccion">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Celular</label>
                                    <input type="text" class="form-control form-control-sm" id="celular"
                                        name="celular">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success w-100" onclick="guardarProveedor()">Guardar</button>
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
                            <h3 class="fw-bold">Listado de Proveedores</h3>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProveedor"
                                onclick="limpiarFormularioProveedor()">Agregar Proveedor</button>
                        </div>
                    </div>
                    <div class="card-body" id="listadoProveedores">
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
            cargarListadoProveedores();
        });

        function cargarListadoProveedores() {
            $.ajax({
                url: '{{ route('proveedor.ajaxListado') }}',
                type: 'POST',
                data: {},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado && response.data.listado) {
                        $('#listadoProveedores').html(response.data.listado);
                    } else {
                        $('#listadoProveedores').html(
                            '<div class="alert alert-danger">No se pudo cargar el listado</div>');
                    }
                },
                error: function() {
                    $('#listadoProveedores').html('<div class="alert alert-danger">Error de conexión</div>');
                }
            });
        }

        function limpiarFormularioProveedor() {
            $('#id').val(0);
            $('#nombre').val('');
            $('#nit').val('');
            $('#razon_social').val('');
            $('#direccion').val('');
            $('#celular').val('');
        }

        function guardarProveedor() {
            var id = $('#id').val();
            var nombre = $('#nombre').val();
            var nit = $('#nit').val();
            var razon_social = $('#razon_social').val();
            var direccion = $('#direccion').val();
            var celular = $('#celular').val();
            $.ajax({
                url: '{{ route('proveedor.guardarProveedor') }}',
                type: 'POST',
                data: {
                    id: id,
                    nombre: nombre,
                    nit: nit,
                    razon_social: razon_social,
                    direccion: direccion,
                    celular: celular
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        $('#modalProveedor').modal('hide');
                        cargarListadoProveedores();
                    } else {
                        alert(response.message || 'Error al guardar');
                    }
                },
                error: function() {
                    alert('Error de conexión');
                }
            });
        }

        function editarProveedor(proveedor) {
            $('#id').val(proveedor.idproveedores);
            $('#nombre').val(proveedor.nombre);
            $('#nit').val(proveedor.nit);
            $('#razon_social').val(proveedor.razon_social);
            $('#direccion').val(proveedor.direccion);
            $('#celular').val(proveedor.celular);
            $('#modalProveedor').modal('show');
        }

        function eliminarProveedor(id) {
            if (!confirm('¿Está seguro de eliminar el proveedor?')) return;
            $.ajax({
                url: '{{ route('proveedor.eliminarProveedor') }}',
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        cargarListadoProveedores();
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
