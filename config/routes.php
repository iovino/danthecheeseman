<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \MetzWeb\Instagram\Instagram;
use \Cheesy\Contact;

//
// Homepage
//
$app->get('/', function (Request $request, Response $response)
{
    require_once(SRC_PATH . '/SolveMedia.php');

    $instagram = new Instagram($this->get('settings')['instagram']['client_id']);
    $instagram->setAccessToken($this->get('settings')['instagram']['access_token']);

    return $this->view->render($response, "index.phtml", [
        'media'      => $instagram->getUserMedia('self', 3),
        'solvemedia' => solvemedia_get_html($this->get('settings')['solvemedia']['challenge_key'])
    ]);
});

//
// Send Email
//
$app->post('/send-message', function (Request $request, Response $response)
{
    // sanitize the request
    $data    = $request->getParsedBody();
    $message = [
        'name'    => filter_var($data['name'], FILTER_SANITIZE_STRING),
        'email'   => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
        'phone'   => filter_var($data['phone'], FILTER_SANITIZE_STRING),
        'message' => filter_var($data['message'], FILTER_SANITIZE_STRING),
    ];

    // initiate contact class
    $contact = new Contact();

    // check for errors
    if (empty($message['name']))
    {
        $contact->addError("The name field is blank");
    }

    if (!$contact->isValidEmail($message['email']))
    {
        $contact->addError("The email address you enter is invalid");
    }

    if (empty($message['phone']))
    {
        $contact->addError("The phone field is blank");
    }

    if (empty($message['message']))
    {
        $contact->addError("The message field is blank");
    }

    // check captcha
    require_once(SRC_PATH . '/SolveMedia.php');

    $privkey = $this->get('settings')['solvemedia']['verification_key'];
    $hashkey = $this->get('settings')['solvemedia']['authentication_key'];

    $solvemedia_response = solvemedia_check_answer($privkey, $_SERVER["REMOTE_ADDR"], $data["adcopy_challenge"], $data["adcopy_response"], $hashkey);

    if (!$solvemedia_response->is_valid)
    {
        $contact->addError("Your answer for Solve Media is incorrect");
    }

    // render any errors
    if ($contact->hasErrors())
    {
        $contact->error($contact->getErrorsHtml());
    }

    // build the email to send
    $headers  = "From: {$this->get('settings')['contact']['email']}\r\n";
    $headers .= "Reply-To: {$message['email']}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $body = $this->view->fetch('email.phtml', $message);

    // send it
    mail($this->get('settings')['contact']['email'], $this->get('settings')['contact']['subject'], $body, $headers);

    $contact->success("Your message has been sent!");
});