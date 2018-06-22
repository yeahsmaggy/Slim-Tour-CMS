<?php
namespace Src\Controllers;

use \RedBeanPHP\R as R;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;
use \Src\Transformers\Products\ProductTransformer;

class ProductController {

    protected $view;


    /**
     * @var UserTransformer
     */
    private $productTransformer;


    public function __construct($view, ProductTransformer $productTransformer) {
        $this->view = $view;
        $this->productTransformer = $productTransformer;
    }

    public function show(Request $request, Response $response, $args) {
        $product = R::findOne('products', ' id = ? ', [$args['id']]);
 		//require 'difficulty_explain_modal.php';

//        $data = $this->productTransformer->collection($product->getProperties());


        return $this->view->render($response, 'product.twig', $product->getProperties());
    }

    public function list(Request $request, Response $response){
        $products = R::findAll( 'products' );
        foreach ( $products as $product ) {
            $data['products'][]=array( 'name'=>$product->name,
                'type'=>$product->type,
                'subtype'=>$product->subtype,
                'alias'=>$product->alias,
                'background_image'=>$product->background_image,
            );
        }        
        

        return $this->view->render($response, 'products.twig', $data);
    }
}