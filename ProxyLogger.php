<?php

@ini_set('display_errors', 'Off');
 
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
