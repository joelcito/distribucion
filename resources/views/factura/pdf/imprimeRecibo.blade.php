<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACTURA</title>
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

        #table_casa_matriz2 {
            width: 180px;
            margin-left: 130px;
            font-size: 9px;
            margin-top: 120px;
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
            height: 20px;
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
            width: 50px;
            /* margin-left: 20px;
             */
            /* text-align: center; */
            font-size: 11px;
            margin-top: 20px;
        }

        #table_nuew_num_fac {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 11px;
            width: 50px;
        }

        #table_nit_num_fac {
            {{--  background-color: pink;  --}} position: absolute;
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

        #table_nuew_num_fac2 {
            position: absolute;
            right: 10px;
            top: 120px;
            margin-right: 100px;
            font-size: 9px;
            width: 50px;
        }
    </style>
</head>

<body>
    @php
        $tipo_documento_sector = 1;
    @endphp

    <table id="table_casa_matriz">
        <tr>
            <td><b>NOMBRE FARMACIA:</b></td>
            <td width="150px;">{{ $factura->cliente->nombre_farmcia }}</td>
        </tr>
        <tr>
            <td><b>DIRECCIÓN CLIENTE:</b></td>
            <td width="150px;">{{ $factura->cliente->ubicacion }}</td>
        </tr>
        <tr>
            <td><b>TELEFONO CLIENTE:</b></td>
            <td width="150px;">{{ $factura->cliente->numero_celular }}</td>
        </tr>
    </table>

    <table id="table_nuew_num_fac">
        <tr>
            <td><b>N° DE NOTA</b></td>
            <td width="100px">{{ $factura->numero_recibo }}</td>
        </tr>
        <tr>
            <td><b>FECHA</b></td>
            <td width="100px">{{ $factura->fecha }}</td>
        </tr>
        <tr>
            <td><b>NOMBRE VENDEDOR</b></td>
            <td width="100px">{{ $factura->usuarioCreador->name }}</td>
        </tr>
    </table>

    <table id="table_casa_matriz2">
        <tr>
            <td><b>LUGAR:</b></td>
            <td width="120px">
                {{ optional(optional(optional($factura->cliente)->provincia)->departamento)->nombre ?? '' }}
            </td>
        </tr>
    </table>

    {{-- <table id="table_nuew_num_fac2">
        <tr>
            <td><b>PROVINCIA:</b></td>
            <td width="120px">
                {{ optional(optional($factura->cliente)->provincia)->nombre ?? '' }}
            </td>
        </tr>
        </tr>
    </table> --}}

    <table id="TableFactura">
        <thead>
            <tr>
                <th style="font-size: 12;">
                    NOTA DE VENTA
                    <br>
                    <img src="{{ public_path('assets/img/image.png') }}" alt="" width="25%">
                </th>
            </tr>
        </thead>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th><br>CANTIDAD<br><br></th>
                <th>PRODUCTOS</th>
                <th><br>CATEGORIA<br><br></th>
                <th>PRECIO UNITARIO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $detalles = $factura->detalles;
            @endphp
            @foreach ($detalles as $detalle)
                @php
                    $total += $detalle->total;
                @endphp
                <tr>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td>{{ $detalle->producto->categoria->nombre }}</td>
                    <td>{{ $detalle->precio }}</td>
                    <td>{{ $detalle->total }}</td>
                </tr>
            @endforeach
            <tr style="align: right;">
                <td style="background: white; border: none;" colspan="3" rowspan="3">
                    @php
                        $to        = $factura->total;
                        $entero    = floor($to);
                        $decimal   = round(($to - $entero) * 100);
                        $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
                        $literal   = ucfirst($formatter->format($entero));
                        $centavos  = str_pad($decimal, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <b>Son: {{ $literal }} {{ $centavos }}/100 Bolivianos</b>
                </td>
                <td style="text-align: right; padding-right: 10px;">SUBTOTAL Bs</td>
                <td style="text-align: right;"> {{ $factura->total }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px;">DESCUENTO Bs</td>
                <td style="text-align: right;">{{ $factura->descuento_adicional }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 10px;">TOTAL Bs</td>
                <td style="text-align: right;">
                    {{ number_format($factura->total - $factura->descuento_adicional, 2) }}
                </td>
            </tr>
            <tr>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
                <td style="border:none; background:white"></td>
            </tr>
            <tr style="align: right;">
                <td style="background: white; border: none; text-align: center;" colspan="5">
                    <b>Dirección: Calle Electo Díaz N° 1726 Contactos: 67692748-73493715 Nit: 7167162017</b>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
