<?php
// pages
$app->get( '/pages', 'authenticate', function () use( $app , $twig ) {
    $currentUser = Sentry::getUser();
    try {
      $data = R::findAll( 'pages' );
    }
    catch( Exception $e ) {
      $app->response()->status( 400 );
      $app->response()->header( 'X-Status-Reason', $e->getMessage() );
    }
    $template = $twig->loadTemplate( 'admin_pages.twig' );
    echo $template->render( array(
        'data' => $data
      ) );
  } );


$app->get( '/page/:id', 'authenticate', function ( $page_id ) use( $app, $twig ) {
    $currentUser = Sentry::getUser();
    $itinerary = '';
    if ( !empty( $_GET['error'] ) ) {
      $error = $_GET['error'];
    }
    if ( !empty( $error ) ) {
      print_r( $error );
    }
    try {
      $data = R::findOne( 'pages', 'id=?', array(
          $page_id
        ) );
      $template = $twig->loadTemplate( 'admin_page.twig' );
      echo $template->render( array(
          'data' => $data
        ) );
    }
    catch( Exception $e ) {
      $app->response()->status( 400 );
      $app->response()->header( 'X-Status-Reason', $e->getMessage() );
    }
  } );


$app->post( '/page/new', function () use( $app ) {
    $request = $app->request();
    $body = $request->getBody();
    $page = R::dispense( 'pages' );
    $pagebody = (string)$body;
    $page->title = str_replace( '+', ' ', substr( $pagebody, 5 ) );
    $page->alias = slugify( $page->title );
    $numberofpages = R::count( 'pages' );
    $id = R::store( $page );
    $app->redirect( MY_APP_BASE . '/pages' );
  } );


$app->get( '/page/:id/delete', function ( $page_id ) use( $app ) {
    $data = R::find( 'pages' );
    $page = R::findOne( 'pages', 'id=?', array(
        $page_id
      ) );
    if ( !empty( $page->gallery_path ) ) {
      rmdir( $page->gallery_path );
    }
    R::trash( $page ); //for one bean
    $app->redirect( MY_APP_BASE . '/pages' );
  } );


$app->post( '/page/:id/update', function ( $page_id ) use( $app ) {
    $request = $app->request();
    $body = $request->post();
    $output = $body;
    $data = R::find( 'pages' );
    $page = R::findOne( 'pages', 'id=?', array(
        $page_id
      ) );
    $upload = new Upload;
    $error = '';
    $page->title = $output['title'];
    $page->alias = slugify( $output['title'] );
    $page->body = $output['body'];
    $page->description = $output['description'];
    $page->date_modified = date( "Y-m-d H:i:s" );
    $id = R::store( $page );
    $app->redirect( MY_APP_BASE . '/page/' . $page_id . '?error=' . $error );
  } );