@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="fw-bold">FORMULARIO DE USUARIO</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioUsuario">
                        <input type="hidden" name="id" id="id" value="0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="name"
                                        name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="required fw-semibold fs-6 mb-2">Email</label>
                                    <input type="email" class="form-control form-control-sm" id="email"
                                        name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-semibold fs-6 mb-2">Contraseña</label>
                                    <input type="password" class="form-control form-control-sm" id="password"
                                        name="password">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success w-100" onclick="guardarUsuario()">Guardar</button>
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
                            <h3 class="fw-bold">Listado de Usuarios</h3>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario"
                                onclick="limpiarFormularioUsuario()">Agregar Usuario</button>
                        </div>
                    </div>
                    <div class="card-body" id="listadoUsuarios">
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
            cargarListadoUsuarios();
        });

        function cargarListadoUsuarios() {
            $.ajax({
                url: '{{ route('usuario.ajaxListado') }}',
                type: 'POST',
                data: {},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado && response.data.listado) {
                        $('#listadoUsuarios').html(response.data.listado);
                    } else {
                        $('#listadoUsuarios').html(
                            '<div class="alert alert-danger">No se pudo cargar el listado</div>');
                    }
                },
                error: function() {
                    $('#listadoUsuarios').html('<div class="alert alert-danger">Error de conexión</div>');
                }
            });
        }

        function limpiarFormularioUsuario() {
            $('#id').val(0);
            $('#name').val('');
            $('#email').val('');
            $('#rol_id').val('');
            $('#password').val('');
        }

        function guardarUsuario() {
            var id = $('#id').val();
            var name = $('#name').val();
            var email = $('#email').val();
            var rol_id = $('#rol_id').val();
            var password = $('#password').val();
            $.ajax({
                url: '{{ route('usuario.guardarUsuario') }}',
                type: 'POST',
                data: {
                    id: id,
                    name: name,
                    email: email,
                    rol_id: rol_id,
                    password: password
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        $('#modalUsuario').modal('hide');
                        cargarListadoUsuarios();
                    } else {
                        alert(response.message || 'Error al guardar');
                    }
                },
                error: function() {
                    alert('Error de conexión');
                }
            });
        }

        function editarUsuario(usuario) {
            $('#id').val(usuario.id);
            $('#name').val(usuario.name);
            $('#email').val(usuario.email);
            $('#rol_id').val(usuario.rol_id);
            $('#password').val('');
            $('#modalUsuario').modal('show');
        }

        function eliminarUsuario(id) {
            if (!confirm('¿Está seguro de eliminar el usuario?')) return;
            $.ajax({
                url: '{{ route('usuario.eliminarUsuario') }}',
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        cargarListadoUsuarios();
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
