<?php
namespace Logs;
use \Exception;

/**
 * <Exception> Thrown when the logs manager class don't have any logs file loaded
 * and tries to do a action that requires it.
 */
class LogsFileNotLoaded extends Exception{
    public function __construct(){
        parent::__construct("There's no logs file loaded!");
    }
}

/**
 * <Exception> Thrown when the logs manager tries to override the data about a
 * logs file loaded
 */
class LogsFileOverrideError extends Exception{
    public function __construct(){
        parent::__construct("Can't load a logs file, override data error");
    }
}

/**
 * <Exception> Thrown when the logs manager can't access a logs file
 * for writing or reading
 */
class LogsFileAccessError extends Exception{
    public function __construct(string $file){
        parent::__construct("Can't access \"$file\" please check if it exists and you have permission to work with it");
    }
}

?>
