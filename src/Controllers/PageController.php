<?php
namespace Src\Controllers;

use \RedBeanPHP\R as R;
use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

class PageController {

    protected $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function show(Request $request, Response $response, $args) {

        if($request->getUri()->getPath() == '/contact'){

            $data['background_image']= '3.jpg';
            if(!empty($_GET['message'])){
                $data['message']=$_GET['message'];
            }
            return $this->view->render($response, 'contact.twig',$data);
        };

        if($args['slug'] == 'admin'){
            return $response->withStatus(302)->withHeader('Location', 'admin/login');
        }
 		$page = R::findOne( 'pages', 'alias=?', [$args['slug']] );
 		$data = array(
 			'title'=>$page->title,
 			'strap'=>$page->strap,
 			'body'=>$page->body,
 			'background_image'=> rand(0,10) . '.jpg');
        return $this->view->render($response, 'page.twig',$data);
    }


    public function contactsend(Request $request, Response $response, $args){
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
        } else if ( !mail( "ajwpr5@gmail.com", "contact form entry", $email_content, $headers ) ) {
            $message .= 'there was a problem with sending';
        }
        else {
            $message .= urlencode('thanks for your enquiry');
        }
        return $response->withStatus(302)->withHeader('Location', '/contact?message='. $message);
    }
}