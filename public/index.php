<?php
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;


if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}


require '../vendor/autoload.php';

session_start();

class_alias('\Cartalyst\Sentry\Facades\Native\Sentry', 'Sentry');

use \RedBeanPHP\R as R;

error_reporting( -1 );
ini_set( 'display_errors', 'On' );


//todo: swap this for .env
// require 'config.php';

//todo: check this comes in with composer
//require 'vendor/redbeanphp/rb.php';

//todo: check this comes in with composer
// require_once 'vendor/wideimage/WideImage.php';

//todo: check this comes in with composer
// require 'vendor/simple_html_dom/simple_html_dom.php';


include '../src/class/upload.class.php';
include_once '../public/resources/UberGallery.php';

$dotenv = new Dotenv\Dotenv('../');
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);


$db_host=getenv('DB_HOST');
$db_name=getenv('DB_NAME');
$db_user=getenv('DB_USER');
$db_pass=getenv('DB_PASS');

// set up database connection
R::setup( 'mysql:host='. $db_host .';dbname='. $db_name, $db_user, $db_pass );
R::debug( TRUE );
R::freeze( true );


$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App( $settings );

//set default conditions for route parameters
// \Slim\Route::setDefaultConditions( array(
//     'id' => '[0-9]{1,}'
//   ) );


// define( 'MY_APP_BASE', $app->request()->getRootUri() );
// define( 'ADMIN_BASE', '/admin' );


$dsn = 'mysql:dbname='  . $db_name .   ';host=' . $db_host ;
Sentry::setupDatabaseResolver( new PDO( $dsn, $db_user, $db_pass ) );
$currentUser = Sentry::getUser();


// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register routes
// require __DIR__ . '/../src/routes.php';


$db = new mysqli( $db_host, $db_user, $db_pass, $db_name );
if ( $db->connect_errno > 0 ) {
  die( 'Unable to connect to database [' . $db->connect_error . ']' );
}
$query = "SELECT title, alias FROM `content`";
$result = $db->query( $query );
if ( $result === false ) {
  trigger_error( 'Wrong SQL: ' . $query . ' Error: ' . $db->error, E_USER_ERROR );
} else {
  $result->data_seek( 0 );
  while ( $row = $result->fetch_assoc() ) {
    $menu[]=array( 'alias'=>$row['alias'], 'title'=>$row['title'] );
  }
}


// Register middleware
require __DIR__ . '/../src/middleware.php';


$twig->addGlobal( 'menu',  $menu );


if ( !empty( $currentUser ) ) {
  if ( $currentUser->hasAnyAccess( array( 'admin' ) ) ) {
    $twig->addGlobal( 'has_admin_access', 1 );
  }
}
if ( ! Sentry::check() ) {
  //not logged in
}else {
  $twig->addGlobal( 'logged_in', 1 );
}
$routeFiles = (array) glob( '../src/routes/*.php' );
foreach ( $routeFiles as $routeFile ) {
  require $routeFile;
}

$app->run();

?>