<?php
    $app->group( '/admin', function () use ( $app , $twig) {
        $app->post( "/login", function () use ($app) {
            if ( isset( $_POST['submit'] ) ) {
              try {
                // validate input
                $email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );
                $password = strip_tags( trim( $_POST['password'] ) );
                // set login credentials
                $credentials = array(
                  'email' => $email,
                  'password' => $password
                );
                // authenticate
                // if authentication fails, capture failure message
                Sentry::authenticate( $credentials, false );
                $app->redirect( ADMIN_BASE . '/tours' );
              }
              catch( CartalystSentryUsersWrongPasswordException $e ) {
                $failMessage = $e->getMessage();
                $app->redirect( ADMIN_BASE . '/login' . '?message=' . urlencode($failMessage));
                  }
            }
          } );


        $app->get( "/login(/)", function () use( $app , $twig) {             
            if ( !Sentry::check() ) {
              // User is not logged in, or is not activated
              $template = $twig->loadTemplate( 'login.twig' );
              echo $template->render( array(
                  'data' => 'none'
                ) );
            }
            else {
              // User is logged in
              $app->redirect( ADMIN_BASE . '/profile' );
            }
          } );


        $app->get( "/logout(/)", function () use( $app ) {
            CartalystSentryFacadesNativeSentry::logout();
            $app->redirect( ADMIN_BASE . '/login' );
          } );


        $app->get( "/register(/)", function () use( $app ) {
            if ( !Sentry::check() ) {
              // User is not logged in, or is not activated
              $template = $twig->loadTemplate( 'register.php' );
              echo $template->render( array(
                  'data' => 'none'
                ) );
            }
            else {
              // User is logged in
              $app->redirect( ADMIN_BASE . '/profile' );
            }
          } );


        $app->post( "/register", function () use( $app ) {
            if ( isset( $_POST['submit'] ) ) {
              try {
                $email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );
                $fname = strip_tags( $_POST['first_name'] );
                $lname = strip_tags( $_POST['last_name'] );
                $password = strip_tags( $_POST['password'] );
                $user = CartalystSentryFacadesNativeSentry::createUser( array(
                    'email' => $email,
                    'password' => $password,
                    'first_name' => $fname,
                    'last_name' => $lname,
                    'activated' => false
                  ) );
                $db = new mysqli( DBHOST, DBUSER, DBPASS, DBNAME );
                if ( $db->connect_errno > 0 ) {
                  die( 'Unable to connect to database [' . $db->connect_error . ']' );
                }
                $query = "INSERT INTO `profile` (`user_id`) VALUES ( " . $user->id . ")";
                if ( !$db->query( $query ) ) {
                }
                $code = $user->getActivationCode();
                $subject = 'Your activation code';
                $message = 'Click or copy this link into your browser: http://www.georidersmtb.com/admin/activation?code=' . $code . '&email=' . $email;
                $headers = 'From: georidersmtb@gmail.com';
                mail( $email, $subject, $message, $headers );
                if ( !mail( $email, $subject, $message, $headers ) ) {
                  throw new Exception( '<br />Email could not be sent.' );
                } 
                echo '<br />User successfully registered and activation code sent.';
                echo '<br />If email doesn\'t appear to arrive, check your spam folder.';
                try {
                  $user = Sentry::findUserById( $user->id );
                  $userGroup = Sentry::findGroupById( 1 );
                  if ( $user->addGroup( $userGroup ) ) {
                  }
                  else {
                  }
                  echo '<br />please check your email for the activation code';
                  echo '<br /><a href="/admin/login">click here to return to the login page</a>';
                }
                catch( CartalystSentryUsersUserNotFoundException $e ) {
                  echo 'User was not found.';
                }
                catch( CartalystSentryGroupsGroupNotFoundException $e ) {
                  echo 'Group was not found.';
                }
                exit;
              }
              catch( Exception $e ) {
                echo $e->getMessage();
                exit;
              }
            } 
          } );


        $app->get( "/activation(/)", function () use( $app ) {
            if ( isset( $_GET['code'] ) && $_GET['email'] ) {
              // find user by email address
              // activate user with activation code
              try {
                $code = strip_tags( $_GET['code'] );
                $email = filter_var( $_GET['email'], FILTER_SANITIZE_EMAIL );
                $user = CartalystSentryFacadesNativeSentry::findUserByCredentials( array(
                    'email' => $email
                  ) );
                if ( $user->attemptActivation( $code ) ) {
                  echo '<br />User activated.';
                  echo '<br /><a href="/admin/login">Click here to login</a>';
                }
                else {
                  throw new Exception( 'User could not be activated.' );
                }
              }
              catch( Exception $e ) {
                echo $e->getMessage();
              }
            }
          } );


          require_once('admin_tours.php');


          require_once('admin_pages.php');


          require_once('admin_profile.php');
      } );