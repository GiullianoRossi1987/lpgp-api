<?php
namespace Config;
use Exception;

/// Exceptions

/**
 * <Exception> Thrown when the internal configurations class object tries to load another configurations file
 * having a configurations file loaded already.
 */
class ConfigurationsLoadedError extends Exception{
    public function __construct(){ parent::__construct("Can't load another configurations file while have one loaded already"); }
}

/**
 * <Exception> Thrown when the configurations class object tries to do a action that requires a configurations file
 * loaded, but there's no configurations file loaded yet
 */
class ConfigurationsNotLoaded extends Exception{
    public function __construct(){ parent::__construct("There's no configurations file loaded to do this action"); }
}

/**
 * <Exception> Thrown when the configurations class object had errors while loading the configurations file
 */
class ConfigurationsLoadingError extends Exception{
    public function __construct(string $file, string $error = "UNKNOWN ERROR"){
        parent::__construct("'$error' occoured while loading the file '$file'");
    }
}
?>
