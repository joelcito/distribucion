<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE STOCK</title>
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
                    background-color: pink;

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
            //   $cliente = \App\Models\Pago::find($pedido->cliente_id);
        @endphp
        <tr>
            <td><b>NOMBRE USUARIO:{{ $users->name ?? 'Usuario desconocido' }}</b></td>



        </tr>
        <tr>
            <td><b>FECHA:{{ $fecha }}</b></td>

        </tr>

    </table>




    <table id="TableFactura">
        <thead>
            <tr>
                <th style="font-size: 12;">
                    REPORTE STOCK
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
                <th>Producto</th>
                @foreach($sucursales as $sucursal)
                    <th>{{ $sucursal->nombre }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $totales = [];
                foreach ($sucursales as $sucursal)
                    $totales[$sucursal->id] = 0;
            @endphp

            @foreach($data as $row)
                <tr>
                    <td>{{ $row['producto'] }}</td>
                    @foreach($sucursales as $sucursal)
                        @php
                            $cantidad = $row['sucursales'][$sucursal->id] ?? 0;
                            $totales[$sucursal->id] += $cantidad;
                        @endphp
                        <td>{{ $cantidad }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                @foreach($sucursales as $sucursal)
                    <th>{{ $totales[$sucursal->id] }}</th>
                @endforeach
            </tr>
        </tfoot>
    </table>
</body>

</html>