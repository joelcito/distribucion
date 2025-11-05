<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DETALLE DEL PEDIDO</title>
    <style type="text/css">
        @page {
            margin: 15px;
        }

        body {
            background-repeat: no-repeat;
            font-size: 13px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        .titulos {
            font-size: 18pt;
        }

        .subtitulos {
            font-size: 14pt;
        }

        table.datos {
            font-size: 11px;
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            position: absolute;
            top: 150px;
            left: 25px;
            width: 720px;
        }

        .datos th {
            height: 25px;
            background-color: #f5f5f5;
            color: #000000;
        }

        .datos td {
            font-size: 8pt;
            height: 20px;
        }

        .datos th,
        .datos td {
            border: 1px solid #000000;
            padding: 2px;
        }

        .datos tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table.contenidos {
            /*font-size: 13px;*/
            line-height: 14px;
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        .contenidos th {
            height: 50px;
            background-color: #616362;
            color: #fff;
        }

        .contenidos td {
            height: 10px;
        }

        .contenidos th,
        .contenidos td {
            border-bottom: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }

        /*ESTILOS NUEVOS PARA LAS TABLAS */
        #table_casa_matriz {
            position: absolute;
            width: 180px;
            /* margin-left: 20px;
             */
            /* text-align: center; */
            font-size: 9px;
            margin-top: 20px;
        }

        #table_casa_matriz2 {
            /*position: relative;*/
            width: 180px;
            margin-left: 130px;

            /* text-align: center; */
            font-size: 9px;
            margin-top: 120px;
        }

        #table_nuew_num_fac {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 9px;
            width: 50px;
        }

        #table_nuew_num_fac2 {
            position: absolute;
            right: 10px;
            top: 120px;
            margin-right: 100px;
            font-size: 9px;
            width: 50px;
        }

        #table_nit_num_fac {
                {
                    {
                    -- background-color: pink;
                    --
                }
            }

            position: absolute;
            max-width: 100px;
            right: -20px;
            top: 20px;
            text-align: left;
            font-size: 11px;
        }



        #logo_factura {
            position: absolute;
            right: 300px;
            top: 20px;
            font-size: 11px;
            width: 120px;
        }

        .estatico {
            width: 120px;
            height: 50px;
            word-wrap: break-word;
        }

        #TableFactura {
            position: absolute;
            top: 30px;
            text-align: center;
            width: 780px;
        }

        #table_datos_factura {
            position: absolute;
            width: 350px;
            margin-left: 20px;
            margin-top: 220px;
            text-align: center;
            font-size: 11px;
            text-align: left;
        }

        #table_datos_factura1 {
            position: absolute;
            width: 300px;
            right: -20px;
            top: 220px;
            font-size: 11px;
        }

        #anulado {
            position: absolute;
            font-size: 75px;
            color: rgb(227, 142, 142, 0.8);
            font-weight: bold;
            top: 35%;
            left: 20%;
            transform: rotate(-45deg);
        }
    </style>
</head>

<body>
    @php
        // $documento_sector      = $factura->siat_tipo_documento_sector;
        // $tipo_documento_sector = $documento_sector->codigo_clasificador;

        // $documento_sector = $factura->siat_tipo_documento_sector;
        $tipo_documento_sector = 1;

        // dd($documento_sector, $tipo_documento_sector);

    @endphp



    <table id="table_casa_matriz">
        @php
            // Obtener el cliente directamente desde el modelo usando cliente_id del pedido
            $cliente = \App\Models\Cliente::find($pedido->cliente_id);
        @endphp
        <tr>
            <td><b>NOMBRE FARMACIA:</b></td>
            <td width="120px">{{ $cliente->nombre_farmcia ?? 'Sin nombre' }}</td>
        </tr>
        <tr>
            <td><b>DIRECCIÓN CLIENTE:</b></td>
            <td width="120px">{{ $cliente->ubicacion ?? 'Sin dirección' }}</td>
        </tr>
        <tr>
            <td><b>TELEFONO CLIENTE:</b></td>
            <td width="120px">{{ $cliente->numero_celular ?? 'Sin teléfono' }}</td>
        </tr>
    </table>

    <table id="table_casa_matriz2">
        <tr>
            <td><b>DEPARTAMENTO:</b></td>
            <td width="120px">
                {{ optional(optional(optional($pedido->cliente)->provincia)->departamento)->nombre ?? 'SIN DEPARTAMENTO' }}
            </td>
        </tr>
    </table>

    <table id="table_nuew_num_fac2">
        <tr>
            <td><b>PROVINCIA:</b></td>
            <td width="120px">
                {{ optional(optional($pedido->cliente)->provincia)->nombre ?? 'SIN PROVINCIA' }}
            </td>
        </tr>

    </table>

    <table id="table_nuew_num_fac">
        @php
            // Obtener el cliente directamente desde el modelo usando cliente_id del pedido
            $users = \App\Models\User::find($pedido->usuario_creador_id);
        @endphp
        <tr>
            <td><b>N° DE NOTA</b></td>

        </tr>
        <tr>
            <td><b>FECHA</b></td>
            <td width="100px">{{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><b>NOMBRE VENDEDOR</b></td>
            <td width="100px">{{ $pedido->name }}</td>
        </tr>
        <tr>
            <td><b>TIPO</b></td>
            <td width="100px">{{ $pedido->tipo }}</td>
        </tr>

    </table>

    <table id="TableFactura">
        <thead>
            <tr>
                <th style="font-size: 12;">
                    DETALLE DEL PEDIDO
                    <br>
                    <img src="{{ public_path('assets/img/image.png') }}" alt="" width="25%">
                </th>
            </tr>
        </thead>
        {{-- <tbody>
            <tr>
                <td style="font-size: 8;">
                    (Con Derecho a Crédito Fiscal)
                </td>
            </tr>
        </tbody> --}}
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th><br>CANTIDAD<br><br></th>
                <th>PRODUCTOS</th>
                <th>CATEGORIA</th>
                <th>PRECIO UNITARIO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                // Si por alguna razón sigue siendo string, conviértelo en array
                $detalles = is_array($pedido->pedidos_productos) ? $pedido->pedidos_productos : json_decode($pedido->pedidos_productos, true);
            @endphp

            @foreach ($detalles as $detalle)
                @php
                    $cantidad = $detalle['cantidad'] ?? 0;

                    $nombre_producto = \App\Models\Producto::find($detalle['producto_id'])->nombre ?? 'Sin categoría';
                    $nombre_categoria = \App\Models\Categoria::find($detalle['categoria_id'])->nombre ?? 'Sin categoría';
                    $precio = $detalle['precio'] ?? 0;
                    $total += $cantidad * $precio;
                @endphp
                <tr>
                    <td>{{ $cantidad }}</td>
                    <td>{{ $nombre_producto}}</td>
                    <td>{{ $nombre_categoria }}</td>
                    <td>{{ number_format($precio, 2) }}</td>
                    <td>{{ number_format($cantidad * $precio, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align:right;">TOTAL Bs</td>
                <td>{{ number_format($total, 2) }}</td>
            </tr>
            <tr>
                <td style="border:none; background:white">
                    @php
                        $to        = $total;
                        $entero    = floor($to);
                        $decimal   = round(($to - $entero) * 100);
                        $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
                        $literal   = ucfirst($formatter->format($entero));
                        $centavos  = str_pad($decimal, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <b>Son: {{ $literal }} {{ $centavos }}/100 Bolivianos</b>
                </td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
