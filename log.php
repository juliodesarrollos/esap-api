<?php
class Log {
    private $logFile;
    
    public function __construct($file = 'app.log') {
        $this->logFile = $file;
    }
    
    public function write($message) {
        $date = new DateTime();
        $logEntry = $date->format('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
}
?>
