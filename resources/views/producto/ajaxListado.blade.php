<div style="overflow-x: auto;">
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_productos">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Código</th>
                <th>Nombre</th>
                <th>Proveedor</th>
                {{-- <th>Precio Compra</th>
                <th>Precio Venta</th> --}}
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ($productos as $producto)
                <tr>
                    <td>{{ $producto->codigo }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->proveedor->nombre ?? '' }}</td>
                    {{-- <td>{{ $producto->precio_compra }}</td>
                    <td>{{ $producto->precio_venta }}</td> --}}
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown"
                                data-bs-display="static" aria-expanded="false">
                                Opciones
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                {{-- @if ($producto->tipo_producto == 'PRODUCTO') --}}
                                <li><button class="dropdown-item" type="button"
                                        onclick="adicionarStockSucursal({{ json_encode($producto) }})"><i
                                            class="fa fa-calendar-plus"></i> Stock-Sucursal</button></li>
                                <li><button class="dropdown-item" type="button"
                                        onclick="transferenciaSucursal({{ json_encode($producto) }})"><i
                                            class="fa fa-arrow-right"></i> Transferencia</button></li>
                                {{-- @endif --}}
                                <li>
                                    <!-- <button class="dropdown-item" type="button"
                                                        onclick="editarProducto({{ json_encode($producto) }})"><i
                                                            class="fa fa-edit"></i>
                                                        Editar</button> -->
                                    <button class="dropdown-item" type="button"
                                        onclick="abrirModalProducto({{ $producto->id }})">
                                        Editar
                                    </button>
                                </li>
                                <li><button class="dropdown-item" type="button"
                                        onclick="eliminarProducto('{{ $producto->idproductos }}')"><i
                                            class="fa fa-trash"></i>
                                        Eliminar</button></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <h4 class="text-danger">No hay datos</h4>
            @endforelse
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('#kt_table_productos').DataTable({
            lengthMenu: [10, 25, 50, 100],
            dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>',
            language: {
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros por página',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            order: [],
            responsive: true
        });
    });

    let imagenesAEliminar = []; // ✅ Solo una vez
    let imagenesExistentes = []; // Para editar

    function abrirModalProducto(productoId) {
        console.log('ID a editar:', productoId); // 🔹 log para depuración
        $.ajax({
            url: '{{ route("producto.obtenerProducto") }}',
            type: 'POST',
            data: { id: productoId },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                console.log(res); // 🔹 ver qué responde el servidor
                if (res.estado) {
                    const p = res.data;
                    $('#id').val(p.id);
                    $('#nombre').val(p.nombre);
                    $('#codigo').val(p.codigo);
                    $('#proveedores_idproveedores').val(p.proveedor_id);
                    $('#categoria_id').val(p.categoria_id);
                    $('#precio_compra').val(p.precio_compra);
                    $('#precio_venta').val(p.precio_venta);
                    imagenesExistentes = p.imagenes || [];
                    imagenesAEliminar = [];
                    mostrarImagenes();
                    $('#modalProducto').modal('show');
                } else {
                    alert(res.message || 'Producto no encontrado');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText); // 🔹 ver detalle del error
                alert('Error de conexión');
            }
        });
    }


    function eliminarImagenExistente(index) {
        imagenesAEliminar.push(index);
        $(`#contenedorImagenesExistentes div:eq(${index})`).remove();
    }
</script>
