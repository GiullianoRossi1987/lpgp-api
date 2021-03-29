<?php
namespace ErrorHandle;
use \Exception;

/**
 * <Exception> Thrown when the error handler class object tries to throw a error
 * but there's no logs error to relate for.
 *
 */
class LogsFileNotLoaded extends Exception{
    public function __construct(){
        parent::__construct("Can't throw a error, no logs file related!");
    }
}

/**
 * <Exception> Thrown when the error handler class object tries to set a logs file
 * but there's another logs file loaded already
 */
class LogsFileLoadedError extends Exception{
    public function __construct(){
        parent::__construct("Can't set logs file, other logs file already setted");
    }
}

?>
