<?php
error_reporting( -1 );
ini_set( 'display_errors', 'On' );


require 'config.php';

require 'vendor/autoload.php';

class_alias( 'Cartalyst\Sentry\Facades\Native\Sentry', 'Sentry' );

require 'vendor/redbeanphp/rb.php';

require_once 'vendor/wideimage/WideImage.php';

include 'class/upload.class.php';

require 'vendor/simple_html_dom/simple_html_dom.php';

include_once 'resources/UberGallery.php';


$app = new \Slim\Slim( array(
    'cookies.encrypt' => true,
    'cookies.secret_key' => 'my_secret_key',
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC,
    // 'cookies.domain' => '.admin.georiders.com'
  ) );

define( 'MY_APP_BASE', $app->request()->getRootUri() );
define( 'ADMIN_BASE', '/admin' );
$dsn = 'mysql:dbname='  . DBNAME .   ';host=' . DBHOST ;
$u = DBUSER;
$p = DBPASS;
Cartalyst\Sentry\Facades\Native\Sentry::setupDatabaseResolver( new PDO( $dsn, $u, $p ) );
$loader = new Twig_Loader_Filesystem( array( './views', './views/admin', './views/frontend' ) );
$twig = new Twig_Environment( $loader, array(
    'cache' => './compilation_cache',
    'debug' => true
  ) );
$twig->addExtension( new Twig_Extension_Debug() );
$db = new mysqli( DBHOST, DBUSER, DBPASS, DBNAME );
if ( $db->connect_errno > 0 ) {
  die( 'Unable to connect to database [' . $db->connect_error . ']' );
}
$query = "SELECT title, alias FROM `pages`";
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
$currentUser = Cartalyst\Sentry\Facades\Native\Sentry::getUser();
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
R::setup( 'mysql:host='. DBHOST .';dbname='. DBNAME, DBUSER, DBPASS );
// R::debug( TRUE );
R::freeze( true );
// route middleware for simple API authentication
function authenticate( \Slim\Route $route ) {
  $app = \Slim\Slim::getInstance();
  // check if user logged in
  //user andrewwe_andywel pass N3Lp9mOE
  $dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST;
  $u = DBUSER;
  $p = DBPASS;
  Cartalyst\Sentry\Facades\Native\Sentry::setupDatabaseResolver( new PDO( $dsn, $u, $p ) );
  // check if user logged in
  if ( Cartalyst\Sentry\Facades\Native\Sentry::check() ) {
    $currentUser = Cartalyst\Sentry\Facades\Native\Sentry::getUser();
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
  $dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST;
  $u = DBUSER;
  $p = DBPASS;
  Cartalyst\Sentry\Facades\Native\Sentry::setupDatabaseResolver( new PDO( $dsn, $u, $p ) );
  // check if user logged in
  if ( Cartalyst\Sentry\Facades\Native\Sentry::check() ) {
    $currentUser = Cartalyst\Sentry\Facades\Native\Sentry::getUser();
  }
  else {
    $app->redirect( '/admin/login' );
  }
}
$app->run();

function slugify( $text ) {
  // replace non letter or digits by -
  $text = preg_replace( '~[^\\pL\d]+~u', '-', $text );
  // trim
  $text = trim( $text, '-' );
  // transliterate
  $text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );
  // lowercase
  $text = strtolower( $text );
  // remove unwanted characters
  $text = preg_replace( '~[^-\w]+~', '', $text );
  if ( empty( $text ) ) {
    return 'n-a';
  }
  return $text;
}
?>