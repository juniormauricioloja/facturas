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
        

        default:
            http_response_code(405);
            break;
    }//end while

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
        $obligadoContabilidad=$dataJson["infoCompRetencion"]["obligadoContabilidad"];
        $claveAcceso = $dataJson["infoTributaria"]["claveAcceso"];
        $codDoc = $dataJson["infoTributaria"]["codDoc"];
        $estab = $dataJson["infoTributaria"]["estab"];
        $ptoEmi = $dataJson["infoTributaria"]["ptoEmi"];
        $secuencial = $dataJson["infoTributaria"]["secuencial"];
        $dirMatriz = $dataJson["infoTributaria"]["dirMatriz"];

        $numeroDeFacturaOk = $estab . "-" . $ptoEmi . "-" . $secuencial;
    //info factura


        $fechaEmision = $dataJson["infoCompRetencion"]["fechaEmision"];
        try{
            $dirEstablecimiento = $dataJson["infoCompRetencion"]["direccionComprador"];
        }catch (Exception $e) {
            $dirEstablecimiento = "";
        }
       
        $razonSocialComprador = $dataJson["infoCompRetencion"]["razonSocialSujetoRetenido"];
        $identifacionComprador = $dataJson["infoCompRetencion"]["identificacionSujetoRetenido"];
        $periodoFiscal=$dataJson["infoCompRetencion"]["periodoFiscal"];
        $items = $dataJson["impuestos"]["impuesto"];
        $numdocumento=$dataJson["impuestos"]["impuesto"]["numDocSustento"];
      
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
                            <img src="img/my-360-logo-.png" class="col-center" style="width:40%; margin-left:-15px; margin-top:15px; margin-bottom:5px" >
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
                                        <td><h4>Comprobante de Retención:</h4></td>
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
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Comprobante</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Número</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Fecha Emisión</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Periodo Fiscal</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Base Imponible para
                            la Retención</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Impuesto</b></td> 
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Porcentaje Retención</b></td> 
                           
                            <td align="center" colspan="2" class="col-center" style="background-color:#E0E0E0;"><b>Valor Retenido</b></td> 
                        </tr>




          
                            
    DRK;
        $html2 = "";
        //foreach ($items as $item) {
        
        if (isset($items["numDocSustento"])) {
            $comprobante = "factura";
            $baseImponible = $items["baseImponible"];
            $numDocSustento=$items["numDocSustento"];
            $codigoRetencion = $items["codigoRetencion"];
            $porcentajeRetener=$items["porcentajeRetener"];
            $valorRetenido=$items["valorRetenido"];
            if($codigoRetencion=="303"){
                $codigoRetencion="Honorarios profesionales y demás pagos por servicios relacionados con el título profesional";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="304"){
                $codigoRetencion="Servicios predomina el intelecto no relacionados con el título profesional";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="304A"){
                $codigoRetencion="Comisiones y demás pagos por servicios predomina intelecto no relacionados con el título profesional";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="304B"){
                $codigoRetencion="Pagos a notarios y registradores de la propiedad y mercantil por sus actividades ejercidas como tales";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="304C"){
                $codigoRetencion="Pagos a deportistas, entrenadores, árbitros, miembros del cuerpo técnico por sus actividades ejercidas como tales";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="304D"){
                $codigoRetencion="Pagos a artistas por sus actividades ejercidas como tales";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="304E"){
                $codigoRetencion="Honorarios y demás pagos por servicios de docencia";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="307"){
                $codigoRetencion="Servicios predomina la mano de obra";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="308"){
                $codigoRetencion="Utilización o aprovechamiento de la imagen o renombre";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="309"){
                $codigoRetencion="Servicios prestados por medios de comunicación y agencias de publicidad";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="310"){
                $codigoRetencion="Servicio de transporte privado de pasajeros o transporte público o privado de carga";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="311"){
                $codigoRetencion="Transferencia de bienes muebles de naturaleza corporal";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="312"){
                $codigoRetencion="Pagos a artistas por sus actividades ejercidas como tales";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="312A"){
                $codigoRetencion="Compra de bienes de origen agrícola, avícola, pecuario, apícola, cunícola, bioacuático, forestal y carnes en estado natural";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="312B"){
                $codigoRetencion="Impuesto a la Renta único para la actividad de producción y cultivo de palma aceitera";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="314A"){
                $codigoRetencion="Regalías por concepto de franquicias de acuerdo a Ley de Propiedad Intelectual - pago a personas naturales";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="314B"){
                $codigoRetencion="Cánones, derechos de autor,  marcas, patentes y similares de acuerdo a Ley de Propiedad Intelectual – pago a personas naturales";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="314C"){
                $codigoRetencion="Regalías por concepto de franquicias de acuerdo a Ley de Propiedad Intelectual  - pago a sociedades";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="314D"){
                $codigoRetencion="Cánones, derechos de autor,  marcas, patentes y similares de acuerdo a Ley de Propiedad Intelectual – pago a sociedades";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
             if($codigoRetencion=="319"){
                $codigoRetencion="Cuotas de arrendamiento mercantil (prestado por sociedades), inclusive la de opción de compra";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="320"){
                $codigoRetencion="Arrendamiento de bienes inmuebles";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="322"){
                $codigoRetencion="Seguros y reaseguros (primas y cesiones)";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323"){
                $codigoRetencion="Rendimientos financieros pagados a naturales y sociedades  (No a IFIs)";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323A"){
                $codigoRetencion="Rendimientos financieros depósitos Cta. Corriente";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323B1"){
                $codigoRetencion="Rendimientos financieros  depósitos Cta. Ahorros Sociedades";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323E"){
                $codigoRetencion="Rendimientos financieros depósito a plazo fijo  gravados";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323E2"){
                $codigoRetencion="Rendimientos financieros depósito a plazo fijo exentos";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323E2"){
                $codigoRetencion="Rendimientos financieros operaciones de reporto - repos";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323F"){
                $codigoRetencion="Rendimientos financieros operaciones de reporto - repos";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323G"){
                $codigoRetencion="Inversiones (captaciones) rendimientos distintos de aquellos pagados a IFIs";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323H"){
                $codigoRetencion="Rendimientos financieros  obligaciones";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323I"){
                $codigoRetencion="Rendimientos financieros  bonos convertible en acciones";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323M"){
                $codigoRetencion="Rendimientos financieros : Inversiones en títulos valores en renta fija gravados ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323N"){
                $codigoRetencion="Rendimientos financieros  Inversiones en títulos valores en renta fija exentos";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323O"){
                $codigoRetencion="Intereses y demás rendimientos financieros pagados a bancos y otras entidades sometidas al control de la Superintendencia de Bancos y de la Economía Popular y Solidaria";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323P"){
                $codigoRetencion="Intereses pagados por entidades del sector público a favor de sujetos pasivos";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323Q"){
                $codigoRetencion="Otros intereses y rendimientos financieros gravados ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323R"){
                $codigoRetencion="Otros intereses y rendimientos financieros exentos";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323S"){
                $codigoRetencion="Pagos y créditos en cuenta efectuados por el BCE y los depósitos centralizados de valores, en calidad de intermediarios, a instituciones del sistema financiero por cuenta de otras personas naturales y sociedades";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="323U"){
                $codigoRetencion="Rendimientos financieros originados en títulos valores de obligaciones de 360 días o más para el financiamiento de proyectos públicos en asociación público-privada";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="324A"){
                $codigoRetencion=" Intereses en operaciones de crédito entre instituciones del sistema financiero y entidades economía popular y solidaria.";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="324B"){
                $codigoRetencion="Inversiones entre instituciones del sistema financiero y entidades economía popular y solidaria";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="324C"){
                $codigoRetencion="Pagos y créditos en cuenta efectuados por el BCE y los depósitos centralizados de valores, en calidad de intermediarios, a instituciones del sistema financiero por cuenta de otras instituciones del sistema financiero";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="325"){
                $codigoRetencion="Anticipo dividendos";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="325A"){
                $codigoRetencion="Préstamos accionistas, beneficiarios o partícipes residentes o establecidos en el Ecuador";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="326"){
                $codigoRetencion="Dividendos distribuidos que correspondan al impuesto a la renta único establecido en el art. 27 de la LRTI";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="327"){
                $codigoRetencion="Dividendos distribuidos a personas naturales residentes";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="328"){
                $codigoRetencion="Dividendos distribuidos a sociedades residentes";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            } if($codigoRetencion=="329"){
                $codigoRetencion="Dividendos distribuidos a fideicomisos residentes";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }


            if($codigoRetencion=="331"){
                $codigoRetencion="Dividendos en acciones (capitalización de utilidades)
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="332"){
                $codigoRetencion="Otras compras de bienes y servicios no sujetas a retencion
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="332B"){
                $codigoRetencion="Compra de bienes inmuebles
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="332C"){
                $codigoRetencion="Transporte público de pasajeros
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="332D"){
                $codigoRetencion="Pagos en el país por transporte de pasajeros o transporte internacional de carga, a compañías nacionales o extranjeras de aviación o marítimas
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="332E"){
                $codigoRetencion="Valores entregados por las cooperativas de transporte a us socios
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="332F"){
                $codigoRetencion="Compraventa de divisas distintas al dólar de los Estados Unidos
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="332G"){
                $codigoRetencion="Pagos con tarjetas de crédito
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="332H"){
                $codigoRetencion="Pago al exterior tarjeta de crédito reportada por la Emisora de tarjeta de crédito, solo recap.
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="332I"){
                $codigoRetencion="Pago a través de convenio de débito
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="333"){
                $codigoRetencion="Ganancia en la enajenación de derechos representativos de capital u otros derechos que permitan la exploración, explotación, concesión o similares de sociedades, que se coticen en bolsa de valores del Ecuador
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="335"){
                $codigoRetencion="Loterías, rifas, apuestas y similares
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="336"){
                $codigoRetencion="Venta de combustibles a distribuidores
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="338"){
                $codigoRetencion="Producción y venta local de banano producido o no por el mismo sujeto pasivo
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="340"){
                $codigoRetencion="Otras retenciones aplicables el 1% (incluye régimen RIMPE - Emprendedores, para este caso aplica con cualquier forma de pago inclusive los pagos que deban realizar las tarjetas de crédito/débito)
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="343A"){
                $codigoRetencion="Energía Eléctrica
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="343B"){
                $codigoRetencion="Actividades de construcción de obra material inmueble, urbanización, lotización o actividades similares.
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="343C"){
                $codigoRetencion="Impuesto redimible a las botellas plásticas
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="340"){
                $codigoRetencion="Otras retenciones aplicables al 2.7%
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="344A"){
                $codigoRetencion="Pago local tarjeta de crédito /débito reportada por la Emisora de tarjeta de crédito / entidades del sistema financiero
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="344B"){
                $codigoRetencion="Adquisición de sustancias minerales dentro del territorio nacional
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="345"){
                $codigoRetencion="Otras retenciones aplicables al 8%
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="346"){
                $codigoRetencion="Otras retenciones aplicables a otros porcentajes
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="346A"){
                $codigoRetencion="Otras ganancias de capital distintas de enajenación de derechos representativos de capital 
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="346B"){
                $codigoRetencion="Donaciones en dinero -Impuesto a las donaciones 
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="346C"){
                $codigoRetencion="Retención a cargo del propio sujeto pasivo por la exportación de concentrados y/o elementos metálicos
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="346D"){
                $codigoRetencion="Retención a cargo del propio sujeto pasivo por la comercialización de productos forestales
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="348"){
                $codigoRetencion="Impuesto único a ingresos provenientes de actividades agropecuarias en etapa de producción / comercialización local o exportación
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }if($codigoRetencion=="350"){
                $codigoRetencion="Otras autorretenciones
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="351"){
                $codigoRetencion="Régimen Microempresarial
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="343"){
                $codigoRetencion="Por actividades de construccion
                ";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="3440"){
                $codigoRetencion="Otras Retenciones aplicables el 2.75%";
                $baseImponibleR=$baseImponible;
                $porcentajeRetenerR=$porcentajeRetener;
                $codigoRetencionR=$codigoRetencion;
                $valorRetenidoR=$valorRetenido;
            }
            if($codigoRetencion=="2"){
                $baseImponibleI=$baseImponible;
                $porcentajeRetenerI=$porcentajeRetener;
                $codigoRetencion="IVA";
                $codigoRetencionI="IVA";
                $valorRetenidoI=$valorRetenido;
            }
            if($codigoRetencion=="7"||$codigoRetencion=="3"|| $codigoRetencion=="1"|| $codigoRetencion=="8"|| $codigoRetencion=="IVA"){
                $baseImponibleI=$baseImponible;
                $porcentajeRetenerI=$porcentajeRetener;
                $codigoRetencion="IVA";
                $codigoRetencionI="IVA";
                $valorRetenidoI=$valorRetenido;
            }
            
            $auxItem2 = "<tr>
                                <td></td>
                                <td>$comprobante</td>
                                <td></td>
                                <td>$numDocSustento</td>
                                <td></td>
                                <td>$fechaEmision</td>
                                <td></td>
                                <td>$periodoFiscal</td>
                                <td>$baseImponible</td>
                                    <td></td>
                                    <td></td>
                                    <td>$codigoRetencion</td>
                                    <td></td>
                                    <td>$porcentajeRetener</td>
                                    <td></td>
                                    <td>$valorRetenido</td>
                            </tr>";
            $html2 = $html2 . $auxItem2;
        } else {
            foreach ($items as $item) {
                $comprobante = "factura";
                $baseImponible = $item["baseImponible"];
                $codigoRetencion = $item["codigoRetencion"];
                $numDocSustento=$items["numDocSustento"];
                $porcentajeRetener=$item["porcentajeRetener"];
                $valorRetenido=$item["valorRetenido"];
                if($codigoRetencion=="303"){
                    $codigoRetencion="Honorarios profesionales y demás pagos por servicios relacionados con el título profesional";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="304"){
                    $codigoRetencion="Servicios predomina el intelecto no relacionados con el título profesional";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="3440"){
                    $codigoRetencion="Otras Retenciones aplicables el 2.75%";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="304A"){
                    $codigoRetencion="Comisiones y demás pagos por servicios predomina intelecto no relacionados con el título profesional";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="304B"){
                    $codigoRetencion="Pagos a notarios y registradores de la propiedad y mercantil por sus actividades ejercidas como tales";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="332"){
                    $codigoRetencion="Otras compras de bienes y servicios no sujetas a retencion
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="304C"){
                    $codigoRetencion="Pagos a deportistas, entrenadores, árbitros, miembros del cuerpo técnico por sus actividades ejercidas como tales";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="304D"){
                    $codigoRetencion="Pagos a artistas por sus actividades ejercidas como tales";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="351"){
                    $codigoRetencion="Régimen Microempresarial
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="304E"){
                    $codigoRetencion="Honorarios y demás pagos por servicios de docencia";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="307"){
                    $codigoRetencion="Servicios predomina la mano de obra";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="308"){
                    $codigoRetencion="Utilización o aprovechamiento de la imagen o renombre";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="309"){
                    $codigoRetencion="Servicios prestados por medios de comunicación y agencias de publicidad";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="310"){
                    $codigoRetencion="Servicio de transporte privado de pasajeros o transporte público o privado de carga";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="311"){
                    $codigoRetencion="Transferencia de bienes muebles de naturaleza corporal";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="312"){
                    $codigoRetencion="Pagos a artistas por sus actividades ejercidas como tales";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="312A"){
                    $codigoRetencion="Compra de bienes de origen agrícola, avícola, pecuario, apícola, cunícola, bioacuático, forestal y carnes en estado natural";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="312B"){
                    $codigoRetencion="Impuesto a la Renta único para la actividad de producción y cultivo de palma aceitera";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="314A"){
                    $codigoRetencion="Regalías por concepto de franquicias de acuerdo a Ley de Propiedad Intelectual - pago a personas naturales";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="314B"){
                    $codigoRetencion="Cánones, derechos de autor,  marcas, patentes y similares de acuerdo a Ley de Propiedad Intelectual – pago a personas naturales";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="314C"){
                    $codigoRetencion="Regalías por concepto de franquicias de acuerdo a Ley de Propiedad Intelectual  - pago a sociedades";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="314D"){
                    $codigoRetencion="Cánones, derechos de autor,  marcas, patentes y similares de acuerdo a Ley de Propiedad Intelectual – pago a sociedades";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                 if($codigoRetencion=="319"){
                    $codigoRetencion="Cuotas de arrendamiento mercantil (prestado por sociedades), inclusive la de opción de compra";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="320"){
                    $codigoRetencion="Arrendamiento de bienes inmuebles";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="322"){
                    $codigoRetencion="Seguros y reaseguros (primas y cesiones)";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323"){
                    $codigoRetencion="Rendimientos financieros pagados a naturales y sociedades  (No a IFIs)";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323A"){
                    $codigoRetencion="Rendimientos financieros depósitos Cta. Corriente";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323B1"){
                    $codigoRetencion="Rendimientos financieros  depósitos Cta. Ahorros Sociedades";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323E"){
                    $codigoRetencion="Rendimientos financieros depósito a plazo fijo  gravados";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323E2"){
                    $codigoRetencion="Rendimientos financieros depósito a plazo fijo exentos";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323E2"){
                    $codigoRetencion="Rendimientos financieros operaciones de reporto - repos";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323F"){
                    $codigoRetencion="Rendimientos financieros operaciones de reporto - repos";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323G"){
                    $codigoRetencion="Inversiones (captaciones) rendimientos distintos de aquellos pagados a IFIs";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323H"){
                    $codigoRetencion="Rendimientos financieros  obligaciones";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323I"){
                    $codigoRetencion="Rendimientos financieros  bonos convertible en acciones";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323M"){
                    $codigoRetencion="Rendimientos financieros : Inversiones en títulos valores en renta fija gravados ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323N"){
                    $codigoRetencion="Rendimientos financieros  Inversiones en títulos valores en renta fija exentos";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323O"){
                    $codigoRetencion="Intereses y demás rendimientos financieros pagados a bancos y otras entidades sometidas al control de la Superintendencia de Bancos y de la Economía Popular y Solidaria";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323P"){
                    $codigoRetencion="Intereses pagados por entidades del sector público a favor de sujetos pasivos";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323Q"){
                    $codigoRetencion="Otros intereses y rendimientos financieros gravados ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323R"){
                    $codigoRetencion="Otros intereses y rendimientos financieros exentos";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323S"){
                    $codigoRetencion="Pagos y créditos en cuenta efectuados por el BCE y los depósitos centralizados de valores, en calidad de intermediarios, a instituciones del sistema financiero por cuenta de otras personas naturales y sociedades";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="323U"){
                    $codigoRetencion="Rendimientos financieros originados en títulos valores de obligaciones de 360 días o más para el financiamiento de proyectos públicos en asociación público-privada";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="324A"){
                    $codigoRetencion=" Intereses en operaciones de crédito entre instituciones del sistema financiero y entidades economía popular y solidaria.";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="324B"){
                    $codigoRetencion="Inversiones entre instituciones del sistema financiero y entidades economía popular y solidaria";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="324C"){
                    $codigoRetencion="Pagos y créditos en cuenta efectuados por el BCE y los depósitos centralizados de valores, en calidad de intermediarios, a instituciones del sistema financiero por cuenta de otras instituciones del sistema financiero";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="325"){
                    $codigoRetencion="Anticipo dividendos";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="325A"){
                    $codigoRetencion="Préstamos accionistas, beneficiarios o partícipes residentes o establecidos en el Ecuador";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="326"){
                    $codigoRetencion="Dividendos distribuidos que correspondan al impuesto a la renta único establecido en el art. 27 de la LRTI";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="327"){
                    $codigoRetencion="Dividendos distribuidos a personas naturales residentes";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="328"){
                    $codigoRetencion="Dividendos distribuidos a sociedades residentes";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                } if($codigoRetencion=="329"){
                    $codigoRetencion="Dividendos distribuidos a fideicomisos residentes";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
    
    
                if($codigoRetencion=="331"){
                    $codigoRetencion="Dividendos en acciones (capitalización de utilidades)
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="332B"){
                    $codigoRetencion="Compra de bienes inmuebles
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="332C"){
                    $codigoRetencion="Transporte público de pasajeros
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="332D"){
                    $codigoRetencion="Pagos en el país por transporte de pasajeros o transporte internacional de carga, a compañías nacionales o extranjeras de aviación o marítimas
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="332E"){
                    $codigoRetencion="Valores entregados por las cooperativas de transporte a us socios
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="332F"){
                    $codigoRetencion="Compraventa de divisas distintas al dólar de los Estados Unidos
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="332G"){
                    $codigoRetencion="Pagos con tarjetas de crédito
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="332H"){
                    $codigoRetencion="Pago al exterior tarjeta de crédito reportada por la Emisora de tarjeta de crédito, solo recap.
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="332I"){
                    $codigoRetencion="Pago a través de convenio de débito
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="333"){
                    $codigoRetencion="Ganancia en la enajenación de derechos representativos de capital u otros derechos que permitan la exploración, explotación, concesión o similares de sociedades, que se coticen en bolsa de valores del Ecuador
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="335"){
                    $codigoRetencion="Loterías, rifas, apuestas y similares
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="336"){
                    $codigoRetencion="Venta de combustibles a distribuidores
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="338"){
                    $codigoRetencion="Producción y venta local de banano producido o no por el mismo sujeto pasivo
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="340"){
                    $codigoRetencion="Otras retenciones aplicables el 1% (incluye régimen RIMPE - Emprendedores, para este caso aplica con cualquier forma de pago inclusive los pagos que deban realizar las tarjetas de crédito/débito)
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="343A"){
                    $codigoRetencion="Energía Eléctrica
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="343B"){
                    $codigoRetencion="Actividades de construcción de obra material inmueble, urbanización, lotización o actividades similares.
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="343C"){
                    $codigoRetencion="Impuesto redimible a las botellas plásticas
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="340"){
                    $codigoRetencion="Otras retenciones aplicables al 2.7%
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="344A"){
                    $codigoRetencion="Pago local tarjeta de crédito /débito reportada por la Emisora de tarjeta de crédito / entidades del sistema financiero
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="344B"){
                    $codigoRetencion="Adquisición de sustancias minerales dentro del territorio nacional
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="345"){
                    $codigoRetencion="Otras retenciones aplicables al 8%
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="346"){
                    $codigoRetencion="Otras retenciones aplicables a otros porcentajes
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="346A"){
                    $codigoRetencion="Otras ganancias de capital distintas de enajenación de derechos representativos de capital 
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="346B"){
                    $codigoRetencion="Donaciones en dinero -Impuesto a las donaciones 
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="346C"){
                    $codigoRetencion="Retención a cargo del propio sujeto pasivo por la exportación de concentrados y/o elementos metálicos
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="346D"){
                    $codigoRetencion="Retención a cargo del propio sujeto pasivo por la comercialización de productos forestales
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="348"){
                    $codigoRetencion="Impuesto único a ingresos provenientes de actividades agropecuarias en etapa de producción / comercialización local o exportación
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }if($codigoRetencion=="350"){
                    $codigoRetencion="Otras autorretenciones
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="343"){
                    $codigoRetencion="Por actividades de construccion
                    ";
                    $baseImponibleR=$baseImponible;
                    $porcentajeRetenerR=$porcentajeRetener;
                    $codigoRetencionR=$codigoRetencion;
                    $valorRetenidoR=$valorRetenido;
                }
                if($codigoRetencion=="2"){
                    $baseImponibleI=$baseImponible;
                    $porcentajeRetenerI=$porcentajeRetener;
                    $codigoRetencion="IVA";
                    $codigoRetencionI="IVA";
                    $valorRetenidoI=$valorRetenido;
                }
                if($codigoRetencion=="7"||$codigoRetencion=="3"|| $codigoRetencion=="1"||$codigoRetencion=="8"||$codigoRetencion=="IVA"){
                    $baseImponibleI=$baseImponible;
                    $porcentajeRetenerI=$porcentajeRetener;
                    $codigoRetencion="IVA";
                    $codigoRetencionI="IVA";
                    $valorRetenidoI=$valorRetenido;
                }
                
                $auxItem2 = "<tr>
                                    <td></td>
                                    <td>$comprobante</td>
                                    <td></td>
                                    <td>$numDocSustento</td>
                                    <td></td>
                                    <td>$fechaEmision</td>
                                    <td></td>
                                    <td>$periodoFiscal</td>
                                    <td>$baseImponible</td>
                                        <td></td>
                                        <td></td>
                                        <td>$codigoRetencion</td>
                                        <td></td>
                                        <td>$porcentajeRetener</td>
                                        <td></td>
                                        <td>$valorRetenido</td>
                                </tr>";
                $html2 = $html2 . $auxItem2;
            }
        }

        //}

        $html3 = <<<DRKA
        </table>
                    
                    </table>
                    
                </div>
                </div>

            </div>
         </body>
    </html>           

         


    DRKA;
}

    $html_ok = $html . $html2 . $html3;

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html_ok, 2);
    $mpdf->Output($claveAccesoAux . ".pdf", 'F');
    //$mpdf->Output();
    //Enviar Mail

   if(is_null($baseImponibleI)){
        $baseImponibleI="0.00";
        $porcentajeRetenerI="0.00";
        $codigoRetencionI="0.00";
        $valorRetenidoI="0.00";
   }
   if(is_null($baseImponibleR)){
       $baseImponibleR="0.00";
       $porcentajeRetenerR="0.00";
       $codigoRetencionI="0.00";
       $valorRetenidoR="0.00";
   }
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
                'comprobante'=>$comprobante,
                'numfactura'=>$numDocSustento,
                'baseImponibleR'=>$baseImponibleR,
                'baseImponibleI'=>$baseImponibleI,
                'porcentajeRetenerR'=>$porcentajeRetenerR,
                'porcentajeRetenerI'=>$porcentajeRetenerI,
                'codigoRetencionR'=>$codigoRetencionR,
                'codigoRetencionI'=>$codigoRetencionI,
                'valorRetenidoR'=>$valorRetenidoR,
                'valorRetenidoI'=>$valorRetenidoI,
                
            );
           
        echo json_encode($response); 
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

    