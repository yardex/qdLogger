<?php
/*
 * Quick and dirty Logger and var dumper
 * for debugging production sites
 *
 * @author jarek.dobrzanski@gmail.com
 *
 * usage:
 * 
 * 1. log message ('true' to show mem info):
 *
 * qdLog->log('stuff to log',true)
 * 
 * 2. dump var
 *
 * qdlog->dump($var);
 *
 */ 
class qdLogger {

    var $logfile;
    var $dumpfile;
    var $start;

    function __construct($logfile, $dumpfile = FALSE) {
        $this->logfile = $logfile;
        if($dumpfile) $this->dumpfile = $dumpfile;
    }
    
    function write($msg, $file){
        $f = fopen($file, "a+");
        fwrite($f, $msg . "\n");
        fclose($f);
    }
    
    function log($msg, $mem = FALSE){
        $timestamp = date('Y-m-d H:i:s');
        $mem_string = ($mem) ? $this->get_mem() : '';
        $this->write("[" . $timestamp . "]" . $mem_string . " " . $msg, $this->logfile);
    }
    
    function start($msg, $mem = FALSE){
        $utime = $this->start = round(microtime(true) * 1000);
        $timestamp = $this->toTimestamp($utime);
        $mem_string = ($mem) ? $this->get_mem() : '';
        $this->write("[" . $timestamp . "]" . $mem_string . "[" . $_SERVER['REQUEST_URI'] . "] " . $msg, $this->logfile);   
    }
    
    function measure($msg, $mem = FALSE){
        $utime = round(microtime(true) * 1000);
        $timestamp = $this->toTimestamp($utime);
        $interval = $utime - $this->start;
        $mem_string = ($mem) ? $this->get_mem() : '';
        $this->write("[" . $timestamp . "][" . $interval . "ms]" . $mem_string .  "[" . $_SERVER['REQUEST_URI'] . "] " . $msg, $this->logfile);
    
    
    }
    
    function toTimestamp($milliseconds)
    {
        $seconds = $milliseconds / 1000;
        $remainder = round($seconds - ($seconds >> 0), 3) * 1000;
        return gmdate('Y:m:d H:i:s.', $seconds).$remainder;
    } 
    
    function dump(&$var){
        $this->write(print_r($var, TRUE), $this->dumpfile);
    }
        
    
    function get_mem(){
        return "[peak:" . (round(memory_get_peak_usage()/1024)) . "k now:" . (round(memory_get_usage()/1024) . "k]");
    }  
       
}
