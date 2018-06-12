<?php



// DIC configuration
$container = $app->getContainer();
// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};




$loader = new Twig_Loader_Filesystem( array( '../src/views', '../src/views/admin', '../src/views/frontend' ) );
$twig = new Twig_Environment( $loader, array(
    'cache' => './compilation_cache',
    'debug' => true
  ) );
$twig->addExtension( new Twig_Extension_Debug() );




// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(['../src/views', '../src/views/admin', '../src/views/frontend'], [
        'cache' => './compilation_cache',
        'debug' => true
    ]);
    
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $view->addExtension( new Twig_Extension_Debug() );

    return $view;
};

//register controllers
$container['controller.home'] = function($container) {
    // return new HomeController($container['view']);
    return new Src\Controllers\HomeController($container['view']);
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