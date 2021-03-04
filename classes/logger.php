<?php
namespace Logs;
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/logger.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/interfaces/i_FileReader.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/InternalConfigurations.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Core.php";

use General\iFileReader;
use Config\InternalConfigurations;
use \DateTime;

/**
 * <Class <iFileReader> > It manages the logs files for the new application,
 * it's quite more simple use, so we use it instead of the old logs manager class.
 *
 * @var string|null $logsFile The logs file path loaded
 * @var boolean $gotLogs If the class have a logs file loaded or not
 * @var string|null $cache The content to be saved, the new logs etc
 */
class Logger implements iFileReader{
    private $logsFile = null;
    private $gotLogs = false;
    private $cache = null;

    /**
     * Loads a logs file to the class data
     * @param string $file The logs file path to load
     * @param array|[] $args Not used just leave't blank
     * @throws LogsFileOverrideError If there's a logs file loaded already
     * @return void
     */
    public function load(string $file, $args = []): void{
        if($this->gotLogs) throw new LogsFileOverrideError();
        $this->logsFile = $file;
        $this->cache = "";
        $this->gotLogs = true;
    }

    /**
     * Unloads the logs file loaded, if there's one loaded
     * @throws LogsFileNotLoaded If there's no logs file loaded
     * @return void
     */
    public function dispose(): void{
        if(!$this->gotLogs) throw new LogsFileNotLoaded();
        $this->logsFile = null;
        $this->gotLogs = false;
    }

    /**
     * Adds the content of the cache in the logs file, using the append method
     * @throws LogsFileNotLoaded If there's no logs file loaded
     * @throws LogsFileAccessError If occoured a error during the file change
     * @return void
     */
    public function writeChanges(): void{
        if(!$this->gotLogs) throw new LogsFileNotLoaded();
        try{
            $resource = fopen($this->logsFile, "a+");
            fwrite($resource, $this->cache);
            fclose($resource);
            $this->cache = "";
        }
        catch(Exception $e){}
    }

    /**
     * Adds a new log line to the cache, it doesn't writes in the
     * file, but it stores it in the internal cache of the class
     *
     * @param string $line The new line to put
     * @throws LogsFileNotLoaded If there's no logs file loaded
     * @return void
     */
    public function addLine(string $line, bool $autoSave = false): void{
        if(!$this->gotLogs) throw new LogsFileNotLoaded();
        $date = date(DEFAULT_DATETIME_F);
        $this->cache .= "\n[$date] $line";
        if($autoSave) $this->writeChanges();
    }

    /**
     * Reads the content of the logs file
     * @throws LogsFileNotLoaded If there's no logs file loaded
     * @return string
     */
    public function getRawContent(): string{
        if(!$this->gotLogs) throw new LogsFileNotLoaded();
        return file_get_contents($this->logsFile);
    }

    /**
     * Returns if the class have a logs file loaded or not
     * @return boolean
     */
    public function gotFile(): bool{ return $this->gotLogs; }

    /**
     * Simple class constructor
     * @param string $file The logs file path to load
     */
    public function __construct(string $file){ $this->load($file); }

    /**
     * Returns the cache attribute value
     * @throws LogsFileNotLoaded If there's no logs file loaded
     * @return string
     */
    public function getCache(){
        if(!$this->gotLogs) throw new LogsFileNotLoaded();
        return $this->cache;
    }
}
