<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, X-Requested-With');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json, charset=utf-8');

require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Client.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/users-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/proprietaries-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/Exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/clients-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/ErrorHandler.php";

use Clients\Client;
use Core\UsersData;
use Core\ProprietariesData;
use Core\ClientsData;
use ErrorHandle\ErrorHandler;
// exceptions
// proprietaries exceptions
use ClientsExceptions\TokenReferenceError;
use ProprietariesExceptions\ProprietaryNotFound;
use ProprietariesExceptions\AuthenticationError;
// clients exceptions
use SignaturesExceptions\SignaturesAuthError;

$errorHandler = new ErrorHandler($_SERVER["DOCUMENT_ROOT"] . "/logs/profile.log");

if(isset($_GET["client-key"])){
    $cl_obj = new Client();
    if(isset($_GET["lpgp_mode"]) && $_GET["lpgp_mode"] == "t"){
        try{
            $cl_obj->loginLPGP($_GET["client-key"]);
        }
        catch(Exception $e){
            die($errorHandler->throwError(2, $e->getMessage()));
        }
    }
    else{
        try{
            $data = json_decode($_GET["client-key"], true);
            $cl_obj->login($data["id"], $data["token"]);
        }
        catch(Exception $e){
            die($errorHandler->throwError(2, $e->getMessage()));
        }
    }
    if(!$cl_obj->isRoot()) die($errorHandler->throwError(1, "Only root clients can retrieve the encoded content of other client!"));
    $login_data = $cl_obj->getDatabaseAccess();
    // proceed to the other methods
    // CODE DOWN HERE vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
    $cdo = new ClientsData($login_data[0], $login_data[1]);
    try{
        if(isset($_GET["data_json"])){
            $data = json_decode($_GET["data_json"]);
            $cdo->addClient($data["name"], $cl_obj->getPropData()[0]["nm_proprietary"], $data["root"]);
            die($errorHandler->throwError(0, "Success"));
        }
        else if(isset($_GET["name"]) && isset($_GET["root"])){
            $cdo->addClient($_GET["name"], $cl_obj->getPropData()[0]["nm_proprietary"], $_GET["root"]);
            die($errorHandler->throwError(0, "Success"));
        }
        else
            die($errorHandler->throwError(3, "No client reference found"));
    }
    catch(Exception $e){ die($errorHandler->throwError(1, $e->getMessage())); }
}
else if(isset($_GET["info"])) die(json_encode(array(
    "Sysdone" => 0
)));
else die($errorHandler->throwError(3, "Key value not specified - (update)"));
