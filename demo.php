<?php
require_once __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL ^ E_NOTICE);
switch ($_SERVER['REQUEST_METHOD']) {
       
        case 'POST':
            $datos = json_decode(file_get_contents('php://input'),true);
            $claveAccesoAux=$datos["claveAcceso"];
            generarmasivos($claveAccesoAux);
            
            http_response_code(200);
           

            
               //end else
            break;
        case 'GET':
            comprimirarchivos();
            exit;
            http_response_code(200);
            break;

        default:
            http_response_code(405);
            break;
    }

function generarmasivos($claveAccesoAux){
        $respuesta = autorizar($claveAccesoAux);
        

        if ($respuesta["RespuestaAutorizacionComprobante"]["autorizaciones"]["autorizacion"]["estado"] == "AUTORIZADO") {

        $numeroAutorizacion = $respuesta["RespuestaAutorizacionComprobante"]["autorizaciones"]["autorizacion"]["numeroAutorizacion"];
        $fechaAutorizacion = $respuesta["RespuestaAutorizacionComprobante"]["autorizaciones"]["autorizacion"]["fechaAutorizacion"];
        $comprobanteString = $respuesta["RespuestaAutorizacionComprobante"]["autorizaciones"]["autorizacion"]["comprobante"];


        $xml = simplexml_load_string($comprobanteString);



        $dataJson1 = json_encode($xml);
        $dataJson = json_decode($dataJson1, true);


    //info tributaria
        $ambiente = $dataJson["infoTributaria"]["ambiente"];
        $ambiente_ok = "";
        if ($ambiente == "1") {
            $ambiente_ok = "PRUEBAS";
        } else {
            $ambiente_ok = "PRODUCCIÓN";
        }
        $tipoEmision = $dataJson["infoTributaria"]["tipoEmision"];
        $razonSocialEmisor = $dataJson["infoTributaria"]["razonSocial"];
        $nombreComercialEmisor = $dataJson["infoTributaria"]["nombreComercial"];
        if(is_array($nombreComercialEmisor)){
            $nombreComercialEmisor=$razonSocialEmisor;
        }
        
        
        $rucEmisor = $dataJson["infoTributaria"]["ruc"];
        $obligadoContabilidad=$dataJson["infoFactura"]["obligadoContabilidad"];
        $claveAcceso = $dataJson["infoTributaria"]["claveAcceso"];
        $codDoc = $dataJson["infoTributaria"]["codDoc"];
        $estab = $dataJson["infoTributaria"]["estab"];
        $ptoEmi = $dataJson["infoTributaria"]["ptoEmi"];
        $secuencial = $dataJson["infoTributaria"]["secuencial"];
        $dirMatriz = $dataJson["infoTributaria"]["dirMatriz"];

        $numeroDeFacturaOk = $estab . "-" . $ptoEmi . "-" . $secuencial;
    //info factura


        $fechaEmision = $dataJson["infoFactura"]["fechaEmision"];
        try{
            $dirEstablecimiento = $dataJson["infoFactura"]["direccionComprador"];
        }catch (Exception $e) {
            $dirEstablecimiento = "";
        }
       
        $razonSocialComprador = $dataJson["infoFactura"]["razonSocialComprador"];
        $identifacionComprador = $dataJson["infoFactura"]["identificacionComprador"];
        //$direccionComprador = $dataJson["infoFactura"]["direccionComprador"];
        $totalSinImpuestos = $dataJson["infoFactura"]["totalSinImpuestos"];
        $totalDescuento = $dataJson["infoFactura"]["totalDescuento"];
        //$valorIva2 = $dataJson["infoFactura"]["totalConImpuestos"]["totalImpuesto"]["valor"];
        $numDetalles = count($dataJson["infoFactura"]["totalConImpuestos"]["totalImpuesto"]);
        $items2 = $dataJson["infoFactura"]["totalConImpuestos"]["totalImpuesto"];
        $razonSocialEmisor=str_replace('"',"",$razonSocialEmisor);
        $nombreComercialEmisor=str_replace('"',"",$razonSocialEmisor);
        $razonSocialComprador=str_replace('"',"",$razonSocialEmisor);
        $razonSocialComprador=str_replace("?","N",$razonSocialEmisor);
        $razonSocialEmisor=str_replace("?","N",$razonSocialEmisor);
        //echo $razonSocialEmisor;
        if(isset($items2["baseImponible"])){
            $codigoPorcentaje = $items2["codigoPorcentaje"];
            if($codigoPorcentaje=="0"){
                $baseImponible0=$items2["baseImponible"];
                $valorIva="0.00";
                $baseImponible12="0.00";
            }
            if($codigoPorcentaje=="2"){
                $baseImponible12=$items2["baseImponible"];
                $valorIva=$items2["valor"];
                $baseImponible0="0.00";
            }

            
        }else{
            foreach ($items2 as $item) {
                $codigoPorcentaje = $item["codigoPorcentaje"];
                if($codigoPorcentaje=="0"){
                    $baseImponible0=$item["baseImponible"];
                }
                if($codigoPorcentaje=="2"){
                    $baseImponible12=$item["baseImponible"];
                    $valorIva=$item["valor"];
                }
        }
        if(is_null($baseImponible0)){
             $baseImponible0="0.00";
        }
        if(is_null($baseImponible12)){
            $valorIva="0.00";
            $baseImponible12="0.00";
        }
        }
        
        
        $importeTotal = $dataJson["infoFactura"]["importeTotal"];
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
            $response = array();
            $response[0] = array(
                'numerofactura'=>$numeroDeFacturaOk,
                'secuncia'=>$secuencial,
                'autorizacion'=>$numeroAutorizacion,
                'fecha'=>$fechaEmision,
                'ruc_emisor'=>$rucEmisor,
                'ruc_cliente'=>$identifacionComprador,
                'razon_social'=>$razonSocialEmisor,
                'nombre_comercial'=>$nombreComercialEmisor,
                'totalsinimpuesto'=>$totalSinImpuestos,
                'descuento'=>$totalDescuento,
                'baseImponible0'=>$baseImponible0,
                'baseImponible12'=>$baseImponible12,
                'iva'=>$valorIva,
                'total' => $dataJson["infoFactura"]["importeTotal"],
                
            );
        echo json_encode($response); 

    //pago
    //detalles

        $numDetalles = count($dataJson["detalles"]["detalle"]);
        $items = $dataJson["detalles"]["detalle"];

    //$i = 0;
    //info Adicional 
        //$infoAdicional = $dataJson["infoAdicional"]["campoAdicional"];


        $mpdf = new \Mpdf\Mpdf();
        $html = <<<DRK
        <!DOCTYPE html>
        <html lang="ES">
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <title></title>
                <!-- Tell the browser to be responsive to screen width -->
                <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />
        
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
                <!-- Google Font -->
                <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        
                <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        
            </head>
            <body>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table display" style="width: 100%">
                            <tr>
                            <td>
                            <!--  
                            <img src="img/my-360-logo-.png" class="col-center" style="width:40%; margin-left:-15px; margin-top:15px; margin-bottom:5px" >-->
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
              
                                <table class="classTableBorde" style="width: 110%; margin-bottom:50px; margin-right:40px; border:solid; border-width:thin; border-color:#CDCDCD; background-color:#E0E0E0">
                                    <tr style="height: 200px;" class="col-center">
                                        <td colspan="2" class="col-center" style="vertical-align:middle;">
                                           
                                        </td>
                                    </tr>
                                    <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                    <tr>
                                        <td><b>Emisor: </b><span style="font-size:small;">$razonSocialEmisor</span> </td>
                                        <td></td>
                                    </tr>
              
                                    <tr>
                                        <td><b>RUC:</b><span style="font-size:small;">$rucEmisor</span></td>
                                        <td colspan="2"></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><b>Matriz:</b><span style="font-size:small;">$dirMatriz </span></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><b>Correo:</b><span style="font-size:small;"></span></td>
                                        <td colspan="1"></td>
                                    </tr>
                                    
                                    <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr><tr>
                                    
                                <td></td>
                                <td></td>
                              
                            </tr>
                                    
                                    <tr>
                                        <td><b>Obligado a llevar contabilidad:</b><span style="font-size:small;">$obligadoContabilidad</span></td>
                                      
                                    </tr>
                                    <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                <tr>
                                    
                                    <td></td>
                                    <td></td>
                                  
                                </tr>
                                    <tr>
                       

                                </tr>
                                  
                                </table>
                            </td>
                            <td>
                                <table class="classTableBorde" style="width: 100%; background-color:#E0E0E0; border:solid; border-width:thin; border-color:#CDCDCD;">
                                    <tr>
                                        <td><h4>FACTURA:</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><b>No.$numeroDeFacturaOk</b></td>
  
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <b>Número de Autorización:</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <p style="font-size:small;">$numeroAutorizacion</p>
                                        </td>
                                    </tr>
                                    <tr> 
                                        <td colspan="2">
                                            <b>Fecha y hora de Autorización:</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="font-size=x-small;">
                                            <p>$fechaAutorizacion</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Ambiente:</b></td>
          
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="font-size=x-small;">
                                            <p>$ambiente_ok</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Emisión:</b></td>
       
                                    </tr>
                                    <tr>
                                    <td colspan="2" style="font-size=x-small;">
                                        <p>Normal</p>
                                    </td>
                                </tr>
                                    <tr>
                                        <td colspan="2">
                                            <h4>Clave de acceso:</h4>
                                        </td>
                                    </tr>
                                    <tr class="col-center">
                                        <td align="center" colspan="2" class="col-center">
                                            <img src='barcode.php?text=$claveAcceso' alt='testing' / class="col-center" style="width:48%; height:40px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2" class="col-center"><p style="font-size:x-small;"> $claveAcceso </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    
                    
                    <table class="classTableBorde" style="width: 100%;background-color:#E0E0E0; margin-top:-40px; border:solid; border-width:thin; border-color:#CDCDCD; margin-botton:20px">
                        <tr>
                            <td><b>Razón Social:</b> <span>$razonSocialComprador</span></td>
                            <td></td>
                            <td><b>RUC/CI:</b> <span> $identifacionComprador </span></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Dirección:</b> $dirEstablecimiento<span></span> </td>
                            <td></td>
                            <td><b>Correo:</b> <span> </span> </td>
                        </tr>
                        <tr><td><b>Fecha Emisión:</b> <span>$fechaEmision</span></td>

                        </tr>
                    </table>
                 
               
                    
                    <table class="classTableBorde" style="width: 110%; margin-top:10px; border:solid; border-width:thin; border-color:#CDCDCD;">
                        <tr>
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Código Principal</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Código Auxiliar</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Cantidad</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Descripción</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Precio Unitario</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Descuento</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Precio Total</b></td> 
                        </tr>




          
                            
    DRK;
        $html2 = "";
        //foreach ($items as $item) {
        if (isset($items["cantidad"])) {
            $codigoPrincipal=$items["codigoPrincipal"];
            $codigoAuxiliar=$items["codigoAuxiliar"];
            $cantidad_ok2 = $items["cantidad"];
            $descripcion_ok2 = $items["descripcion"];
            $precioUnitario_ok2 = $items["precioUnitario"];
            $precioTotal_ok2 = $items["precioTotalSinImpuesto"];
            $auxItem2 = "<tr>
                                <td></td>
                                <td>$codigoPrincipal</td>
                                <td></td>
                                <td>$codigoAuxiliar</td>
                                <td></td>
                                <td>$cantidad_ok2</td>
                                <td></td>
                                <td>$descripcion_ok2</td>
                                <td>$precioUnitario_ok2</td>
                                    <td></td>
                                    <td></td>
                                    <td>0.00</td>
                                    <td></td>
                                    <td>$precioTotal_ok2</td>
                            </tr>";
            $html2 = $html2 . $auxItem2;
        } else {
            foreach ($items as $item) {
                $codigoPrincipal=$item["codigoPrincipal"];
                $cantidad_ok2 = $item["cantidad"];
                $codigoAuxiliar=$item["codigoAuxiliar"];
                $descripcion_ok2 = $item["descripcion"];
                $precioUnitario_ok2 = $item["precioUnitario"];
                $precioTotal_ok2 = $item["precioTotalSinImpuesto"];
                $auxItem2 =  "<tr>
                                    <td></td>
                                    <td>$codigoPrincipal</td>
                                    <td></td>
                                    <td>$codigoAuxiliar</td>
                                    <td></td>
                                    <td>$cantidad_ok2</td>
                                    <td></td>
                                    <td>$descripcion_ok2</td>
                                    <td>$precioUnitario_ok2</td>
                                    <td></td>
                                    <td></td>
                                    <td>0.00</td>
                                    <td></td>
                                    <td>$precioTotal_ok2</td>
                                </tr>";
                $html2 = $html2 . $auxItem2;
            }
        }

        //}

        $html3 = <<<DRKA
        </table>
                    <table class="table display" style="width: 100%;   margin-top:20px" >
                        <tr>
                            <td>
                                <table class="classTableBorde" style="width: 80%; margin-top:-150px; ">
                                    <tr>
                                        <td style="background-color:#CDCDCD;"><b>Información Adicional</b></td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;"></td>
                                    </tr>
                                </table>
                            </td>
                            
                            <td>
                                <table class="classTableBorde" style="width: 100%;">
                                    <tr>
                                        <td style="background-color:#F5F5F5;">Subtotal Sin Impuestos:</td>
                                        <td style="text-align:right;" >$totalSinImpuestos</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">Subtotal 12%:</td>
                                        <td  style="text-align:right;">$baseImponible12</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">Subtotal 0%</td>
                                        <td style="text-align:right;">$baseImponible0</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">Subtotal No Objeto IVA:</td>
                                        <td  style="text-align:right;">0.00</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">Descuentos:</td>
                                        <td style="text-align:right;">$totalDescuento</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">ICE:</td>
                                        <td  style="text-align:right;">0.00</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">IVA 12%:</td>
                                        <td  style="text-align:right;">$valorIva</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">Propina:</td>
                                        <td  style="text-align:right;">0.00</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color:#F5F5F5;">Valor Total:</td>
                                        <td style="text-align:right;">$importeTotal</td>
                                    </tr>
                                    
                                </table>
                            </td>
                        </tr>
                    </table>
                    
                </div>
                </div>
                <br>
                <div class="container col-center" style="margin-top:5rem; text-align:center; background-color:#E0E0E0" >
                    <span>Este comprobante ha sido emitido por Be360 el software de facturación electrónica de Ecuador.</span>
                </div>
            </div>
         </body>
    </html>           

         


    DRKA;
    }

    $html_ok = $html . $html2 . $html3;

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html_ok, 2);
    
    $mpdf->Output($razonSocialEmisor.'-'.$numeroDeFacturaOk. '.pdf', 'F');
    //$mpdf->Output();
    //Enviar Mail



}
    function autorizar($claveAccesoAux) {
        $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
        $client = new SoapClient($url);
        $claveAcceso2 =$claveAccesoAux;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <SOAP-ENV:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ec="http://ec.gob.sri.ws.autorizacion"">
                  <SOAP-ENV:Body>
                      <ec:autorizacionComprobante>
                  <!--Optional:-->
                         <claveAccesoComprobante>'.$claveAcceso2.'</claveAccesoComprobante>
                     </ec:autorizacionComprobante>
                  </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>'; 
            $parametros = new stdClass();
            $parametros->claveAccesoComprobante = $claveAcceso2;
            $result = $client->autorizacionComprobante($parametros);
            $array = json_decode(json_encode($result), true);
            return $array;
}        

function comprimirarchivos(){


    foreach(glob(__DIR__ . '/*.pdf') as $file){
        // check if is a file and not sub-directory
        if(is_file($file)){
            // delete file
            unlink($file);
        }
    }

}