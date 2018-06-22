<?php
declare(strict_types=1);
namespace Src\Transformers\Products;
use Src\Transformers\AbstractTransformer;
class ProductTransformer extends AbstractTransformer
{
    public function item($product)
    {
//        return [
//            'id' => $product['id'],
//            'attributes' => [
//                'name' => $product['name'],
//                'surname' => $product['surname'],
//                'email' => $product['email'],
//            ],
//            'links' => [
//                [
//                    'self' => '/users/' . $product['id'],
//                    'related' => [],
//                ],
//            ],
//
//
            return array(
                'strap'=>$product['strap'],
                'name'=>$product['name'],
                'summary'=>$product['summary'],
//                //'gallery'=>UberGallery::init()->createGallery($product['gallery_path'] ),
                'total_days'=>$product['total_days'],
                'itinerary_details'=> !empty($product['itinerary']) ? unserialize( $product['itinerary']) : null,
                'itinerary_rating'=> !empty($product['itinerary_rating']) ? unserialize( $product['itinerary_rating']) : null,
                'tour_facts'=> !empty($product['tour_facts']) ? unserialize( $product['tour_facts']) : null,
                'difficulty_rating'=>$product['difficulty_rating'],
                'difficulty_modal'=>$product['difficulty_modal'],
                'code'=>$product['code'],
                'start_date'=>$product['start_date'],
                'end_date'=>$product['end_date'],
                'price'=>$product['price'],
                'book_path'=>$product['book_path'],
                'jumbotron'=>1,
                'tour_code'=>$product['tour_code'],
                'background_image'=>$product['background_image'],
            );
    }
}