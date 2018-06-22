<?php
use \Flexsounds\Component\SymfonyContainerSlimBridge\ContainerBuilder;
use \Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

$container = new ContainerBuilder();

// $container['logger'] = function ($c) {
//     $settings = $c->get('settings')['logger'];
//     $logger = new Monolog\Logger($settings['name']);
//     $logger->pushProcessor(new Monolog\Processor\UidProcessor());
//     $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
//     return $logger;
// };

$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../config/'));
$loader->load('services.yml');
$app = new \Slim\App( $container );



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