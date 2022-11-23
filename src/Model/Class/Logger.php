<?php
namespace App\Model\Class;

class Logger extends LoggerSingleton{
    /**
     * A file pointer resource of the file
    */
    private $fileHandle ;

    /**
     * Singleton's constructor is called only once, 
     */
    protected function __construct()
    {
        $this->fileHandle = fopen('php://stderr', 'w');
    }

    /**
     * write a log entryto the opened file resource.
     */
    public function writeLog(string $message):void
    {
        date_default_timezone_set("America/Sao_Paulo");
        $date = date('Y-m-d H:i:s');
        fwrite($this->fileHandle, "$date: $message\n");
    }

    /**
     * Just a handy to reduce the amount of code needed to log message
     * from the client code.
     */
    public static function log(string $message):void{
    
        /**
         * The client code.
         */
        /*$IP = $_SERVER['REMOTE_ADDR'];
        
        // PHP code to get the MAC address of Server
        $MAC = exec('getmac');
  
        // Storing 'getmac' value in $MAC
        $MAC = strtok($MAC, ' ');
        */
        
        $logger = static::getInstance();
        $logger->writeLog("$message");
    }

}