<?php


// profiles
$app->get( '/profile(/)', 'authenticate_user', function () use( $app, $twig ) {

    $currentUser = Sentry::getUser();
    try {
      $data = $currentUser;
      $data = R::getAll( 'SELECT users.first_name, users.email, users.last_name, profile.age, users.id, profile.pedal_type,
profile.type_of_bike,profile.health_condition,profile.height,profile.skill_level,profile.bike_size,profile.helmet,profile.gloves,profile.dietary_requirements,profile.room_type,profile.insurance,profile.number_nights,profile.flight_number,profile.time_of_flight,profile.time_of_transfer,profile.additional_info
FROM users
JOIN profile
WHERE profile.user_id = users.id
AND users.id = :currentuserid
', [':currentuserid' => $data->id] );
      if ( !empty( $_GET['message'] ) ) {
        $data[0]['message'] = 'Your profile updated successfully';
      }
      $template = $twig->loadTemplate( 'profile.twig' );
      echo $template->render( array(
          'data' => reset( $data )
        ) );
    }
    catch( Exception $e ) {
      echo '<pre>';
      print_r($e);
      echo '</pre>';
      $app->response()->status( 400 );
      $app->response()->header( 'X-Status-Reason', $e->getMessage() );
    }
  } );


// update
$app->post( '/profile/update', function () use( $app ) {
    $currentUser = Sentry::getUser();
    $request = $app->request();
    $body = $request->post();
    $output = $body;
    $e = '';
    try {
      $user = Sentry::findUserById( $currentUser->id );
      $user->first_name = $output['first_name'];
      $user->last_name = $output['last_name'];
      $profile = R::findOne( 'profile', 'user_id=?', array(
          $currentUser->id
        ) );
      $profile->pedal_type = $output['pedal_type'];
      $profile->type_of_bike = $output['type_of_bike'];
      $profile->health_condition = $output['health_condition'];
      $profile->age = $output['age'];
      $profile->height = $output['height'];
      $profile->skill_level = $output['skill_level'];
      $profile->bike_size = $output['bike_size'];
      $profile->helmet = ( !empty( $output['helmet'] ) == 1 ) ? '1' : '0';
      $profile->gloves = ( !empty( $output['gloves'] ) == 1 ) ? '1' : '0';
      $profile->dietary_requirements = $output['dietary_requirements'];
      $profile->room_type = $output['room_type'];
      $profile->insurance = $output['insurance'];
      $profile->number_nights = $output['number_nights'];
      $profile->flight_number = $output['flight_number'];
      $profile->time_of_flight = $output['time_of_flight'];
      $profile->time_of_transfer = $output['time_of_transfer'];
      $profile->additional_info = $output['additional_info'];
      $id = R::store( $profile );
      // Update the user
      $app->redirect( ADMIN_BASE . '/profile?error=' . $e . '&message=' . $id );
    }
    catch( CartalystSentryUsersUserExistsException $e ) {
      echo 'User with this login already exists.';
    }
    catch( CartalystSentryUsersUserNotFoundException $e ) {
      echo 'User was not found.';
    }
  } );