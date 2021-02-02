<?php
header("Content-Type: text/xml");
function url_completa($forwarded_host = false)
{
     $ssl   = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
     $proto = strtolower($_SERVER['SERVER_PROTOCOL']);
     $proto = substr($proto, 0, strpos($proto, '/')) . ($ssl ? 's' : '');
     if ($forwarded_host && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
          $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
     } else {
          if (isset($_SERVER['HTTP_HOST'])) {
               $host = $_SERVER['HTTP_HOST'];
          } else {
               $port = $_SERVER['SERVER_PORT'];
               $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
               $host = $_SERVER['SERVER_NAME'] . $port;
          }
     }
     return $proto . '://' . $host;
}
$urld = url_completa() . "/webservices/";
$urlsoap = url_completa() . "/webservices/init.php";
?>
<wsdl:definitions xmlns:tm="http://microsoft.com/wsdl/mime/textMatching/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" xmlns:tns="<?= $urld ?>" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:s="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" targetNamespace="<?= $urld ?>">
     <wsdl:types>
          <s:schema elementFormDefault="qualified" targetNamespace="<?= $urld ?>">
               <!-- datos a ingresar -->
               <s:element name="POST_ACT_MAT">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:POST_ACT_MATRequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_ACT_MATRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_mat" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="nomb" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="paq" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="undpaq" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="und" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="paqxun" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="unxpaq" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="jprod" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="peso" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="cod" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <!-- fin de datos a ingresar -->
               <!-- estructura de respuesta de POST_ACT_MAT  -->
               <s:element name="POST_ACT_MATResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="s:string" />
                         </s:sequence>
                    </s:complexType>
               </s:element>

               <!-- fin de estructura de respuesta -->
               <!-- POST_ACT_STOCK -->
               <s:element name="POST_ACT_STOCK">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:POST_ACT_STOCKRequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_ACT_STOCKRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_mat" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="undpaq" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="und" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="stck" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <s:element name="POST_ACT_STOCKResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="s:string" />
                         </s:sequence>
                    </s:complexType>
               </s:element>

               <!-- fin de POST_ACT_STOCK -->
               <!-- POST_ACT_CRED -->
               <s:element name="POST_ACT_CRED">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:POST_ACT_CREDRequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_ACT_CREDRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="cd_cli" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_cli" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="mntcred" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="mntutil" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="mntdisp" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="fvenc" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <s:element name="POST_ACT_CREDResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="s:string" />
                         </s:sequence>
                    </s:complexType>
               </s:element>

               <!-- FIN POST_ACT_CRED -->
               <!-- POST_ACT_CLI -->
               <s:element name="POST_ACT_CLI">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:POST_ACT_CLIRequestBody" />
                              <s:element minOccurs="0" maxOccurs="1" name="cliente_detalle" type="tns:ClienteDetalle" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_ACT_CLIRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_cli" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="categ" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="nomb" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="nrdoc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="telf" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="telfmov" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="email" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="drcfisc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_eje" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="nombeje" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="telf_eje" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="email_eje" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="cod" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <s:complexType name="ClienteDetalle">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="id_dest" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="drcdest" type="s:string" />
                    </s:sequence>
               </s:complexType>

               <s:element name="POST_ACT_CLIResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="s:string" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <!-- fin POST_ACT_CLI -->
               <!-- GET_ACT_CLI -->
               <s:element name="GET_ACT_CLI">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:GET_ACT_CLIRequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="GET_ACT_CLIRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="fecini" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="fecfin" type="s:string" />
                    </s:sequence>
               </s:complexType>


               <s:element name="GET_ACT_CLIResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="tns:resultArray" />
                         </s:sequence>
                    </s:complexType>
               </s:element>

               <s:complexType name="resultArray">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="unbounded" name="Cliente" type="tns:Cliente" />
                    </s:sequence>
               </s:complexType>

               <s:complexType name="Cliente">
                    <s:sequence>
                         <s:element minOccurs="1" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="cd_cli" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="nrdoc" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="nomb" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="telf" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="telfmov" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="email" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="drcfisc" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="city" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="distr" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="codubig" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="obs" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="cod" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <!-- fin GET_ACT_CLI -->

               <!-- GET_CTZ_RECEP_COTZ -->
               <s:element name="GET_CTZ_RECEP_COTZ">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:GET_CTZ_RECEP_COTZRequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="GET_CTZ_RECEP_COTZRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="cd_cli" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_cli" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="fcre" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="cod" type="s:string" />
                    </s:sequence>
               </s:complexType>


               <s:element name="GET_CTZ_RECEP_COTZResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="tns:ArrayCotizacion" />
                         </s:sequence>
                    </s:complexType>
               </s:element>

               <s:complexType name="ArrayCotizacion">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="unbounded" name="Cotizacion" type="tns:Cotizacion" />
                    </s:sequence>
               </s:complexType>

               <s:complexType name="Cotizacion">
                    <s:sequence>
                         <s:element minOccurs="1" maxOccurs="1" name="id_ctwb" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="cd_cli" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="direcdest" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="codpostal" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="payment_method" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="payment_method_title" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="lat" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="long" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="tpodesp" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="tpcotz" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="cod_status" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="status_desc" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="prctotal" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="materials" type="tns:ArrayMaterials" />
                    </s:sequence>
               </s:complexType>
               <s:complexType name="ArrayMaterials">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="unbounded" name="material" type="tns:Material" />
                    </s:sequence>
               </s:complexType>
               <s:complexType name="Material">
                    <s:sequence>
                         <s:element minOccurs="1" maxOccurs="1" name="pos" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="id_mat" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="nomb" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="cant" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="und" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="prec" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="prectot" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <!-- fin GET_CTZ_RECEP_COTZ -->

               <!-- POST_ACT_PREC -->
               <s:element name="POST_ACT_PREC">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:POST_ACT_PRECRequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_ACT_PRECRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_mat" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="canal" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="categ" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="prec" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <s:element name="POST_ACT_PRECResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="s:string" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <!-- FIN POST_ACT_PREC -->

               <!-- POST_CTZ_ENV_COTZ -->
               <s:element name="POST_CTZ_ENV_COTZ">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:POST_CTZ_ENV_COTZRequestBody" />
                              <s:element minOccurs="0" maxOccurs="1" name="det_cot" type="tns:DetalleCotizacion" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_CTZ_ENV_COTZRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_ctwb" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_ped" type="s:string" />
                    </s:sequence>
               </s:complexType>

               <s:complexType name="DetalleCotizacion">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="pos" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_mat" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="nomb" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="cant" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="und" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="prec" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="dsct" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="prctot" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="cod" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <s:element name="POST_CTZ_ENV_COTZResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="s:string" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <!-- FIN POST_CTZ_ENV_COTZ -->

               <!-- GET_PAG_RECEP_PAGO -->
               <s:element name="GET_PAG_RECEP_PAGO">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:GET_PAG_RECEP_PAGORequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="GET_PAG_RECEP_PAGORequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_ctwb" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_ped" type="s:string" />
                    </s:sequence>
               </s:complexType>


               <s:element name="GET_PAG_RECEP_PAGOResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="tns:resultSTPAG" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="resultSTPAG">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="unbounded" name="RESP_RECEP_PAGO" type="tns:RESP_RECEP_PAGO" />
                    </s:sequence>
               </s:complexType>

               <s:complexType name="RESP_RECEP_PAGO">
                    <s:sequence>
                         <s:element minOccurs="1" maxOccurs="1" name="stpag" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="dscrp" type="s:string" />
                         <s:element minOccurs="1" maxOccurs="1" name="tpcob" type="s:string" />
                    </s:sequence>
               </s:complexType>

               <!-- FIN GET_PAG_RECEP_PAGO -->

               <!-- POST_ACT_STAT_DESP -->
               <s:element name="POST_ACT_STAT_DESP">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="0" maxOccurs="1" name="request" type="tns:POST_ACT_STAT_DESPRequestBody" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_ACT_STAT_DESPRequestBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="user" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="pass" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_soc" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_ctwb" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="id_ped" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="stat" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <s:element name="POST_ACT_STAT_DESPResponse">
                    <s:complexType>
                         <s:sequence>
                              <s:element minOccurs="1" maxOccurs="1" name="RPTA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DETA" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="STUS" type="s:string" />
                              <s:element minOccurs="1" maxOccurs="1" name="DATA" type="s:string" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <!-- FIN POST_ACT_STAT_DESP -->


          </s:schema>
     </wsdl:types>
     <!-- aqui se selecciona la estructura de entrada y salida -->
     <wsdl:message name="POST_ACT_MATSoapIn">
          <wsdl:part name="parameters" element="tns:POST_ACT_MAT" />
     </wsdl:message>
     <wsdl:message name="POST_ACT_MATSoapOut">
          <wsdl:part name="parameters" element="tns:POST_ACT_MATResponse" />
     </wsdl:message>
     <!-- fin de seleccion de estructura -->
     <!-- POST_ACT_STOCK -->
     <wsdl:message name="POST_ACT_STOCKSoapIn">
          <wsdl:part name="parameters" element="tns:POST_ACT_STOCK" />
     </wsdl:message>
     <wsdl:message name="POST_ACT_STOCKSoapOut">
          <wsdl:part name="parameters" element="tns:POST_ACT_STOCKResponse" />
     </wsdl:message>
     <!-- fin POST_ACT_STOCK -->

     <!-- POST_ACT_CRED -->
     <wsdl:message name="POST_ACT_CREDSoapIn">
          <wsdl:part name="parameters" element="tns:POST_ACT_CRED" />
     </wsdl:message>
     <wsdl:message name="POST_ACT_CREDSoapOut">
          <wsdl:part name="parameters" element="tns:POST_ACT_CREDResponse" />
     </wsdl:message>
     <!-- fin POST_ACT_CRED -->

     <!-- POST_ACT_CLI -->
     <wsdl:message name="POST_ACT_CLISoapIn">
          <wsdl:part name="parameters" element="tns:POST_ACT_CLI" />
     </wsdl:message>
     <wsdl:message name="POST_ACT_CLISoapOut">
          <wsdl:part name="parameters" element="tns:POST_ACT_CLIResponse" />
     </wsdl:message>
     <!-- fin POST_ACT_CLI -->
     <!-- GET_ACT_CLI -->
     <wsdl:message name="GET_ACT_CLISoapIn">
          <wsdl:part name="parameters" element="tns:GET_ACT_CLI" />
     </wsdl:message>
     <wsdl:message name="GET_ACT_CLISoapOut">
          <wsdl:part name="parameters" element="tns:GET_ACT_CLIResponse" />
     </wsdl:message>
     <!-- fin GET_ACT_CLI -->
     <!-- POST_ACT_PREC -->
     <wsdl:message name="POST_ACT_PRECSoapIn">
          <wsdl:part name="parameters" element="tns:POST_ACT_PREC" />
     </wsdl:message>
     <wsdl:message name="POST_ACT_PRECSoapOut">
          <wsdl:part name="parameters" element="tns:POST_ACT_PRECResponse" />
     </wsdl:message>
     <!-- fin POST_ACT_PREC -->

     <!-- GET_CTZ_RECEP_COTZ -->
     <wsdl:message name="GET_CTZ_RECEP_COTZSoapIn">
          <wsdl:part name="parameters" element="tns:GET_CTZ_RECEP_COTZ" />
     </wsdl:message>
     <wsdl:message name="GET_CTZ_RECEP_COTZSoapOut">
          <wsdl:part name="parameters" element="tns:GET_CTZ_RECEP_COTZResponse" />
     </wsdl:message>
     <!-- fin GET_CTZ_RECEP_COTZ -->

     <!-- POST_CTZ_ENV_COTZ -->
     <wsdl:message name="POST_CTZ_ENV_COTZSoapIn">
          <wsdl:part name="parameters" element="tns:POST_CTZ_ENV_COTZ" />
     </wsdl:message>
     <wsdl:message name="POST_CTZ_ENV_COTZSoapOut">
          <wsdl:part name="parameters" element="tns:POST_CTZ_ENV_COTZResponse" />
     </wsdl:message>
     <!-- fin POST_CTZ_ENV_COTZ -->

     <!-- GET_PAG_RECEP_PAGO -->
     <wsdl:message name="GET_PAG_RECEP_PAGOSoapIn">
          <wsdl:part name="parameters" element="tns:GET_PAG_RECEP_PAGO" />
     </wsdl:message>
     <wsdl:message name="GET_PAG_RECEP_PAGOSoapOut">
          <wsdl:part name="parameters" element="tns:GET_PAG_RECEP_PAGOResponse" />
     </wsdl:message>
     <!-- fin GET_PAG_RECEP_PAGO -->

     <!-- POST_ACT_STAT_DESP -->
     <wsdl:message name="POST_ACT_STAT_DESPSoapIn">
          <wsdl:part name="parameters" element="tns:POST_ACT_STAT_DESP" />
     </wsdl:message>
     <wsdl:message name="POST_ACT_STAT_DESPSoapOut">
          <wsdl:part name="parameters" element="tns:POST_ACT_STAT_DESPResponse" />
     </wsdl:message>
     <!-- fin POST_ACT_STAT_DESP -->


     <!-- se registrar la funciones de entrada y salida -->
     <wsdl:portType name="POST_ACT_MATSoap">
          <wsdl:operation name="POST_ACT_MAT">
               <wsdl:input message="tns:POST_ACT_MATSoapIn" />
               <wsdl:output message="tns:POST_ACT_MATSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- POST_ACT_STOCK -->
     <wsdl:portType name="POST_ACT_STOCKSoap">
          <wsdl:operation name="POST_ACT_STOCK">
               <wsdl:input message="tns:POST_ACT_STOCKSoapIn" />
               <wsdl:output message="tns:POST_ACT_STOCKSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin POST_ACT_STOCK -->
     <!-- POST_ACT_CRED -->
     <wsdl:portType name="POST_ACT_CREDSoap">
          <wsdl:operation name="POST_ACT_CRED">
               <wsdl:input message="tns:POST_ACT_CREDSoapIn" />
               <wsdl:output message="tns:POST_ACT_CREDSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin POST_ACT_CRED -->

     <!-- POST_ACT_CLI -->
     <wsdl:portType name="POST_ACT_CLISoap">
          <wsdl:operation name="POST_ACT_CLI">
               <wsdl:input message="tns:POST_ACT_CLISoapIn" />
               <wsdl:output message="tns:POST_ACT_CLISoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin POST_ACT_CLI -->
     <!-- GET_ACT_CLI -->
     <wsdl:portType name="GET_ACT_CLISoap">
          <wsdl:operation name="GET_ACT_CLI">
               <wsdl:input message="tns:GET_ACT_CLISoapIn" />
               <wsdl:output message="tns:GET_ACT_CLISoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin GET_ACT_CLI -->

     <!-- POST_ACT_PREC -->
     <wsdl:portType name="POST_ACT_PRECSoap">
          <wsdl:operation name="POST_ACT_PREC">
               <wsdl:input message="tns:POST_ACT_PRECSoapIn" />
               <wsdl:output message="tns:POST_ACT_PRECSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin POST_ACT_PREC -->

     <!-- GET_CTZ_RECEP_COTZ -->
     <wsdl:portType name="GET_CTZ_RECEP_COTZSoap">
          <wsdl:operation name="GET_CTZ_RECEP_COTZ">
               <wsdl:input message="tns:GET_CTZ_RECEP_COTZSoapIn" />
               <wsdl:output message="tns:GET_CTZ_RECEP_COTZSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin GET_CTZ_RECEP_COTZ -->

     <!-- POST_CTZ_ENV_COTZ -->
     <wsdl:portType name="POST_CTZ_ENV_COTZSoap">
          <wsdl:operation name="POST_CTZ_ENV_COTZ">
               <wsdl:input message="tns:POST_CTZ_ENV_COTZSoapIn" />
               <wsdl:output message="tns:POST_CTZ_ENV_COTZSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin POST_CTZ_ENV_COTZ -->

     <!-- GET_PAG_RECEP_PAGO -->
     <wsdl:portType name="GET_PAG_RECEP_PAGOSoap">
          <wsdl:operation name="GET_PAG_RECEP_PAGO">
               <wsdl:input message="tns:GET_PAG_RECEP_PAGOSoapIn" />
               <wsdl:output message="tns:GET_PAG_RECEP_PAGOSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin GET_PAG_RECEP_PAGO -->


     <!-- POST_ACT_STAT_DESP -->
     <wsdl:portType name="POST_ACT_STAT_DESPSoap">
          <wsdl:operation name="POST_ACT_STAT_DESP">
               <wsdl:input message="tns:POST_ACT_STAT_DESPSoapIn" />
               <wsdl:output message="tns:POST_ACT_STAT_DESPSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin POST_ACT_STAT_DESP -->
     <!-- fin de registro -->


     <!-- aqui se indica que estructura de entrada y salida  usara una funcion especifica -->
     <wsdl:binding name="POST_ACT_MATSoap" type="tns:POST_ACT_MATSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_MAT">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- POST_ACT_STOCK -->
     <wsdl:binding name="POST_ACT_STOCKSoap" type="tns:POST_ACT_STOCKSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_STOCK">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin POST_ACT_STOCK -->
     <!-- POST_ACT_CRED -->
     <wsdl:binding name="POST_ACT_CREDSoap" type="tns:POST_ACT_CREDSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_CRED">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin POST_ACT_CRED -->
     <!-- POST_ACT_CLI -->
     <wsdl:binding name="POST_ACT_CLISoap" type="tns:POST_ACT_CLISoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_CLI">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin POST_ACT_CLI -->
     <!-- GET_ACT_CLI -->
     <wsdl:binding name="GET_ACT_CLISoap" type="tns:GET_ACT_CLISoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="GET_ACT_CLI">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin GET_ACT_CLI -->
     <!-- POST_ACT_PREC -->
     <wsdl:binding name="POST_ACT_PRECSoap" type="tns:POST_ACT_PRECSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_PREC">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin POST_ACT_PREC -->
     <!-- GET_CTZ_RECEP_COTZ -->
     <wsdl:binding name="GET_CTZ_RECEP_COTZSoap" type="tns:GET_CTZ_RECEP_COTZSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="GET_CTZ_RECEP_COTZ">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin GET_CTZ_RECEP_COTZ -->

     <!-- POST_CTZ_ENV_COTZ -->
     <wsdl:binding name="POST_CTZ_ENV_COTZSoap" type="tns:POST_CTZ_ENV_COTZSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_CTZ_ENV_COTZ">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin POST_CTZ_ENV_COTZ -->

     <!-- GET_PAG_RECEP_PAGO -->
     <wsdl:binding name="GET_PAG_RECEP_PAGOSoap" type="tns:GET_PAG_RECEP_PAGOSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="GET_PAG_RECEP_PAGO">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin GET_PAG_RECEP_PAGO -->

     <!-- POST_ACT_STAT_DESP -->
     <wsdl:binding name="POST_ACT_STAT_DESPSoap" type="tns:POST_ACT_STAT_DESPSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_STAT_DESP">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- fin POST_ACT_STAT_DESP -->

     <!-- fin de estructura -->

     <!-- nombres de los servicios a exponer -->
     <wsdl:service name="POST_ACT_MAT">
          <wsdl:port name="POST_ACT_MATSoap" binding="tns:POST_ACT_MATSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- POST_ACT_STOCK -->
     <wsdl:service name="POST_ACT_STOCK">
          <wsdl:port name="POST_ACT_STOCKSoap" binding="tns:POST_ACT_STOCKSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>

     <!-- fin POST_ACT_STOCK -->
     <!-- POST_ACT_CRED -->
     <wsdl:service name="POST_ACT_CRED">
          <wsdl:port name="POST_ACT_CREDSoap" binding="tns:POST_ACT_CREDSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin POST_ACT_CRED -->

     <!-- POST_ACT_CLI -->
     <wsdl:service name="POST_ACT_CLI">
          <wsdl:port name="POST_ACT_CLISoap" binding="tns:POST_ACT_CLISoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin POST_ACT_CLI -->
     <!-- GET_ACT_CLI -->
     <wsdl:service name="GET_ACT_CLI">
          <wsdl:port name="GET_ACT_CLISoap" binding="tns:GET_ACT_CLISoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin GET_ACT_CLI -->
     <!-- POST_ACT_PREC -->
     <wsdl:service name="POST_ACT_PREC">
          <wsdl:port name="POST_ACT_PRECSoap" binding="tns:POST_ACT_PRECSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin POST_ACT_PREC -->
     <!-- GET_CTZ_RECEP_COTZ -->
     <wsdl:service name="GET_CTZ_RECEP_COTZ">
          <wsdl:port name="GET_CTZ_RECEP_COTZSoap" binding="tns:GET_CTZ_RECEP_COTZSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin GET_CTZ_RECEP_COTZ -->

     <!-- POST_CTZ_ENV_COTZ -->
     <wsdl:service name="POST_CTZ_ENV_COTZ">
          <wsdl:port name="POST_CTZ_ENV_COTZSoap" binding="tns:POST_CTZ_ENV_COTZSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin POST_CTZ_ENV_COTZ -->

     <!-- GET_PAG_RECEP_PAGO -->
     <wsdl:service name="GET_PAG_RECEP_PAGO">
          <wsdl:port name="GET_PAG_RECEP_PAGOSoap" binding="tns:GET_PAG_RECEP_PAGOSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin GET_PAG_RECEP_PAGO -->

     <!-- POST_ACT_STAT_DESP -->
     <wsdl:service name="POST_ACT_STAT_DESP">
          <wsdl:port name="POST_ACT_STAT_DESPSoap" binding="tns:POST_ACT_STAT_DESPSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin POST_ACT_STAT_DESP -->
     <!-- fin de servicios -->
</wsdl:definitions>