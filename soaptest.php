#!/usr/bin/env php

<?php

class SoapTest {

    protected $_logger;

    public function __construct($logger)
    {
        $this->_logger = $logger;
    }

    public function run()
    {

        /*
         * Change these to match the store you want to test.
         */

        // add endpoint 
        $mage_url = 'http://dev1.usa.rhinorugby.com/api/v2_soap/index/?wsdl=1';
        // add user
        $mage_user = 'mojotest'; 
        // add api_key
        $mage_api_key = '14qxHxBNE7XW'; 
     
        
        // Set your parameters.

        // Use these params most of time
        $params = array('trace' => 1, 'exceptions' => 1); 

        // Below is for adding gzip encoding.
        //$params = array('trace' => 1, 'cache_wsdl'=> 1, "stream_context" => stream_context_create(array('http' => array('header' => 'Accept-Encoding: gzip;q=1.0,deflate;q=0.6,identity;q=0.3'))));

        // Initialize Logging
        $this->_logger->FileName = "test.log";
        $this->_logger->Open();

        // Initialize the SOAP client 
        $soap = new \SoapClient( $mage_url, $params); 

        // log successful contact 
        $this->_logger->Log("***New client established: ");

        // Print Magento Soap Functions. If these print then you have a successful connection, even if you do not login.
        $this->_logger->Log(print_r($soap->__getFunctions(), true));

        // Inside try block so we can get an exception message on fail.
        try{
            

            // Login call is cross version
            $session_id = $soap->login( $mage_user, $mage_api_key );
            $this->_logger->Log("SessionID: ".$session_id);
            
             
            // Uncomment which api calls you would like to make dependent on which api version you are using V1 or V2.


            // You will need to add the proper ids to each call, if calling order info, need order number, if calling customer, need customer, make changes as necessary.
            
            // Version 1 API calls
            //$result = $soap->call( $session_id, 'customer.info', 1);
            //$result = $soap->call( $session_id, 'customer.info', 2);
            //$result = $soap->call( $session_id,'catalog_product.info','MDCOM01'); 

            // Version 2 API calls
            //$result = $soap->customerCustomerInfo($session_id, 1);
            $result = $soap->catalogCategoryAttributeList($session_id);
            //$result = $soap->salesOrderInfo($session_id, '100000621');
            
            // Log call.
            $this->_logger->Log(print_r($result, true));

            // prints to screen.
            print_r($result);

                     
        }
        catch (Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage() . "\n";

            // Request Headers
            $requestHeaders = $soap->__getLastRequestHeaders();
            $responseHeaders = $soap->__getLastResponseHeaders();

            // Log headers on fail.
            $this->_logger->Log("Request Header:\n" . $requestHeaders);
            $this->_logger->Log("Response Headers:\n" . $responseHeaders);

        }
     
        $this->_logger->Close();
    }
}


class ProxyLogger{
     
    var $FH = null;
    var $FileFolder = null;
    var $FileName = null;
     
    function Open() {
        $filePath = $this->FileFolder.$this->FileName."_".date("Ymd",time()).".log";
        $this->FH = fopen($filePath, 'a'); 
        if(empty($this->FH)) {
            die("can't open file: " . $filePath);
        }
            
    }
     
    function Log($strMessage, $sessionId=false, $requestId = false){
         
        // Get timestamp
        list($totalSeconds, $extraMilliseconds) = $this->TimeAndMilliseconds();
        $tStamp = date("d-m-Y H:i:s", $totalSeconds) . " ".$extraMilliseconds;
             
        // Build message
        $strLog = $tStamp;
        if($sessionId) $strLog.="       ".$sessionId;
        if($requestId) $strLog.="   ".$requestId;
        $strLog.= " ".$strMessage."\n";
         
        // Write log
        fwrite($this->FH, $strLog);
         
    }
     
    function Close(){
        fclose($this->FH);
    }
         
     
    function TimeAndMilliseconds(){
        $m = explode(' ',microtime());
        return array($m[1], (int)round($m[0]*1000,3));
    } 
     
}


 
 
$logger = new ProxyLogger();
$soap = new SoapTest($logger);
$soap->run();
