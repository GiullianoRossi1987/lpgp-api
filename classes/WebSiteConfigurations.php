<?php
namespace Config;
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/config_exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/interfaces/i_FileReader.php";
use Exception;
use General\iFileReader;

/**
 * Class created to manage the configurations from the website folder.
 * Remembering that to use the LPGP API Server you must have the LPGP Server
 * installed too. To learn more about the configurations file of the WebSite
 * Server, check the ConfigManager class file [lpgp-new/config]
 * link to github file:
 * https://github.com/GiullianoRossi1987/lpgp-new/blob/main/config/configmanager.php
 *
 * Attributes
 * @var string $configFile The path to the loaded configurations file
 * @var boolean $gotConfig If the class have a configurations file loaded
 * @var array|null $config The configurations themselfs
 *
 */
class WebSiteConfigurations implements iFileReader{
    private $configFile = "";
    private $gotConfig = false;
    private $config = [];

    /**
     * Loads a new configurations file to the class
     * @param string $file Path to the website configurations file
     * @param array|[] $args Not used, just leave't blanck
     * @throws ConfigurationsLoadedError If there's a configurations file loaded already
     * @return void
     */
    public function load(string $file, $args = []): void{
        if($this->gotConfig) throw new ConfigurationsLoadedError();
        $this->configFile = $file;
        $this->config = json_decode(file_get_contents($file), true);
        $this->gotConfig = true;
    }

    /**
     * Unloads the configurations file loaded.
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded already
     * @return void
     */
    public function dispose(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $this->config = null;
        $this->configFile = "";
        $this->gotConfig = false;
    }

    /**
     * Class constructor, it already loads the configurations file without
     * requiring the args.
     * @param string $file The path to the website configurations file
     * @throws ConfigurationsLoadedError If there're configurations loaded already
     */
    public function __construct(string $file){ $this->load($file); }

    /**
     * Returns the plain text content of the website configurations file loaded
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return string
     */
    public function getRawContent(): string{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $content = file_get_contents($this->configFile);
        return $content;
    }

    /**
     * Dumps the configurations and write them in the configurations file
     * Not recommended to use, unless you know exactly what you're doing.
     * 'cause when changed on mere configurations it have to be adapted
     * on the website server too.
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return void
     */
    public function writeChanges(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $dumped = json_encode($this->config);
        file_put_contents($this->configFile, $dumped);
        // unset($dumped);
    }

    /**
     * Returns the website configurations array
     * @throws ConfigurationsNotLoaded If there's no configurations file loaded
     * @return array
     */
    public function getConfig(): array{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        return $this->config;
    }

    /**
     * Returns if the class have loaded a configurations file
     * @return boolean
     */
    public function gotFile(): bool{ return $this->gotConfig; }
}


?>
