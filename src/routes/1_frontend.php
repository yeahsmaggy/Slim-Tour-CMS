<?php

// Routes
// $app->get('/[{name}]', function (Request $request, Response $response, array $args) {
//     // Sample log message
//     $this->logger->info("Slim-Skeleton '/' route");
//     // Render index view
//     return $this->renderer->render($response, 'index.phtml', $args);
// });

$app->get('/', "controller.home:show" );

$app->get( '/tours', function() use ( $app, $twig ) {
		$tours = R::findAll( 'tours' , 'WHERE published="1"' );
		foreach ( $tours as $tour ) {
			$data['tours'][]=array( 'name'=>$tour->name,
				'type'=>$tour->type,
				'subtype'=>$tour->subtype,
				'alias'=>$tour->alias,
				'background_image'=>$tour->background_image,
			);
		}
		$template = $twig->loadTemplate( 'tours.twig' );
		echo $template->render( $data );
	} );


$app->get( '/tour/:slug(/)', function( $slug ) use ( $app, $twig ) {
		$tour = R::findOne( 'tours', 'alias=?', [$slug] );
		require 'difficulty_explain_modal.php';
		$data = array(
			'strap'=>$tour->strap,
			'name'=>$tour->name,
			'summary'=>$tour->summary,
			'gallery'=>UberGallery::init()->createGallery($tour['gallery_path'] ),
			'total_days'=>$tour->total_days,
			'itinerary_details'=>unserialize( $tour->itinerary ),
			'itinerary'=>unserialize($tour->itinerary),
			'tour_facts'=>unserialize( $tour->tour_facts ),
			'difficulty_rating'=>$tour->difficulty_rating,
			'difficulty_modal'=>$tour->difficulty_modal,
			'code'=>$tour->code,
			'start_date'=>$tour->start_date,
			'end_date'=>$tour->end_date,
			'price'=>$tour->price,
			'book_path'=>$tour->book_path,
			'name'=>$tour->name,
			'jumbotron'=>1,
			'tour_code'=>$tour->tour_code,
			'background_image'=>$tour->background_image,
		);
		$template = $twig->loadTemplate( 'tour.twig' );
		echo $template->render( $data );
	} );


$app->get( '/contact(/)', function( ) use ( $app, $twig ) {
			$data['background_image']= '3.jpg';
			if(!empty($_GET['message'])){
				$data['message']=$_GET['message'];
			}
		$template = $twig->loadTemplate( 'contact.twig' );
		echo $template->render( $data );
	} );


$app->get( '/contact/:message', function( $message ) use ( $app, $twig ) {
		$data['message'] = $message;
		$template = $twig->loadTemplate( 'contact.twig' );
		echo $template->render( $data );
	} );


$app->post( '/contact/send', function() use ( $app, $twig ) {
		$message ='';
		$email_content = SITENAME .' has received an enquiry through the website contact form';
		$email_content .= 'Customer Reference: '. $_POST['cust_ref'] . "\n\r";
		$email_content .= 'Name: '. $_POST['your_name'] . "\n\r";
		$email_content .= 'Phone number'. $_POST['phone_number'] . "\n\r";
		$email_content .= 'Email address: '. $_POST['email_address'] . "\n\r";
		$email_content .= 'Message: '. $_POST['your_message'] . "\n\r";
		$headers = 'From:' . $_POST['email_address'] . "\r\n" .
	    'Reply-To:' . $_POST['email_address'] . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
		if ( !mail( SITEEMAIL, "contact form entry", $email_content, $headers ) ) {
			$message .= 'there was a problem with sending';
		} else if ( !mail( "data.chokheli.33@facebook.com", "contact form entry", $email_content, $headers ) ) {
			$message .= 'there was a problem with sending';
		}
		else {
			$message .= urlencode('thanks for your enquiry');
		}
		$app->redirect( '/contact?message='. $message );
	} );


$app->get( '/:slug(/)', function( $slug ) use ( $app, $twig ) {
	if($slug == 'admin'){
		$app->redirect('/admin/login');
	}
		$page = R::findOne( 'pages', 'alias=?', [$slug] );
		$data = array( 
			'title'=>$page->title,
			'strap'=>$page->strap,
			'body'=>$page->body,
			'background_image'=> rand(0,10) . '.jpg');
		$template = $twig->loadTemplate( 'page.twig' );
		echo $template->render( $data );
	} );