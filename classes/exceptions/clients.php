<?php
namespace Clients;
use \Exception;


/**
 * <Exception> Thrown when the client class tries to access the logged client
 * data but there's no client logged in the system.
 */
class ClientNotLogged extends Exception{
    public function __construct(){
        parent::__construct("Error retrieving the client data, no client logged");
    }
}

/**
 * <Exception> Thrown when the client class tries to override the logged client
 * data.
 */
class ClientLoggedError extends Exception{
    public function __construct(){
        parent::__construct("Client data overriding error");
    }
}

/**
 * <Exception> Thrown when the client token isn't valid, it means that it doesn't
 * match with the token of the referred client on the database.
 */
class LoginTolkenError extends Exception{
    public function __construct($client){
        parent::__construct("Login error, invalid token for client #$client");
    }
}

/**
 * <Exception> Thrown when the client tries to do a action but the client
 * don't have enough permissions. To check the permissions check the iClient
 * interface (classes/interfaces/i_Client.php) docs.
 */
class ClientPermissionError extends Exception{
    public function __construct($client){
        parent::__construct("Permission Error: Client #$client don't have enough permissions!");
    }
}

?>
