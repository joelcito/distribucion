@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')



    <div class="d-flex">

        <!-- CATEGORIAS -->
        <div class="col-md-3">
            <div id="listaCategorias">
                @foreach ($categorias as $categoria)
                    <button class="btn btn-outline-primary mb-2 w-100 text-start categoria-btn" data-id="{{ $categoria->id }}">
                        {{ $categoria->nombre }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- GALERIA DE PRODUCTOS -->
        <div class="col-9 p-3" id="galeriaProductos">
            <div class="alert alert-info">Seleccione una categoría para ver productos</div>
        </div>

    </div>

    <!-- MODAL PRODUCTO (opcional) -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="fw-bold">Editar Producto</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formularioProducto">
                        <input type="hidden" id="id" name="id" value="0">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Nombre</label>
                                <input type="text" class="form-control form-control-sm" id="nombre" name="nombre">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">Proveedor</label>
                                <select class="form-control form-control-sm" id="proveedores_idproveedores"
                                    name="proveedores_idproveedores">
                                    <option value="">Seleccione proveedor</option>
                                    @foreach (\App\Models\Proveedor::all() as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="fw-semibold">Precio Compra</label>
                                <input type="number" class="form-control form-control-sm" id="precio_compra"
                                    name="precio_compra" step="0.01">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="fw-semibold">Precio Venta</label>
                                <input type="number" class="form-control form-control-sm" id="precio_venta"
                                    name="precio_venta" step="0.01">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-semibold">Imágenes del producto</label>
                                <input type="file" id="imagenes" name="imagenes[]" multiple accept="image/*"
                                    class="form-control form-control-sm">
                                <div id="contenedorImagenes" class="d-flex flex-wrap mt-2"></div>
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


@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>

        $(document).ready(function () {

            let defaultImg = "{{ asset('img/default.png') }}"; // Imagen por defecto
            let idCategoriaSeleccionada = null;

            // 1️⃣ Click en categorías
            $('.categoria-btn').click(function () {
                idCategoriaSeleccionada = $(this).data('id');
                console.log('Categoría seleccionada:', idCategoriaSeleccionada);
                cargarProductosPorCategoria(idCategoriaSeleccionada);
            });

            // 2️⃣ Función para cargar productos de la categoría
            function cargarProductosPorCategoria(idCategoria) {
                $('#galeriaProductos').html('<div>Cargando productos...</div>');

                $.ajax({
                    url: '{{ route("producto.ajaxPorCategoria") }}',
                    type: 'POST',
                    data: { categoria_id: idCategoria },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        console.log(res.data);

                        if (res.estado && Array.isArray(res.data) && res.data.length > 0) {
                            let html = '';

                            res.data.forEach(producto => {
                                let img = defaultImg;

                                if (producto.imagenes && producto.imagenes.length) {
                                    if (typeof producto.imagenes[0] === 'string') {
                                        img = "{{ url('/') }}/" + producto.imagenes[0]; // caso string
                                    } else if (typeof producto.imagenes[0] === 'object' && producto.imagenes[0].ruta) {
                                        img = "{{ url('/') }}/" + producto.imagenes[0].ruta; // caso objeto
                                    }
                                }

                                html += `
            <div class="card m-2" style="width: 150px; cursor:pointer;" onclick="abrirModalProducto(${producto.id})">
                <img src="${img}" class="card-img-top" alt="${producto.nombre}">
                <div class="card-body p-2">
                    <p class="card-text text-center">${producto.nombre}</p>
                </div>
            </div>
        `;
                            });

                            $('#galeriaProductos').html(`<div class="d-flex flex-wrap">${html}</div>`);
                        } else {
                            $('#galeriaProductos').html('<div class="alert alert-info">No hay productos en esta categoría</div>');
                        }
                    },
                    error: function () {
                        $('#galeriaProductos').html('<div class="alert alert-danger">Error de conexión</div>');
                    }
                });
            }

            // 3️⃣ Abrir modal producto para editar
            window.abrirModalProducto = function (productoId) {
                $.ajax({
                    url: '{{ route("producto.obtenerProducto") }}',
                    type: 'POST',
                    data: { id: productoId },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        if (res.estado) {
                            const p = res.data;
                            console.log('Producto cargado:', p);
                            // Aquí se puede abrir tu modal y llenar los campos
                            // $('#modalProducto').modal('show');
                        } else {
                            Swal.fire('Error', 'No se pudo cargar el producto', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    }
                });
            }

        });


    </script>
@endsection