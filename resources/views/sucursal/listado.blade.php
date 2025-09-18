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
  
    <div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Sucursales</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSucursal" onclick="limpiarFormularioSucursal()">
                            <i class="fa fa-plus"></i> Agregar Sucursal
                        </button>
                    </div>
                </div>
                <div class="card-body" id="listadoSucursales">
                    
                </div>
            </div>
        </div>
    </div>
</div>


@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            cargarListadoSucursales();
        });

        // function cargarListadoSucursales() {
        //     $.ajax({
        //         url: '{{ route('sucursal.ajaxListado') }}',
        //         type: 'POST',
        //         data: {},
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function(response) {
        //             if (response.estado && response.data.listado) {
        //                 $('#listadoSucursales').html(response.data.listado);
        //             } else {
        //                 $('#listadoSucursales').html(
        //                     '<div class="alert alert-danger">No se pudo cargar el listado</div>');
        //             }
        //         },
        //         error: function() {
        //             $('#listadoSucursales').html('<div class="alert alert-danger">Error de conexión</div>');
        //         }
        //     });
        // }

        function cargarListadoSucursales() {
    $.ajax({
        url: '{{ route('sucursal.ajaxListado') }}',
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            if (response.estado) {
                let html = '';
                if(response.data.length > 0){
                    html += '<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_sucursales">';
                    html += '<thead><tr><th>Código</th><th>Nombre</th><th>Dirección</th><th>Acciones</th></tr></thead><tbody>';
                    response.data.forEach(function(sucursal){
                        html += `<tr>
                            <td>${sucursal.codigo_sucursal}</td>
                            <td>${sucursal.nombre}</td>
                            <td>${sucursal.direccion}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick='editarSucursal(${JSON.stringify(sucursal)})'><i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm" onclick='eliminarSucursal(${sucursal.id})'><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                } else {
                    html = '<div class="alert alert-info">No hay sucursales registradas</div>';
                }
                $('#listadoSucursales').html(html);
                $('#kt_table_sucursales').DataTable();
            } else {
                $('#listadoSucursales').html('<div class="alert alert-danger">'+response.message+'</div>');
            }
        },
        error: function(xhr){
            $('#listadoSucursales').html('<div class="alert alert-danger">Error de conexión: '+xhr.status+'</div>');
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
            $('#id').val(sucursal.id);
            $('#codigo_sucursal').val(sucursal.codigo_sucursal);
            $('#nombre').val(sucursal.nombre);
            $('#direccion').val(sucursal.direccion);
            $('#modalSucursal').modal('show');
        }

        function eliminarSucursal(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "¡No podrá revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('sucursal.eliminarSucursal') }}',
                type: 'POST',
                data: { id: id },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.estado) {
                        cargarListadoSucursales(); // recarga la tabla
                        Swal.fire(
                            'Eliminada!',
                            response.message,
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            response.message || 'No se pudo eliminar',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error',
                        'Error de conexión',
                        'error'
                    );
                }
            });
        }
    });
}
    </script>
@endsection
@endsection
