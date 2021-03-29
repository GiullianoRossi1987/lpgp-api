<?php
namespace ErrorHandle;
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/ErrorHandler.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/logger.php";

use Logs\Logger;

/**
 * <Class> Error handler class, it throws errors during the API requisitions, it
 * fetches the error code and content to a array and encodes it in JSON to send
 * via HTTPS requires, also writing in the logs file the error line;
 * @var string|null logsFile The logs file path to load
 * @var boolean gotLogs If the class have a logs file loaded
 * @var Logger|null writer The logs class handler to write the logs file
 */
class ErrorHandler{
    private $logsFile = null;
    private $gotLogs = false;
    private $writer = null;

    const NON_ERR_CODE = 0;

    /**
     * Sets the logs file to the class attribute and then enable it to be used
     * as the proper error handler.
     * @param string $logsFile The logs file path to load
     * @throws LogsFileLoadedError If the class already have a logs file setted
     * @return void
     */
    public function loadLogs(string $logsFile): void{
        if($this->gotLogs) throw new LogsFileLoadedError();
        $this->logsFile = $logsFile;
        $this->writer = new Logger($this->logsFile);
        $this->gotLogs = true;
        return;
    }

    /**
     * Unsets the logs file from the class attributes, making it able to receive
     * other logs file.
     * @return void
     */
    public function unloadLogs(): void{
        if($this->gotLogs){
            $this->logsFile = null;
            $this->writer   = null;
            $this->gotLogs  = false;
        }
        return;
    }

    /**
     * Class constructor used to create a class object and set the logs file to
     * load.
     * @param string|null $logsFile The logs file path to load, if the param value
     *                                  is null then will not load a logs file at all
     * @return void
     */
    public function __construct(?string $logsFile){
        if(!is_null($logsFile)) $this->loadLogs($logsFile);
    }

    /**
     * Class destroier, used when the class memory reference must be erased to
     * give place to other variable, then before it the class must unload the
     * logs file loaded.
     * @return void
     */
    public function __destruct(){ $this->unloadLogs(); }

    /**
     * Returns the JSON content of the message to be sent via HTTPS request
     * If the error code is equal to the constant NON_ERR_CODE, then the
     * function will assume that's isn't a error and will not write in the logs
     * file, otherwise it'll write the logs file.
     *
     * @param int $status The error code status of the error
     * @param string $error The error message.
     * @throws LogsFileNotLoaded If there's no logs file loaded yet
     * @return string The JSON encoded content.
     */
    public function throwError(int $status = ErrorHandler::NON_ERR_CODE, string $error): string{
        $content = array(
            "status" => $status,
            "error" => $error
        );
        if($status != self::NON_ERR_CODE){
            if(!$this->gotLogs) throw new LogsFileNotLoaded();
            $this->writer->addLine("ERR $status -> $error", true);
        }
        return json_encode($content);
    }
}
?>
