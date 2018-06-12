<?php
namespace Src\Controllers;

use \RedBeanPHP\R as R;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

class HomeController {

    protected $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function show(Request $request, Response $response) {
    //featured tour
        $data['featured'] = R::findAll( 'products', 'WHERE type="bike" AND featured="checked"' );

        //add published=1 back in
        // $db = new mysqli( BLOGDBHOST, BLOGDBUSER, BLOGDBPASS, BLOGDBNAME);
        // if ( $db->connect_errno > 0 ) {
        //  die( 'Unable to connect to database [' . $db->connect_error . ']' );
        // }
        // // then fetch and close the statement
        // $query = "SELECT *  FROM node WHERE status = 1 ORDER BY created DESC LIMIT 5 ";
        // $result = $db->query( $query );
        // if ( $result === false ) {
        //  trigger_error( 'Wrong SQL: ' . $query . ' Error: ' . $db->error, E_USER_ERROR );
        // } else {
        //  $result->data_seek( 0 );
        //  while ( $row = $result->fetch_assoc() ) {
        //      $nodes[]=$row;
        //  }
        // }
        //homepage promoboxes
        $promoboxes = R::findAll( 'products', 'WHERE type="bike" ORDER BY date_modified DESC LIMIT 4' );
        $strapline = '';
        $welcome_text = '';
        $data =  array(
            'promoboxes' => $promoboxes,
            // 'nodes'=>$nodes,
            'strapline'=>$strapline,
            'welcome_text'=>$welcome_text,
            'data'=>$data
        ) ;
        return $this->view->render($response, 'index.twig', $data);
    }
}