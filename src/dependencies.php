<?php
use \RedBeanPHP\R as R;


$loader = new Twig_Loader_Filesystem( array( '../src/views', '../src/views/admin', '../src/views/frontend' ) );
$twig = new Twig_Environment( $loader, array(
    'cache' => './compilation_cache',
    'debug' => true
  ) );
$twig->addExtension( new Twig_Extension_Debug() );




class HomeController {

    protected $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function hello(\Slim\Http\Request $request, \Slim\Http\Response $response) {
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


// DIC configuration
$container = $app->getContainer();
// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(['../src/views', '../src/views/admin', '../src/views/frontend'], [
        'cache' => 'path/to/cache'
    ]);
    
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

//register controllers
$container['controller.home'] = function($container) {
    return new HomeController($container['view']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};


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