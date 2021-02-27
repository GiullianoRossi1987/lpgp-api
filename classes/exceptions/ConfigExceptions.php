<?php
namespace Config;
use Exception;

/**
 * <Exception> Thrown when the Configurations class object/instance tries to load a new configurations file, but there's another
 * configurations file loaded. In that case, try to use the unload function.
 */
class ConfigurationsFileLoaded extends Exception{
	public function __construct(){ parent::__construct("Can't load a configurations file, another file is already loaded!"); }
}

/**
 * <Exception> Thrown when the Configurations class object/instance tries to do any action that requires a configurations file loaded
 * without a configurations file.
 */
class ConfigurationsNotLoaded extends Exception{
	public function __construct(){ parent::__construct("Action unavailable, there's no configurations file loaded!"); }
}

/**
 * <Exception> Thrown when a inexpected error occours while loading a configurations file.
 */
class LoadingError extends Exception{
	public function __construct(string $file){ parent::__construct("Can't load file \"$file\", check if it exists and the permissions"); }
}
/**
 * <Exception> Thrown when the object/instance tries to change te configurations of a configurations open in the writing protection mode
 */
class WriteProtectionError extends Exception{
	public function __construct(){ parent::__construct("Write protection activated, you don't have permission to change the configurations file"); }
}
?>
