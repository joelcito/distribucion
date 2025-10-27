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
    <div class="modal fade" id="modalIngreso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de <span id="text_tipoo_modal" class="text-info"></span></h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioIngresoSalida">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Monto</label>
                                    <input type="number" id="monto" name="monto"
                                        class="form-control form-control-solid mb-3 mb-lg-0" min="0.1" step="0.01"
                                        value="0" required>
                                    <input type="hidden" id="tipo" name="tipo" required>
                                    <input type="hidden" value="{{ $cajaAbierta != null ? $cajaAbierta->id : 0 }}"
                                        id="caja_abierto_ingre_cerra" name="caja_abierto_ingre_cerra" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" id="descripcion" name="descripcion"
                                        class="form-control form-control-solid mb-3 mb-lg-0">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarTipoIngresoSalida()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->


    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxlg">

                <!--begin::Card-->
                <div class="card">
                    <div class="card-header flex-wrap bg-light-info py-4">
                        <h3
                            class="card-title page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                            LISTADO DE PAGOS</h3>
                        <div class="card-toolbar">
                            @if ($cajaAbierta)
                                <button type="button" class="btn btn-sm fw-bold btn-success ml-5"
                                    onclick="modalIngresoSalida('INGRESO')">
                                    <i class="fas fa-money-bill"></i><i class="fas fa-arrow-down"></i>Nuevo Ingreso</button>

                                <button type="button" class="btn btn-sm fw-bold btn-danger ml-5"
                                    onclick="modalIngresoSalida('SALIDA')">
                                    <i class="fas fa-money-bill"></i><i class="fas fa-arrow-up"></i>Nuevo Salida</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <form id="formulario_busqueda">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Sucursal</label>
                                        <select data-control="select2" data-placeholder="Seleccione"
                                            class="form-select form-select-solid fw-bold"
                                            class="form-control form-control-sm" name="sucursal_id" id="sucursal_id">
                                            <option value="">SELECCIONE</option>
                                            @foreach ($sucursales as $sucursal)
                                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Fecha Ini</label>
                                        <input type="date" class="form-control form-control-sm" id="fecha_ini"
                                            name="fecha_ini" value="{{ $fechaIni }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Fecha Fin</label>
                                        <input type="date" class="form-control form-control-sm" id="fecha_fin"
                                            name="fecha_fin" value="{{ $fechaFin }}">
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Caja</label>
                                        <select data-control="select2" data-placeholder="Seleccione"
                                            class="form-select form-select-solid fw-bold"
                                            class="form-control form-control-sm" name="caja_id" id="caja_id">
                                            <option value="">SELECCIONE</option>
                                            @foreach ($cajasRangoFecha as $caja)
                                                <option value="{{ $caja->id }}">{{ $caja->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-md-3">
                                    <div class="fv-row mb-7">
                                        <label class="fw-semibold fs-6 mb-2">Usuario</label>
                                        <select data-control="select2" data-placeholder="Seleccione"
                                            class="form-select form-select-solid fw-bold"
                                            class="form-control form-control-sm" name="usuario_id" id="usuario_id">
                                            <option value="">SELECCIONE</option>
                                            @foreach ($usuarios as $usuario)
                                                <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button onclick="ajaxListado()" type="button"
                                        class="btn btn-sm w-100 btn-success mt-8"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                        <div id="table_listado">

                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

@stop()

@section('js')
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
            // Mostrar SweetAlert2 antes de enviar la solicitud
            // Swal.fire({
            //     title: 'Generando Listado...',
            //     text: 'Por favor espera mientras generamos el listado.',
            //     allowOutsideClick: false, // Evitar que se cierre al hacer clic fuera
            //     didOpen: () => {
            //         Swal.showLoading(); // Mostrar el spinner de carga
            //     }
            // });

            let datos = $('#formulario_busqueda').serializeArray();
            $.ajax({
                url: "{{ url('pago/ajaxListado') }}",
                method: "POST",
                data: datos,
                success: function(resultado) {
                    if (resultado.estado) {
                        $('#table_listado').html(resultado.data.listado)
                    } else {

                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.',
                    });
                }
            })
        }

        function limpiarErorres() {
            $(".invalid-feedback").remove();
            $(".is-invalid").removeClass("is-invalid");
        }

        function modalNuevoRol() {
            limpiarErorres();

            $('#id').val(0)
            $('#nombre').val('')
            $('#modalRol').modal('show')
        }

        // function guardarRol() {
        //     let datos = $('#formularioRol').serializeArray();
        //     $.ajax({
        //         url: "{{ route('rol.guardarRol') }}",
        //         method: "POST",
        //         data: datos,
        //         success: function(resultado) {
        //             if (resultado.estado) {
        //                 Swal.fire({
        //                     title: "EL REGISTRO FUE EXITOSO.",
        //                     icon: "success",
        //                     timer: 3000, // Se cierra en 3 segundos
        //                     showConfirmButton: false
        //                 });
        //                 ajaxListado();
        //                 $('#modalRol').modal('hide')
        //             } else {

        //             }
        //         },
        //         error: function(xhr) {
        //             limpiarErorres();

        //             if (xhr.status === 422) {
        //                 let errores = xhr.responseJSON.errors;

        //                 for (let campo in errores) {
        //                     let mensaje = errores[campo][0];

        //                     let input = $(`[name="${campo}"]`);
        //                     input.addClass("is-invalid");
        //                     input.after(`<div class="invalid-feedback">${mensaje}</div>`);
        //                 }
        //             } else {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'Error',
        //                     text: 'Ocurrió un error inesperado.',
        //                 });
        //             }
        //         }
        //     });
        // }

        // function editarRol(rol) {
        //     limpiarErorres();

        //     Object.keys(rol).forEach(key => {
        //         let input = $(`#${key}`);
        //         if (input.length) {
        //             input.val(rol[key]);
        //         }
        //     });
        //     $('#modalRol').modal('show')
        // }

        // function eliminarRol(rol) {
        //     Swal.fire({
        //         title: "Quieres eliminar " + rol.nombre,
        //         text: "Ya no podras recuperarlo!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Si, borrar!",
        //         cancelButtonText: "No, cancelar!",
        //         reverseButtons: true
        //     }).then(function(result) {
        //         if (result.value) {
        //             $.ajax({
        //                 url: "{{ route('rol.eliminarRol') }}",
        //                 method: "POST",
        //                 data: rol,
        //                 success: function(resultado) {
        //                     if (resultado.estado) {
        //                         ajaxListado();
        //                     }
        //                 },
        //                 error: function(xhr) {
        //                     Swal.fire({
        //                         icon: 'error',
        //                         title: 'Error',
        //                         text: 'Ocurrió un error inesperado.',

        //                     });
        //                 }
        //             });
        //         } else if (result.dismiss === "cancel") {
        //             Swal.fire(
        //                 "Cancelado",
        //                 "La operacion fue cancelada",
        //                 "error"
        //             )
        //         }
        //     });

        // }

        function modalIngresoSalida(tipo) {
            $('#tipo').val(tipo)
            $('#text_tipoo_modal').text(tipo)
            $('#modalIngreso').modal('show')
            $('#monto').val(0)
            $('#descripcion').val('')
        }

        function guardarTipoIngresoSalida() {
            if ($("#formularioIngresoSalida")[0].checkValidity()) {
                datos = $("#formularioIngresoSalida").serializeArray()
                $.ajax({
                    url: "{{ url('pago/guardarTipoIngresoSalida') }}",
                    data: datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.estado) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Se registro con exito',
                                showConfirmButton: false, // No mostrar botón de confirmación
                                timer: 2000, // 5 segundos
                                timerProgressBar: true
                            });
                            $('#modalIngreso').modal('hide')
                            ajaxListado();
                        }
                    }
                });
            } else {
                $("#formularioIngresoSalida")[0].reportValidity()
            }
        }
    </script>
@endsection
