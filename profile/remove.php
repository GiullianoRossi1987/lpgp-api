<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, X-Requested-With');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json, charset=utf-8');

require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Client.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/proprietaries-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/Exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/signatures-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/clients-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/clients-access-data.php";

use Clients\Client;
use Core\UsersData;
use Core\ProprietariesData;
// exceptions
// clients exceptions
use ClientsExceptions\AccountError;
use ClientsExceptions\ClientNotFound;
use ClientsExceptions\ProprietaryReferenceError;
// proprietaries exceptions
use ClientsExceptions\TokenReferenceError;
use ProprietariesExceptions\ProprietaryNotFound;
use ProprietariesExceptions\AuthenticationError;
// users exceptions
use UsersSystemExceptions\UserNotFound;
use UsersSystemExceptions\UserKeyNotFound;
use Core\ClientsData;

if(isset($_GET["client-key"])){
    $cl_obj = new Client();
    if(isset($_GET["lpgp_mode"]) && $_GET["lpgp_mode"] == "t"){
        try{
            $cl_obj->loginLPGP($_GET["client-key"]);
        }
        catch(Exception $e){
            die(json_encode(array(
                "status" => "2",
                "error" => $e->getMessage()
            )));
        }
    }
    else{
        try{
            $data = json_decode($_GET["client-key"], true);
            $cl_obj->login($data["id"], $data["token"]);
        }
        catch(Exception $e){
            die(json_encode(array(
                "status" => "2",
                "error" => $e->getMessage()
            )));
        }
    }
    if(!$cl_obj->isRoot()){
        die(json_encode(array(
            "status" => 1,
            "error" => "Only clients with root permissions can remove a profile"
        )));
    }
    $login_data = $cl_obj->getDatabaseAccess();
    $prp_obj = new ProprietariesData($login_data[0], $login_data[1]);
    try{
        $prp_obj->delProprietary((int)$cl_obj->getPropData()["cd_proprietary"]);
    }
    catch(Exception $e){
        die(json_encode(array(
            "status" => 2,
            "error" => $e->getMessage()
        )));
    }
}
else if(isset($_GET["info"])) die(json_encode(array(
    "Sysdone" => 0
)));
else die(json_encode(array(
    "status" => 1,
    "error" => "Key not referred in the options of the URL"
)));
