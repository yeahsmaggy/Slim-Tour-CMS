<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

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


// die();
