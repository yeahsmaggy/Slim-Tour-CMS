<?php
use \RedBeanPHP\R as R;


$app->get( '/admin/tours(/)', 'authenticate', function () use( $app, $twig ) {
    $currentUser = Sentry::getUser();
    try {
      $data = R::findAll( 'tours' );
    }
    catch( Exception $e ) {
      $app->response()->status( 400 );
      $app->response()->header( 'X-Status-Reason', $e->getMessage() );
    }
    $template = $twig->loadTemplate( 'admin_tours.twig' );
    echo $template->render( array(
        'data' => $data
      ) );
  } );


$app->post( '/admin/tours', function () use( $app ) {die;
    $request = $app->request();
    $body = $request->getBody();
    // store tour record
    $tour = R::dispense( 'tours' );
    $tour->name = (string)$body;
    $id = R::store( $tour );
  } );


$app->get( '/admin/tour/:id(/)', 'authenticate', function ( $tour_id ) use( $app, $twig ) {
    $currentUser = Sentry::getUser();
    $itinerary = '';
    if ( !empty( $_GET['error'] ) ) {
      $error = $_GET['error'];
    }
    if ( !empty( $error ) ) {
      print_r( $error );
    }
    try {
      $data = R::findOne( 'tours', 'id=?', array(
          $tour_id
        ) );
      $template = $twig->loadTemplate( 'admin_tour.twig' );
      if ( !empty( $data->background_image ) ) {
        // homepage thumbs
        $imagefile = 'img/background_images/' . $data->background_image;
        if ( file_exists( $imagefile ) ) {
          // create a thumbnail
          $image = WideImage::load( $imagefile );
          $image = $image->resize( 400, 400, "outside" );
          $image = $image->crop( "center", "center", 400, 400 );
          $thumbfile = 'img/background_images/thumb-' . $data->background_image;
          $image->saveToFile( $thumbfile, 70 );
          // create a homepage featured slideshow thumb
          $image = WideImage::load( $imagefile );
          $image = $image->resize( 1600, 550, "outside" );
          $image = $image->crop( "center", "center", 1600, 550 );
          $slidefile = 'img/background_images/slide-' . $data->background_image;
          $image->saveToFile( $slidefile, 70 );
        }
      }
      $itinerary = unserialize( $data->itinerary );
      $tour_facts = unserialize( $data->tour_facts );
      echo $template->render( array(
          'data' => $data,
          'itinerary' => $itinerary,
          'tour_facts' => $tour_facts
        ) );
    }
    catch( Exception $e ) {
      echo '<pre>';
      print_r( $e );
      echo '</pre>';
      $app->response()->status( 400 );
      $app->response()->header( 'X-Status-Reason', $e->getMessage() );
    }
  } );


$app->post( '/tour/new', function () use( $app ) {
    $request = $app->request();
    $body = $request->getBody();
    $tour = R::dispense( 'tours' );
    $page = (string)$body;
    $tour->name = str_replace( '+', ' ', substr( $page, 5 ) );
    $tour->alias = slugify( $tour->name );
    $numberoftours = R::count( 'tours' );
    $id = R::store( $tour );
    $app->redirect( ADMIN_BASE . '/tours' );
  } );


$app->get( '/tour/:id/delete', function ( $tour_id ) use( $app ) {
    $data = R::find( 'tours' );
    $tour = R::findOne( 'tours', 'id=?', array(
        $tour_id
      ) );
    if ( !empty( $tour->gallery_path ) ) {
      if(file_exists($tour->gallery_path)){
         rmdir( $tour->gallery_path );
      }
    }
    R::trash( $tour );
    $app->redirect( ADMIN_BASE . '/tours' );
  } );


$app->post( '/tour/:id/update', function ( $tour_id ) use( $app ) {
    $request = $app->request();
    $body = $request->post();
    $output = $body;
    $data = R::find( 'tours' );
    $tour = R::findOne( 'tours', 'id=?', array(
        $tour_id
      ) );
    $upload = new Upload;
    $error = '';


    if ( !empty( $_FILES['background_image']['name'] ) ) {
      $filename = 'background_image';
      $file_background_image = $_FILES['background_image'];
      $type = true;
      $extensions = array(
        'jpg',
        'png'
      );
    }


    if ( !empty( $_FILES['dossier']['name'] ) ) {
      $filename = 'dossier';
      $file_dossier = $_FILES['dossier'];
      $type = false;
      $extensions = array(
        'pdf'
      );
    }


    if ( !empty( $filename ) ) {
      $upload->upload( $filename, array(
          'folder' => 'img/background_images/',
          'isImage' => $type, // if true it will treat files as images
          'maxSize' => 2, // the max allowed size in MB
          'allowed_extensions' => $extensions, // an array of lowercase allowed extensions, (!) IF EMPTY ALL ARE ALLOWED (!)
          'overwrite' => 1
          // if true it will overwrite the file on the server in case it has the same name
        ) ); // this will do all the work
    }


    if ( $upload->errors ) {
      foreach ( $upload->errors as $error ) {
        $error = "Error: " . $error[0] . " - " . $error[1] . "<br/>";
      }
    }


    if ( $upload->success ) {
      foreach ( $upload->success as $success ) {
        $error = "Success: " . $success[0] . " - " . $success[1] . "<br/>";
      }
    }


    if ( !empty( $file_background_image['name'] ) ) {
      $tour->background_image = $file_background_image['name'];
    }


    if ( !empty( $file_dossier['name'] ) ) {
      $tour->dossier = $file_dossier['name'];
    }


    $tour->name = $output['name'];
    $tour->alias = slugify( $output['name'] );
    $tour->strap = $output['strap'];
    $tour->summary = $output['summary'];
    $gallery_name = strtolower( str_replace( ' ', '_', $tour->name ) );
    $gallery_name = $gallery_name . '_gallery';
    $tour->date_modified = date( "Y-m-d H:i:s" );


    if ( !file_exists( 'galleries/' . $gallery_name ) ) {
      mkdir( 'galleries/' .  $gallery_name, 0777, true );
    }


    $tour->gallery_path = 'galleries/' . $gallery_name;


    if ( !empty( $output['itin'] ) ) {
      $tour->itinerary = serialize( $output['itin'] );
    }


    if ( !empty( $output['tour_facts'] ) ) {
      $tour->tour_facts = serialize( $output['tour_facts'] );
    }

    
    $tour->difficulty_rating = $output['difficulty_rating'];
    $tour->start_date = $output['start_date'];
    $tour->end_date = $output['end_date'];
    $tour->price = $output['price'];
    $tour->tour_code = $output['tour_code'];
    $tour->live = ( !empty( $output['live'] ) == 1 ) ? 'checked' : '';
    $tour->published = ( !empty( $output['published'] ) == 1 ) ? '1' : '0';
    $tour->bookings = $output['bookings'];
    $tour->featured = ( !empty( $output['featured'] ) == 1 ) ? 'checked' : '';
    $tour->type = $output['type'];
    $tour->subtype = $output['subtype'];
    $tour->total_days = $output['total_days'];
    $id = R::store( $tour );
    $app->redirect(  '/admin/tour/' . $tour_id . '?error=' . $error );
  } );