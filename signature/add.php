<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, X-Requested-With');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json, charset=utf-8');

require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Client.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/users-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/proprietaries-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/Exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/signatures-data.php";

use Clients\Client;
use Core\UsersData;
use Core\ProprietariesData;
use Core\SignaturesData;
// exceptions
// proprietaries exceptions
use ClientsExceptions\TokenReferenceError;
use ProprietariesExceptions\ProprietaryNotFound;
use ProprietariesExceptions\AuthenticationError;


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
    $login_data = $cl_obj->getDatabaseAccess();
    $sig = new SignaturesData($login_data[0], $login_data[1]);
    // proceed to the other methods
    if(isset($_GET["add"])){
        try {
            $to_add = json_decode($_GET["add"], true);
            $id_code = 0;
            for($i = 0; $i < count(SignaturesData::CODES); ++$i){
                if($to_add["hash"] == SignaturesData::CODES[$i]){
                    $id_code = $i;
                    break;
                }
            }
            $sig->addSignature($cl_obj->getPropData()["cd_proprietary"], $to_add["password"], $id_code, $to_add["encode"]);
            die(json_encode(array(
                "status" => 0,
                "message" => "Signature Created"
            )));
        }
        catch (\Exception $e) {
            die(json_encode(array(
                "status" => 2,
                "error" => $e->getMessage()
            )));
        }
    }

}
else if(isset($_GET["info"])) die(json_encode(array(
    "Sysdone" => 0
)));
else die(json_encode(array(
    "status" => 1,
    "error" => "Key not referred in the options of the URL"
)));
