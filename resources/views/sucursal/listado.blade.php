@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .tamanio_boton {
            font-size: 6px;
        }
    </style>
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalSucursal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE SUCURSAL <span class="text-info" id="nombre_busqueda"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioSucursal">
                        <input type="hidden" name="id" id="id" value="0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Código Sucursal</label>
                                    <input type="text" class="form-control form-control-sm" id="codigo_sucursal"
                                        name="codigo_sucursal">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre"
                                        name="nombre">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Dirección</label>
                                    <input type="text" class="form-control form-control-sm" id="direccion"
                                        name="direccion">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarSucursal()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal - Add task-->
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxlg">
                <div class="card">
                    <div class="card-header flex-wrap bg-light-info py-4">
                        <div id="kt_app_toolbar_container" class="app-container container-xxlg d-flex flex-stack">
                            <h3 class="fw-bold">Listado de Sucursales</h3>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSucursal"
                                onclick="limpiarFormularioSucursal()">Agregar Sucursal</button>
                        </div>
                    </div>
                    <div class="card-body" id="listadoSucursales">
                        <!-- El listado se carga por AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            cargarListadoSucursales();
        });

        function cargarListadoSucursales() {
            $.ajax({
                url: '{{ route('sucursal.ajaxListado') }}',
                type: 'POST',
                data: {},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado && response.data.listado) {
                        $('#listadoSucursales').html(response.data.listado);
                    } else {
                        $('#listadoSucursales').html(
                            '<div class="alert alert-danger">No se pudo cargar el listado</div>');
                    }
                },
                error: function() {
                    $('#listadoSucursales').html('<div class="alert alert-danger">Error de conexión</div>');
                }
            });
        }

        function limpiarFormularioSucursal() {
            $('#id').val(0);
            $('#codigo_sucursal').val('');
            $('#nombre').val('');
            $('#direccion').val('');
        }

        function guardarSucursal() {
            var id = $('#id').val();
            var codigo_sucursal = $('#codigo_sucursal').val();
            var nombre = $('#nombre').val();
            var direccion = $('#direccion').val();

            $.ajax({
                url: '{{ route('sucursal.guardarSucursal') }}',
                type: 'POST',
                data: {
                    id: id,
                    codigo_sucursal: codigo_sucursal,
                    nombre: nombre,
                    direccion: direccion
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        $('#modalSucursal').modal('hide');
                        cargarListadoSucursales();
                    } else {
                        alert(response.message || 'Error al guardar');
                    }
                },
                error: function() {
                    alert('Error de conexión');
                }
            });
        }

        function editarSucursal(sucursal) {
            $('#id').val(sucursal.idsucursales);
            $('#codigo_sucursal').val(sucursal.codigo_sucursal);
            $('#nombre').val(sucursal.nombre);
            $('#direccion').val(sucursal.direccion);
            $('#modalSucursal').modal('show');
        }

        function eliminarSucursal(id) {
            if (!confirm('¿Está seguro de eliminar la sucursal?')) return;
            $.ajax({
                url: '{{ route('sucursal.eliminarSucursal') }}',
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        cargarListadoSucursales();
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
@endsection
