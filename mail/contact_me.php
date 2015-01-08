<?php

use \google\appengine\api\mail\Message;

switch ($_SERVER['HTTP_ORIGIN']) {
    case 'http://voodoocactus.com': case 'http://www.voodoocactus.com':
    header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    // Check for empty fields
    if(empty($_POST['name'])  		||
        empty($_POST['email']) 		||
        empty($_POST['phone']) 		||
        empty($_POST['message'])	||
        !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
    {
        echo "No arguments Provided!";
        syslog(LOG_WARNING,'No arguments Provided!');
        return false;
    }

    $name = $_POST['name'];
    $email_address = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // Create the email and send the message
    $to = 'contact@voodoocactus.com'; // Add your email address inbetween the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
    $email_subject = "Website Contact Form:  $name";
    $email_body = "You have received a new message from your website contact form.\n\n"."Here are the details:\n\nName: $name\n\nEmail: $email_address\n\nPhone: $phone\n\nMessage:\n$message";
    $headers = "From: noreply@voodoocactus.com\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
    $headers .= "Reply-To: $email_address";	

    try
    {
        $message = new Message();
        /// sender email must be of the form [any string]@[Application ID].appspotmail.com
        $message->setSender('noreply@voodoo-cactus.appspotmail.com');
        $message->addTo('contact@voodoocactus.com');
        $message->setSubject($email_subject);
        $message->setTextBody($email_body);
        $message->send();
    } catch (InvalidArgumentException $e) {
        syslog(LOG_WARNING,'Error sending email:' . $e->getMessage());
        return false;
    }

    // PHP mail function not supported by Google AppEngine
    // mail($to,$email_subject,$email_body,$headers);
    return true;			
}
?>
