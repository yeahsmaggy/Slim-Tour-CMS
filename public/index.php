<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';
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
include_once '../src/resources/UberGallery.php';
$dotenv = new Dotenv\Dotenv('../');
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);


$db_host=getenv('DB_HOST');
$db_name=getenv('DB_NAME');
$db_user=getenv('DB_USER');
$db_pass=getenv('DB_PASS');



$app = new \Slim\Slim( array(
    'cookies.encrypt' => true,
    'cookies.secret_key' => 'my_secret_key',
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC,
    // 'cookies.domain' => '.admin.georiders.com'
  ) );


define( 'MY_APP_BASE', $app->request()->getRootUri() );
define( 'ADMIN_BASE', '/admin' );

$dsn = 'mysql:DB_NAME='  . $db_name .   ';host=' . $db_host ;




Sentry::setupDatabaseResolver( new PDO( $dsn, $db_user, $db_pass ) );

$loader = new Twig_Loader_Filesystem( array( '../src/views', '../src/views/admin', '../src/views/frontend' ) );
$twig = new Twig_Environment( $loader, array(
    'cache' => './compilation_cache',
    'debug' => true
  ) );
$twig->addExtension( new Twig_Extension_Debug() );




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



$twig->addGlobal( 'menu',  $menu );
$currentUser = Sentry::getUser();
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
$routeFiles = (array) glob( 'routes/*.php' );
foreach ( $routeFiles as $routeFile ) {
  require $routeFile;
}



//set default conditions for route parameters
\Slim\Route::setDefaultConditions( array(
    'id' => '[0-9]{1,}'
  ) );
// set up database connection
R::setup( 'mysql:host='. $db_host .';DB_NAME='. $db_name, $db_user, $db_pass );
// R::debug( TRUE );
R::freeze( true );
// route middleware for simple API authentication
function authenticate( \Slim\Route $route ) {
  $app = \Slim\Slim::getInstance();
  // check if user logged in
  //user andrewwe_andywel pass N3Lp9mOE
  $dsn = 'mysql:DB_NAME='.$db_name.';host='.$db_host;
  $u = $db_user;
  $p = $db_pass;
  Sentry::setupDatabaseResolver( new PDO( $dsn, $u, $p ) );
  // check if user logged in
  if ( Sentry::check() ) {
    $currentUser = Sentry::getUser();
    if ( !$currentUser->hasAccess( 'admin' ) ) {
      // throw new Exception ('You don\'t have permission to view this page.');
      $app->redirect( '/admin/profile' );
    }
  }
  else {
    $app->redirect( '/admin/login' );
  }
}




// route middleware for simple API authentication
function authenticate_user( \Slim\Route $route ) {
  $app = \Slim\Slim::getInstance();
  // check if user logged in
  $dsn = 'mysql:DB_NAME='.$db_name.';host='.$db_host;
  $u = $db_user;
  $p = $db_pass;
  Sentry::setupDatabaseResolver( new PDO( $dsn, $u, $p ) );
  // check if user logged in
  if ( Sentry::check() ) {
    $currentUser = Sentry::getUser();
  }
  else {
    $app->redirect( '/admin/login' );
  }
}
$app->run();

// die();


//todo: where is this being used?
// function slugify( $text ) {
//   // replace non letter or digits by -
//   $text = preg_replace( '~[^\\pL\d]+~u', '-', $text );
//   // trim
//   $text = trim( $text, '-' );
//   // transliterate
//   $text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );
//   // lowercase
//   $text = strtolower( $text );
//   // remove unwanted characters
//   $text = preg_replace( '~[^-\w]+~', '', $text );
//   if ( empty( $text ) ) {
//     return 'n-a';
//   }
//   return $text;
// }
?>