<?php
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, X-Requested-With');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json, charset=utf-8');

require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Client.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/users-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/proprietaries-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/Exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/ErrorHandler.php";

//error handler
use ErrorHandle\ErrorHandler;

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

$errorHandler = new ErrorHandler($_SERVER["DOCUMENT_ROOT"] . "/logs/profile.log");

/**
 * Function created to parse the associative parameters of the
 * received new data to be merged and updated to the profile selected
 * @param array $params The parameters received via request
 * @return array|null Only return null in case of error
 */
function parseParams(array $params): ?array{
    $parsed = array();
    foreach($params as $key => $value){
        switch($key){
            case "name":
                $params["nm_proprietary"] = $value;
                break;
            case "password":
                $params["vl_password"] = $value;
                break;
            case "email":
                $params["vl_email"] = $value;
                break;
            default:
                return null;
        }
    }
    return $parsed;
}

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
    if(!$cl_obj->isRoot())
        die($errorHandler->throwError(1, "Only root clients can update profile data - update"));
    $login_data = $cl_obj->getDatabaseAccess();
    // proceed to the other methods
    if(isset($_GET["prop-key"]) && isset($_GET["new-data"])){
        // uses the proprietary authentication,
        if($_GET["prop-key"] != $cl_obj->getPropData()["vl_key"]){
            die($errorHandler->throwError(2, "You only can change your own profile data - update"));
        }
        $prp_obj = new ProprietariesData($login_data[0], $login_data[1]);
        try{
            $parsed = parseParams(json_decode($_GET["new-data"], true));
            if(is_null($parsed)){
                die($errorHandler->throwError(2, "Invalid fields parameter received - update"));
            }
            $prp_obj->fastUpdate($parsed, $cl_obj->getPropData()["cd_proprietary"]);
            die($errorHandler->throwError($errorHandler::NON_ERR_CODE, "Changes done successfully"));
        }
        catch(Exception $e){
            die($errorHandler->throwError(2, $e->getMessage()));
        }
    }
    else die($errorHandler->throwError(3, "Key value not specified - (update)"));
}
else if(isset($_GET["info"])) die(json_encode(array(
    "Sysdone" => 0
)));
?>
