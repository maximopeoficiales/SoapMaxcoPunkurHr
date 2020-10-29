<?php
define('WP_USE_THEMES', false);
require('../wp-blog-header.php');
header("Content-Type: text/xml");
$urld = get_site_url() . "/webservices/";
$urlsoap = get_site_url() . "/webservices/init.php";
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
                         <s:element minOccurs="0" maxOccurs="1" name="cent" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="alm" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="nomb" type="s:string" />
                         <s:element minOccurs="0" maxOccurs="1" name="und" type="s:string" />
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
                              <s:element minOccurs="0" maxOccurs="1" name="POST_ACT_MATResult" type="tns:POST_ACT_MATResponse" />
                         </s:sequence>
                    </s:complexType>
               </s:element>
               <s:complexType name="POST_ACT_MATResponse">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="Body" type="tns:POST_ACT_MATResponseBody" />
                    </s:sequence>
               </s:complexType>
               <s:complexType name="POST_ACT_MATResponseBody">
                    <s:sequence>
                         <s:element minOccurs="0" maxOccurs="1" name="POST_ACT_MATResult" type="s:string" />
                    </s:sequence>
               </s:complexType>
               <!-- fin de estructura de respuesta -->
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

     <wsdl:portType name="POST_ACT_MATSoap">
          <wsdl:operation name="POST_ACT_MAT">
               <wsdl:input message="tns:POST_ACT_MATSoapIn" />
               <wsdl:output message="tns:POST_ACT_MATSoapOut" />
          </wsdl:operation>
     </wsdl:portType>
     <wsdl:binding name="CustomerPOST_ACT_MATSoap" type="tns:POST_ACT_MATSoap">
          <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_MAT">
               <soap:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding>
     <!-- <wsdl:binding name="CustomerETDLoadSoap12" type="tns:CustomerETDLoadSoap">
          <soap12:binding transport="http://schemas.xmlsoap.org/soap/http" />
          <wsdl:operation name="POST_ACT_MAT">
               <soap12:operation soapAction="<?= $urlsoap ?>" style="document" />
               <wsdl:input>
                    <soap12:body use="literal" />
               </wsdl:input>
               <wsdl:output>
                    <soap12:body use="literal" />
               </wsdl:output>
          </wsdl:operation>
     </wsdl:binding> -->
     <wsdl:service name="CustomerPOST_ACT_MAT">
          <wsdl:port name="CustomerPOST_ACT_MATSoap" binding="tns:CustomerPOST_ACT_MATSoap">
               <soap:address location="<?= $urlsoap ?>" />
          </wsdl:port>
          <!-- <wsdl:port name="CustomerETDLoadSoap12" binding="tns:CustomerETDLoadSoap12">
               <soap12:address location="http://des-maq-leg.azurewebsites.net/CustomerETDLoad.asmx" />
          </wsdl:port> -->
     </wsdl:service>
</wsdl:definitions>