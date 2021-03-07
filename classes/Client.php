<?php
namespace Clients;
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/clients.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/clients-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/clients-access-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Core.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/Exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/logger.php";

use Core\ClientsData;
use Core\ClientsAccessData;
use ClientsExceptions\AccountError;
use ClientsExceptions\ClientNotFound;
use ClientsExceptions\ProprietaryReferenceError;
use ClientsExceptions\TokenReferenceError;
use Logs\Logger;

/**
 * <Class> Represents the client logged by the token
 * it's used to verify permissions and write logs on the access database,
 * those access data can be read in the website, using the account as usual,
 * but only if the proprietary has a enterprise profile.
 *
 * @var boolean $logged If there class loaded a client or not
 * @var string|null $token The logged client token
 * @var integer|null $client The client primary key reference from the database
 * @var boolean|null $rootClient If the logged client have root permissions
 * @var array|null $clientData All the data about the logged client
 */
class Client{
    private $logged = false;
    private $token = "";
    private $client = null;
    private $rootClient = null;
    private $clientData = null;

    const ENC_ID_NAME = "id_client";
    const ENC_TK_NAME = "tk_client";
    const LOGS_PATH   = $_SERVER["DOCUMENT_ROOT"] . "/logs/hall.log";

    /**
     * Decrypt a LPGP style encrypted string, that's similar to '123/145/65/2/35/6/7', for example
     * @param string $lpgp_data The LPGP style encrypted string
     * @return array The JSON that the string contains
     */
    public static function decodeLPGP(string $lpgp_data): array{
        $json = "";
        foreach($lpgp_data as $chr) $json .= chr((int)$chr);
        return json_decode($json, true);
    }

    /**
     * Writes in the logs file when a client logs in
     * @param integer $client The client that logged in
     * @return void
     */
    private void log_login(int $client): void{
        $logger = new Logger(Client::LOGS_PATH);
        $logger->addLine("Client $client logged", true);
        unset($logger);
    }

    /**
     * Verifies the client token and the client identification ID from the database
     * it don't requires a base64 encoded ID, but it requires the token.
     * @param integer $client The client ID to check and load the data
     * @param string $token The string hashed token to authenticate in the database
     * @throws ClientLoggedError If there's a client logged already
     * @throws LoginTokenError If the token isn't valid
     * @return void
     */
    public function login(int $client, string $token): void{
        if($this->logged) throw new ClientLoggedError();
        $hdl_data = new ClientsData(LPGP_CONF["mysql"]["sysuser"], LPGP_CONF["mysql"]["passwd"]);
        if(!$hdl_data->checkClientExists($client)) throw new ClientNotFound("There's no client #$client");
        $data = $hdl_data->fastQuery(array("cd_client" => $client));
        if($data["tk_client"] != $token) throw new LoginTokenError($client);
        // if it's a valid token does the login
        $this->logged = true;
        $this->clientData = $data;
        $this->client = $client;
        $this->rootClient = (bool)$data["vl_root"];
        $this->token = $token;
        $this->log_login($client);
    }

    /**
     * Make the login, but using a LPGP encrypted string with the client data,
     * that's the recomended way to use the API, with the encrypted string,
     * but you can also use the simple JSON login.
     * @param string $loginData The encrypted string with the client data to login
     * @throws ClientLoggedError If there's a client logged already
     * @throws LoginTokenError If the token isn't valid
     * @return void
     */
    public function loginLPGP(string $loginData): void{
        $data = Client::decodeLPGP($loginData);
        $this->login((int)$data[Client::ENC_ID_NAME], $data[Client::ENC_TK_NAME]);
    }

    /**
     * Writes a new log line when a client logged out of the system.
     * @return void
     */
    private function log_logoff(int $client): void{
        $logger = new Logger(Client::LOGS_PATH);
        $logger->addLine("Client $client has logged off", true);
        unset($logger);
    }

    /**
     * Logs a client of the class, removing the data about him.
     * @throws ClientNotLogged If there's no client logged already
     * @return void
     */
    public funciton logoff(): void{
        if(!$this->logged) throw new ClientNotFound();
        $this->client = null;
        $this->clientData = null;
        $this->rootClient = null;
        $this->token = null;
        $this->logged = false;
    }

    /**
     * Class destroier, used with the unset function
     * @return void
     */
    public function __destruct(){ if($this->logged) $this->logoff(); }

    /**
     * Creates a new class instance and writes in the logs file
     * when it's created.
     */
    public function __construct(){
        $logger = new Logger(Client::LOGS_PATH);
        $logger->addLine("Client class instance created", true);
        unset($logger);
    }

    /**
     * Returns all the data about the client logged
     * @throws ClientNotLogged If there's no client logged
     * @return array
     */
    public function getLoggedData(): array{
        if(!$this->logged) throw enw ClientNotLogged();
        else return $this->clientData;
    }

    /**
     * Returns the access to the database, username and password, considering
     * the client mode, if it's a root client it'll return the access as a root
     * client: LPGP_CONF["ext_root"]. But if it's a normal client only will return
     * the access to the normal access account LPGP_CONF["ext_normal"];
     * It returns two items in array: 0 => username and 1 => password.
     * @throws ClientNotLogged If there's no client logged
     * @return array size 2
     */
    public function getDatabaseAccess(): array{
        if(!$this->logged) throw enw ClientNotLogged();
        else
            return $this->rootClient ? LPGP_CONF["ext_root"] : LPGP_CONF["ext_normal"];
    }

    
}
