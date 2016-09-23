<?php

include_once('./ProxyLogger.php');

function soapTest($logger)
{
    // add endpoint 
    $mage_url = 'http://example.com/api/soap_v2?wsdl';
    // add user
    $mage_user = 'test'; 
    // add api_key
    $mage_api_key = 'test'; 
 
    /*
     * change params to what is needed for your soap call. 
     * Currently set to use gzipped headers, will fail on magemojo servers
     * that don't have php-xmlrpc installed or zlib.compression enabled in php.ini.
     */
    $params = array('trace' => 1, 'cache_wsdl'=> 1, "stream_context" => stream_context_create(array(
        'http' => array('header' => 'Accept-Encoding: gzip;q=1.0,deflate;q=0.6,identity;q=0.3'))));
   // $params = array('trace' => 1, 'cache_wsdl' => 1); 

    // Initialize the SOAP client 
    $logger->Log("***Starting SoapClient");
    $soap = new SoapClient( $mage_url, $params); 
    // Login to Magento 
    $logger->Log("***New client established: ");
    try{
        // Login call is cross version
        $session_id = $soap->login( $mage_user, $mage_api_key );
        $logger->Log("SessionID: ".$session_id);
         
		/* Version 1 API calls
         * note: there must be a customer id of 1 and 2 for the customer calls to work, you can change this parameter to whatever customer you need.
         * need to change sku of product or product call will fail.
         */

        $result = $soap->call( $session_id, 'customer.info', 1);
        // $result = $soap->call( $session_id, 'customer.info', 2);
        // $result = $soap->call( $session_id,'catalog_product.info','MDCOM01'); 

        // Version 2 API calls
        //$result = $soap->customerCustomerInfo($session_id, 1);
        //$result = $soap->catalogCategoryAttributeList($session_id);
        
        print_r($result);

        // Request Headers
        $requestHeaders = $soap->__getLastRequestHeaders();
        $responseHeaders = $soap->__getLastResponseHeaders();


        $logger->Log("Request Header:\n" . $requestHeaders);
        $logger->Log("Response Headers:\n" . $responseHeaders);
         
    }
    catch (Exception $e) {
        echo ('Caught exception: '.  $e);
    }
 
}
 
 

