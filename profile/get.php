<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, X-Requested-With');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json, charset=utf-8');

require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Client.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/users-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/proprietaries-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/Exceptions.php";

use Clients\Client;
use Core\UsersData;
use Core\ProprietariesData;
// exceptions
// clients exceptions
use ClientsExceptions\AccountError;
use ClientsExceptions\ClientNotFound;
use ClientsExceptions\ProprietaryReferenceError;
use ClientsExceptions\TokenReferenceError;
// proprietaries exceptions
use ProprietariesExceptions\ProprietaryNotFound;
use ProprietariesExceptions\AuthenticationError;
// users exceptions
use UsersSystemExceptions\UserNotFound;
use UsersSystemExceptions\UserKeyNotFound;

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
    // proceed to the other methods
    if(isset($_GET["prop-key"])){
        // uses the proprietary authentication,
        $prp_obj = new ProprietariesData($login_data[0], $login_data[1]);
    }
}
else if(isset($_GET["info"])) die(json_encode(array(
    "Sysdone" => 0
)));
?>
