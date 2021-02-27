<?php
namespace Config;
require_once "classes/exceptions/ConfigExceptions.php";
use \DOMDocument;
use \DOMElement;
use \DOMNode;

/**
 *
 */
class InternalConfigurations{
    private $logsPath = null;
    private $configPath = null;
    private $libPath = null;
    private $document = new DOMDocument("1.0", "utf-8");
    private $configLoaded = null;
    private $gotConfig = false;
    private $readonly = false;

    private function parse(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $this->logsPath = $this->document->getElementsByTagName("logsPath")->item(0)->nodeValue;
        $this->libPath = $this->document->getElementsByTagName("libPath")->item(0)->nodeValue;
        $this->configPath = $this->document->getElementsByTagName("configPath")->item(0)->nodeValue;
    }

    private function dump(bool $save = true): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->readonly) throw new WriteProtectionError();
        $this->document->getElementsByTagName("logsPath")->item(0)->nodeValue = $this->logsPath;
        $this->document->getElementsByTagName("configPath")->item(0)->nodeValue = $this->configPath;
        $this->document->getElementsByTagName("libPath")->item(0)->nodeValue = $this->libPath;
        if($save) $this->document->save($this->configLoaded);
    }

    public function load(string $config, bool $protected = true): void{
        if($this->gotConfig) throw new ConfigurationsFileLoaded();
        $this->document->load($config);
        $this->configLoaded = $config;
        $this->readonly = $protected;
        $this->gotConfig = true;
        parse();
    }

    public function close(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $this->document = new DOMDocument("1.0", "utf-8");
        $this->configLoaded = null;
        $this->readonly = false;
        $this->gotConfig = false;
        $this->logsPath = nul
        $this->libPath = null
        $this->configPath = null;
    }

    public function get_logsPath(): string{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        return $this->logsPath;
    }

    public function get_configPath(): string{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        return $this->configPath;
    }

    public function get_libPath(): string{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        return $this->libPath;
    }

    public function set_logsPath(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->readonly) throw new WriteProtectionError();
        $this->logsPath = $value;
    }

    public function set_configPath(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->readonly) throw new WriteProtectionError();
        $this->configPath = $value;
    }

    public function set_libPath(string $value): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->readonly) throw new WriteProtectionError();
        $this->libPath = $value;
    }
}
