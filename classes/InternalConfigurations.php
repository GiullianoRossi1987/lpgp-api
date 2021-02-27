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
    private $document = null;
    private $configLoaded = null;
    private $gotConfig = false;
    private $readonly = false;
    private $root_element = null;

    private function parse(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $this->logsPath = $this->root_element->getElementsByTagName("logsPath")->item(0)->getAttribute("value");
        $this->libPath = $this->root_element->getElementsByTagName("libPath")->item(0)->getAttribute("value");
        $this->configPath = $this->root_element->getElementsByTagName("configPath")->item(0)->getAttribute("value");
    }

    private function dump(bool $save = true): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        if($this->readonly) throw new WriteProtectionError();
        $this->root_element->getElementsByTagName("logsPath")->item(0)->setAttribute("value", $this->logsPath);
        $this->root_element->getElementsByTagName("configPath")->item(0)->setAttribute("value", $this->configPath);
        $this->root_element->getElementsByTagName("libPath")->item(0)->setAttribute("value", $this->libPath);
        $this->document->document->replaceChild($this->document->getElementsByTagName("config")->item(0), $this->root_element);
        if($save) $this->document->save($this->configLoaded);
    }

    public function getDOM(): DOMDocument{
        return $this->document;
    }

    public function load(string $config, bool $protected = true): void{
        if($this->gotConfig) throw new ConfigurationsFileLoaded();
        $this->document = new DOMDocument("1.0", "utf-8");
        $this->document->load($config);
        $this->root_element = $this->document->getElementsByTagName("config")->item(0);
        $this->configLoaded = $config;
        $this->readonly = $protected;
        $this->gotConfig = true;
        $this->parse();
    }

    public function __construct(string $config, bool $protected = true){
        $this->load($config, $protected);
    }

    public function close(): void{
        if(!$this->gotConfig) throw new ConfigurationsNotLoaded();
        $this->document = new DOMDocument("1.0", "utf-8");
        $this->configLoaded = null;
        $this->readonly = false;
        $this->gotConfig = false;
        $this->logsPath = null;
        $this->libPath = null;
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
