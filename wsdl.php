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
     <!-- POST_ACT_PREC -->
     <wsdl:message name="POST_ACT_PRECSoapIn">
          <wsdl:part name="parameters" element="tns:POST_ACT_PREC" />
     </wsdl:message>
     <wsdl:message name="POST_ACT_PRECSoapOut">
          <wsdl:part name="parameters" element="tns:POST_ACT_PRECResponse" />
     </wsdl:message>
     <!-- fin POST_ACT_PREC -->


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

     <!-- POST_ACT_PREC -->
     <wsdl:portType name="POST_ACT_PRECSoap">
          <wsdl:operation name="POST_ACT_PREC">
               <wsdl:input message="tns:POST_ACT_PRECSoapIn" />
               <wsdl:output message="tns:POST_ACT_PRECSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <!-- fin POST_ACT_PREC -->
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
     <!-- POST_ACT_PREC -->
     <wsdl:service name="POST_ACT_PREC">
          <wsdl:port name="POST_ACT_PRECSoap" binding="tns:POST_ACT_PRECSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
     </wsdl:service>
     <!-- fin POST_ACT_PREC -->
     <!-- fin de servicios -->
</wsdl:definitions>