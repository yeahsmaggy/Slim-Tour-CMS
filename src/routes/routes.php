<?php

// Routes
// $app->get('/[{name}]', function (Request $request, Response $response, array $args) {
//     // Sample log message
//     $this->logger->info("Slim-Skeleton '/' route");
//     // Render index view
//     return $this->renderer->render($response, 'index.phtml', $args);
// });

$app->get('/', "HomeController:show" );
$app->get('/products', "ProductController:list" );
$app->get('/product/{id}', "ProductController:show" );
//$app->get( '/product/{slug}', "ProductController:show");
//$app->get("/{slug}", "PageController:show"); (shadows other routes move to end)
$app->get('/contact', "PageController:show");
$app->get( '/contact/{message}', "PageController:show");
$app->post( '/contact/send', "PageController:contactsend");
$twig='';


$app->group( '/admin', function () use ( $app , $twig) {
    $app->post( "/login", "AuthController:login");
    $app->post( "/logout", "AuthController:logout");
    $app->get("/login", "AuthController:show");
    $app->get("/register", "AuthController:register");
    $app->post( "/register", "AuthController:register");
    $app->get( "/activation", "AuthController:activation" );

    
    // require_once('admin_tours.php');


    // require_once('admin_pages.php');


    // require_once('admin_profile.php');
} );

