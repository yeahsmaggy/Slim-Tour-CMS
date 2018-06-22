<?php
namespace Src\Controllers;

use \RedBeanPHP\R as R;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

class AuthController {

    protected $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function login(Request $request, Response $response, $args){

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
                return $response->withStatus(302)->withHeader('Location', '/products');
            }
            catch( CartalystSentryUsersWrongPasswordException $e ) {
                $failMessage = $e->getMessage();
                return $response->withStatus(302)->withHeader('Location', '/admin/login' . '?message=' . urlencode($failMessage));

            }
        }
    }

    public function logout(Request $request, Response $response, $args){
        CartalystSentryFacadesNativeSentry::logout();
        return $response->withStatus(302)->withHeader('Location', '/admin/login');
    }

    public function show(Request $request, Response $response, $args){
        if ( !Sentry::check() ) {
            // User is not logged in, or is not activated
            return $this->view->render($response, 'page.twig',[]);
        }
        else {
            // User is logged in
            return $response->withStatus(302)->withHeader('Location', '/admin/profile');
        }
    }


    public function register(Request $request, Response, $response, $args){


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


        if ( !Sentry::check() ) {
            // User is not logged in, or is not activated
            return $this->view->render($response, 'register.php',[]);
        }
        else {
            // User is logged in
            return $response->withStatus(302)->withHeader('Location', '/admin/profile');
        }
    }

    public function activation(Request $request, Response $response, $args){
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
    }



}