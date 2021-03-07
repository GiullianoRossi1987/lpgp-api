<?php
namespace Clients;
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/exceptions/clients.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/clients-data.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/clients-access-data.php";

use Core\ClientsData;
use Core\ClientsAccessData;

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
 * @var
 */
class Client{

}
