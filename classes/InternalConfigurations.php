<?php
namespace Config;
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/config_exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/interfaces/i_FileReader.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/logs-system.php";

use \DOMDocument;
use \DOMNode;
use \DOMNodelist;
use \DOMElement;
use General\iFileReader;

if(!defined("API_CONFIG_PATH")) define("API_CONFIG_PATH", $_SERVER["DOCUMENT_ROOT"] . "/config/config.xml");

/**
 * <Class <iFileReader> > This class manages the configurations file of the API server,
 * this file contains the location of the external configurations file,
 * and other important paths to the API server.
 *
 * @var string $configFile Path to the configurations file loaded;
 * @var boolean $gotConfig If the class have a configurations file loaded.
 * @var DOMDocument $DOMReader The XML files reader class
 * @var DOMElement  $root The object with the configurations
 *
 * Attributes for the configurations loaded
 * @var string $ext_config The website configurations file path, from the LPGP Server
 * @var string $error_log Path to the error logs file of the LPGP API Server
 * @var string $general_logs Path to the logs folder of the API Server
 *
 * Constants
 * @var VALUE_ATTR_NAME The name of the default attribute for the XML elements value.
 * @var ROOT_NAME The name of the root XML element
 * @var EXT_CONFIG_NAME The name of the external configurations XML element
 * @var ERR_LOG_NAME The name of the error logs file path XML element
 * @var GENERAL_LOGS_NAME The name of the general logs folder path XML element
 */
class InternalConfigurations implements iFileReader{

    // base attributes
    private $configFile = "";
    private $gotConfig = false;
    private $DOMReader = null;
    private $root = null;

    // configurations attributes
    private $ext_config   = "";
    private $error_log    = "";
    private $general_logs = "";

    const VALUE_ATTR_NAME   = "value";
    const ROOT_NAME         = "api_config";
    const EXT_CONFIG_NAME   = "ext_config";
    const ERR_LOG_NAME      = "error_log_path";
    const GENERAL_LOGS_NAME = "gen_logs_path";

    /**
     * Loads the attributes of the configurations file loaded and sets them to
     * a associative array on attribute $configurations.
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return void
     */
    private function parse(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        // die(var_dump($this->root->getElementsByTagName(InternalConfigurations::ERR_LOG_NAME)->item(0)));
        $this->ext_config   = $this->root->getElementsByTagName(InternalConfigurations::EXT_CONFIG_NAME)->item(0)->getAttribute(InternalConfigurations::VALUE_ATTR_NAME);
        $this->error_log    = $this->root->getElementsByTagName(InternalConfigurations::ERR_LOG_NAME)->item(0)->getAttribute(InternalConfigurations::VALUE_ATTR_NAME);
        $this->general_logs = $this->root->getElementsByTagName(InternalConfigurations::GENERAL_LOGS_NAME)->item(0)->getAttribute(InternalConfigurations::VALUE_ATTR_NAME);
    }

    /**
     * Unset the configurations attributes of the class
     */
    private function unparse(): void{
        $this->ext_config   = "";
        $this->error_log    = "";
        $this->general_logs = "";
    }

    /**
     * This function loads a XML configurations file to the class instance,
     * It checks if there's no other configurations file loaded and then loads it.
     * @throws ConfigurationsLoadedError If there's a configurations file loaded already
     * @return void
     */
    public function load(string $file, $args = []): void{
        if($this->gotConfig) throw new ConfigurationsLoadedError();
        $this->DOMReader = new DOMDocument("1.0", "utf-8");
        $this->DOMReader->load($file);
        $this->root = $this->DOMReader->getElementsByTagName(InternalConfigurations::ROOT_NAME)->item(0);
        $this->gotConfig = true;
        $this->configFile = $file;
        $this->parse();
    }

    /**
     *
     */
    public function __construct(string $file, $args = []){
        $this->load($file, $args);
    }

    /**
     * Unloads the configurations file loaded.
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded already
     * @return void
     */
    public function dispose(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $this->gotConfig = false;
        $this->DOMReader = null;
        $this->root = null;
        $this->unparse();
    }

    public function gotFile(): bool{ return $this->gotFile; }

    /**
     * Writes the current data of the configurations attributes.
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return void
     */
    public function writeChanges(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();

        // External Configurations commit
        $this->root->getElementsByTagName(InternalConfigurations::EXT_CONFIG_NAME)->item(0)->setAttribute(
            InternalConfigurations::VALUE_ATTR_NAME,
            $this->ext_config
        );
        // General Logs Path Commit
        $this->root->getElementsByTagName(InternalConfigurations::GENERAL_LOGS_NAME)->item(0)->setAttribute(
            InternalConfigurations::VALUE_ATTR_NAME,
            $this->general_logs
        );
        // Error Logs Commit
        $this->root->getElementsByTagName(InternalConfigurations::ERR_LOG_NAME)->item(0)->setAttribute(
            InternalConfigurations::VALUE_ATTR_NAME,
            $this->error_log
        );
        // sets the new child
        $old_root = $this->DOMReader->getElementsByTagName(InternalConfigurations::ROOT_NAME)->item(0);
        $this->DOMReader->replaceChild($old_root, $this->root);
        $this->DOMReader->save($this->configFile);
        unset($old_root);
    }

    /**
     * Returns the raw content of the loaded configurations file.
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     */
    public function getRawContent(){
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $rsc = fopen($this->configFile, "r");
        $content = fread($rsc, filesize($this->configFile));
        fclose($rsc);
        return $content;
    }
    // getters and setters

    /**
     * Returns the external configurations path from the loaded configurations file
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return string
     */
    public function getExternalConfigurations(){
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->ext_config;
    }

    /**
     * Returns the general logs path from the configurations file loaded
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return string
     */
    public function getGeneralLogsPath(){
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->general_logs;
    }

    /**
     * Returns the error logs file path from the configurations loaded
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return string
     */
    public function getErrorLogs(){
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->error_log;
    }

    /**
     * Sets a new string value to the external configurations file,
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @param string $value The new path for the configurations file
     * @return void
     */
    public function setExternalConfigurations(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else $this->ext_config = $value;
    }

    /**
     * Sets a new string value to the general logs path attribute.
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @param string $value The new path to the general logs folder
     * @return void
     */
    public function setGeneralLogsPath(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else $this->general_logs = $value;
    }

    /**
     * Sets a new string value to the error logs path attribute
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @param string $value The new value to the error logs
     * @return void
     */
    public function setErrorLogs(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else $this->error_log = $value;
    }
}

?>
