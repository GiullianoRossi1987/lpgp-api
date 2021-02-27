<?php
namespace Config;
require_once "classes/exceptions/ConfigExceptions.php";


/**
 *
 */
class ExternalConfigurations{
	private $mainConfig = null;
	private $gotConfig = false;
	private $configFileLoaded = null;
	private $writingProtection = false;

    public function is_loaded(): bool{ return $this->gotConfig; }

    public function getMainConfig(){
        if($this->gotConfig) return $this->mainConfig;
    }

    public function getConfigFile(){
        if($this->gotConfig) return $this->configFileLoaded;
    }

    public function is_protected(){
        if($this->gotConfig) return $this->writingProtection;
    }

    public function load(string $config, bool $protected = true): void{
        if($this->gotConfig) throw new ConfigurationsFileLoaded();
        $rcr = fopen($config, $protected ? "r" : "w");
        if(!$rcr) throw new LoadingError($config);
        $content = fread($rcr, filesize($config));
        fclose($rcr);
        // setup the vars
        $this->mainConfig = json_decode($content, true);
        $this->configFileLoaded = $config;
        $this->writingProtection = $protected;
        $this->gotConfig = true;
    }

    public function unload(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $this->mainConfig = null;
    	$this->gotConfig = false;
    	$this->configFileLoaded = null;
    	$this->writingProtection = false;
    }

    public function commit(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->writingProtection) throw new WriteProtectionError();
        $dumped = json_encode($this->mainConfig);
        $rsc = fopen($this->configFileLoaded, "w");
        fwrite($rsc, $dumped);
        fclose($rsc);
    }

    public function __construct(string $config, bool $protected){
        if(!$this->gotConfig) $this->load($config, $protected);
    }

    public function __destruct(){
        if($this->gotConfig) $this->unload();
    }

    public function getApacheData(): array{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->mainConfig["apache"];
    }

    public function getMysqlData(): array{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->mainConfig["mysql"];
    }

    public function getMysqlUser(): string{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->mainConfig["mysql"]["sysuser"];
    }

    public function getMysqlPasswd(): string{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->mainConfig["mysql"]["passwd"];
    }

    public function getMysqlDB(): string{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->mainConfig["mysql"]["db"];
    }

    public function getNormalClientAccess(): array{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->mainConfig["mysql"]["ext_normal"];
    }

    public function getRootClientAccess(): array{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        else return $this->mainConfig["mysql"]["ext_root"];
    }

    // SETTERS

    public function setMysqlUser(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->writingProtection) throw new WriteProtectionError();
        $this->mainConfig["mysql"]["sysuser"] = $value;
    }

    public function setMysqlPasswd(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->writingProtection) throw new WriteProtectionError();
        $this->mainConfig["mysql"]["passwd"] = $value;
    }

    public function setMysqlDB(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->writingProtection) throw new WriteProtectionError();
        $this->mainConfig["mysql"]["db"] = $value;
    }

    public function setNormalClientAccess(array $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->writingProtection) throw new WriteProtectionError();
        $this->mainConfig["mysqli"]["ext_normal"] = $value;
    }

    public function setRootClientAccess(array $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->writingProtection) throw new WriteProtectionError();
        $this->mainConfig["mysqli"]["ext_root"] = $value;
    }
}

?>
