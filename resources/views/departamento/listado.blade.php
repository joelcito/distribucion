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
    <div class="modal fade" id="modalRol" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE DEPARTAMENTO <span class="text-info" id="nombre_busqueda"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioDepartamento">
                        <input type="hidden" name="id" id="id" value="0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre"
                                        name="nombre">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarRol()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalNuevoProvincia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE PROVINCIA <span class="text-info" id="nombre_busqueda"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioProvincia">
                        <input type="hidden" name="new_procincia_departameto" id="new_procincia_departameto" value="0">
                        <input type="hidden" name="provincia_id" id="provincia_id" value="0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre_provincia" name="nombre_provincia">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarProvincia()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalListaProvincias" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">LISTADO DE PROVINCIAS <span class="text-info" id="nombre_departamento"></span></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">

                    <div id="table_provincias">

                    </div>

                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarRol()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->


    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxlg">
                <div class="card shadow-sm">
                    <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold">Listado de Departamentos</h3>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevoRol()">
                                <i class="fa fa-plus"></i> Nuevo Departamento
                            </button>
                        </div>
                    </div>

                    <div class="card-body py-4" id="table_listado">
                        <!-- El listado se carga por AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop()

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            // definimos cabecera donde estarra el token y poder hacer nuestras operaciones de put,post...
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $(document).ready(function() {
            ajaxListado();
        });

        function ajaxListado(){
            let datos = {};
            $.ajax({
                url: "{{ route('departamento.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {

                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

                    }
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    // Swal.close();
                }
            })
        }

        function modalNuevoRol(){
            $('#modalRol').modal('show')
        }

        function modalNuevoProvincia(departamento){

            $('#new_procincia_departameto').val(departamento);
            $('#modalNuevoProvincia').modal('show')
            $('#modalListaProvincias').modal('hide')
        }

        function modalProvicias(departamento){
            let datos = {departamento_id:departamento};
            $.ajax({
                url: "{{ route('provincia.ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {

                    if (resultado.estado) {
                        $('#table_provincias').html(resultado.data.listado)
                        $('#modalListaProvincias').modal('show')
                    } else {

                    }
                    // Ocultar SweetAlert2 cuando la solicitud sea exitosa
                    // Swal.close();
                }
            })
        }

        function guardarProvincia(){

            let datos = $('#formularioProvincia').serializeArray();

             $.ajax({
                url: "{{ route('provincia.guardarProvincia') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 3000, // Se cierra en 3 segundos
                            showConfirmButton: false
                        });

                        let datos = {departamento_id:$('#new_procincia_departameto').val()};
                        $.ajax({
                            url: "{{ route('provincia.ajaxListado') }}",
                            method: "POST",
                            data: datos,
                            success: function(resultado) {

                                if (resultado.estado) {
                                    $('#table_provincias').html(resultado.data.listado);

                                    $('#modalNuevoProvincia').modal('hide')
                                    $('#modalListaProvincias').modal('show')

                                } else {

                                }
                            }
                        })
                    } else {

                    }
                },
                error: function(xhr) {
                    limpiarErorres();

                    if (xhr.status === 422) {
                        let errores = xhr.responseJSON.errors;

                        for (let campo in errores) {
                            let mensaje = errores[campo][0];

                            let input = $(`[name="${campo}"]`);
                            input.addClass("is-invalid");
                            input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                }
            });

        }



        function guardarRol(){
            let datos = $('#formularioDepartamento').serializeArray();

             $.ajax({
                url: "{{ route('departamento.guardarDepartamento') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        Swal.fire({
                            title: "EL REGISTRO FUE EXITOSO.",
                            icon: "success",
                            timer: 3000, // Se cierra en 3 segundos
                            showConfirmButton: false
                        });
                        ajaxListado();
                        $('#modalRol').modal('hide')
                    } else {

                    }
                },
                error: function(xhr) {
                    limpiarErorres();

                    if (xhr.status === 422) {
                        let errores = xhr.responseJSON.errors;

                        for (let campo in errores) {
                            let mensaje = errores[campo][0];

                            let input = $(`[name="${campo}"]`);
                            input.addClass("is-invalid");
                            input.after(`<div class="invalid-feedback">${mensaje}</div>`);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error inesperado.',
                        });
                    }
                }
            });
        }

        function editarRol(rol){
            $('#id').val(rol.id)
            $('#nombre').val(rol.nombre)
            $('#modalRol').modal('show')
        }

        function eliminarRol(rol) {
    Swal.fire({
        title: "¿Quieres eliminar " + rol.nombre + "?",
        text: "¡No podrás recuperarlo!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "Sí, borrar",
        cancelButtonText: "No, cancelar",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('rol.eliminarRol') }}",
                method: "POST",
                data: { rol: rol },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resultado) {
                    if (resultado.estado) {
                        ajaxListado(); // recarga el listado
                        Swal.fire(
                            'Eliminado!',
                            'El rol ha sido eliminado correctamente.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            resultado.message || 'No se pudo eliminar el rol.',
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.'
                    });
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire(
                'Cancelado',
                'La operación fue cancelada',
                'info'
            );
        }
    });
}

    </script>
@endsection
