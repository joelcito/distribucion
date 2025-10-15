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
        {{-- <thead>
            <tr>
                <th style="text-align: center;">
                    CONTACTOS
                    <br>
                    78945612
                </th>
            </tr>
        </thead>
        <tbody> --}}
        <tr>
            <td><b>FARMACIA</b></td>
            <td width="100px">nombre farmcia</td>
        </tr>
        <tr>
            <td><b>CONTACTO</b></td>
            <td>789456123</td>
            {{-- </tr>
        <tr>
            <td>Telefono: 7777777777777777</td>
        </tr>
        <tr>
            <td>MUNICIPIO</td>
        </tr> --}}
            {{-- </tbody> --}}
    </table>

    {{-- @if (!is_null($empresa->logo)) --}}
    {{-- <table id="logo_factura">
        <tr>
            <td>
                <img src="{{ public_path('assets/img/lop.jpg') }}" alt="" width="100%"><br>
            </td>
        </tr> --}}
    </table>
    {{-- @endif --}}

    <table id="table_nuew_num_fac">
        <tr>
            <td><b>N° DE NOTA</b></td>
            <td width="100px">{{ $factura->numero_recibo }}</td>
        </tr>
        <tr>
            <td><b>FECHA</b></td>
            <td width="100px">{{ $factura->fecha }}</td>
        </tr>
        {{-- <tr>
            <td><b>CÓD. AUTORIZACIÓN</b></td>
            <td>
                <div class="estatico">
                    COD
                </div>
            </td>
        </tr> --}}
    </table>

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
                <th><br>DETALLE<br><br></th>
                <th>PRECIO UNITARIO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                // $json = json_encode($archivoXML);
                // $array = json_decode($json, true);
                // $listado_detalles = $array['detalle'];
                // $subTotales = 0;

                $detalles = $factura->detalles;
            @endphp
            @foreach ($detalles as $detalle)
                @php
                    $total += $detalle->total;
                @endphp
                <tr>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ $detalle->producto->nombre }}</td>
                    <td></td>
                    <td>{{ $detalle->precio }}</td>
                    <td>{{ $detalle->total }}</td>
                </tr>
            @endforeach
            <tr style="align: right;">
                <td style="background: white; border: none;" colspan="3" rowspan="3">
                    @php

                        // function getXmlValue($element, $default = 0)
                        // {
                        //     return isset($element) && (string) $element !== '' ? (float) (string) $element : $default;
                        // }

                        // $monto_gif_card = getXmlValue($archivoXML->cabecera->montoGiftCard);
                        // $monto_total = getXmlValue($archivoXML->cabecera->montoTotal);

                        // $to = $monto_total - $monto_gif_card;

                        // // Separar la parte entera y la parte decimal del monto
                        // $entero = floor($to); // Parte entera
                        // $decimal = round(($to - $entero) * 100); // Parte decimal, redondeada a dos decimales

                        // // Crear una instancia de NumberFormatter para el idioma español
                        // $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);

                        // // Convertir solo la parte entera a su forma literal
                        // $literal = $formatter->format($entero);
                    @endphp
                    {{-- <b>Son: /100 Bolivianos</b> --}}
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
                    {{-- @php
                        $monto_gif_card = getXmlValue($archivoXML->cabecera->montoGiftCard);
                        $monto_total = getXmlValue($archivoXML->cabecera->montoTotal);

                        $total = $monto_total - $monto_gif_card;
                    @endphp --}}

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

        </tbody>
    </table>

    {{-- @if ($factura->estado === 'Anulado')
        <p id="anulado">ANULADO</p>
    @endif --}}

</body>

</html>
