<?php 
/** 
 * Setup the database connection and other global objects needed to use the api
 * 
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Cache-Control: max-age=0');
header("Content-Type: application/json; charset=UTF-8");

date_default_timezone_set("America/Chicago");

global $pdo, $YT_KEY;

// Vendor autoload if needed later
//require_once dirname(__FILE__).'/vendor/autoload.php';

// Various config varibles. DB, YT_Key, etc.
require_once 'api-config.php';

// Various helper functions
require_once 'api-functions.php';

require_once 'classes/Video.php';
require_once 'classes/Channel.php';
require_once 'classes/User.php';


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
	$pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
	throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Setup our base classes here. These are used in most files so let's just set them up on every call
$Video = new Video($pdo, $YT_KEY);
$Channel = new Channel($pdo, $YT_KEY);
$User = new User($pdo); 