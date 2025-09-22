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
    <div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h3 class="fw-bold">FORMULARIO DE CLIENTES</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioCliente">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-sm" id="nombres"
                                        name="nombres">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ap. Paterno</label>
                                    <input type="text" class="form-control form-control-sm" id="ap_paterno"
                                        name="ap_paterno">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Ap. Materno</label>
                                    <input type="text" class="form-control form-control-sm" id="ap_materno"
                                        name="ap_materno">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Cedula</label>
                                    <input type="text" class="form-control form-control-sm" id="cedula"
                                        name="cedula">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Complemento</label>
                                    <input type="text" class="form-control form-control-sm" id="complemento"
                                        name="complemento">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Nit</label>
                                    <input type="text" class="form-control form-control-sm" id="nit"
                                        name="nit">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Razon Social</label>
                                    <input type="text" class="form-control form-control-sm" id="razon_social"
                                        name="razon_social">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Correo</label>
                                    <input type="text" class="form-control form-control-sm" id="correo"
                                        name="correo">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Celular</label>
                                    <input type="text" class="form-control form-control-sm" id="numero_celular"
                                        name="numero_celular">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Codigo Cliente</label>
                                    <input type="text" class="form-control form-control-sm" id="codigo_cliente" name="codigo_cliente">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Nombre de farmacia</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre_farmacia" name="nombre_farmacia">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Departamento</label>
                                    <select name="departamento_id" id="departamento_id" class="form-control form-control-sm">
                                        <option value="">SELECCIONE</option>
                                        @foreach ($departamentos as $d)
                                            <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Provincia</label>
                                    <select name="provincia_id" id="provincia_id" class="form-control form-control-sm">
                                        <option value="">SELECCIONE</option>
                                        @foreach ($previncias as $p)
                                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Direccion</label>
                                    <input type="text" class="form-control form-control-sm" id="direccion" name="direccion">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm w-100 btn-success" onclick="guardarCliente()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxlg">
            <div class="card shadow-sm">
                <div class="card-header bg-light-info py-4 d-flex align-items-center justify-content-between">
                    <h3 class="card-title fw-bold">Listado de Clientes</h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalNuevoCliente()">
                            <i class="fa fa-plus"></i> Nuevo Registro
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

        function ajaxListado() {

            let datos = {};
            $.ajax({
                url: "{{ route('cliente.ajaxListado') }}",
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

        function limpiarErorres() {
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function modalNuevoCliente() {
            limpiarErorres();

            $('#id').val(0)
            $('#nombres').val('')
            $('#ap_paterno').val('')
            $('#ap_materno').val('')
            $('#cedula').val('')
            $('#complemento').val('')
            $('#nit').val('')
            $('#razon_social').val('')
            $('#correo').val('')
            $('#numero_celular').val('')
            $('#modalCliente').modal('show')
        }

        function guardarCliente() {

            if ($('#formularioCliente')[0].checkValidity()) {
                let datos = $('#formularioCliente').serializeArray();
                $.ajax({
                    url: "{{ route('cliente.guardarCliente') }}",
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
                            $('#modalCliente').modal('hide')
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
            } else {
                $('#formularioCliente')[0].reportValidity();
            }

        }

        function editarCliente(cliente) {
            limpiarErorres();

            Object.keys(cliente).forEach(key => {
                let input = $(`#${key}`);
                if (input.length) {
                    input.val(cliente[key]);
                }
            });
            $('#modalCliente').modal('show')
        }

        function eliminarCliente(cliente) {
    Swal.fire({
        title: "¿Quieres eliminar " + cliente.nombres + "?",
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
                url: "{{ route('cliente.eliminarCliente') }}",
                method: "POST",
                data: cliente,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resultado) {
                    if (resultado.estado) {
                        ajaxListado(); // recarga el listado de clientes
                        Swal.fire(
                            'Eliminado!',
                            'El cliente ha sido eliminado correctamente.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error',
                            resultado.message || 'No se pudo eliminar el cliente.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error',
                        'Ocurrió un error de conexión.',
                        'error'
                    );
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
