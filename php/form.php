<?php

// a simple php file to answer to ajax calls from a filled in form

// default answer
$answer = [
  'code' => 0, // error code
  'message' => "Sorry, something went wrong.", // error message for the user
];

// sleep(3); // test loading icon on form

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $formdata = $_POST; // alternatevly you could get the raw data via file_get_contents("php://input") and then urldecode() or json_decode() - POST handles this for us :)

      $recaptchaIsValid = false;
      $captcha = !empty ($formdata['g-recaptcha-response']) ? $formdata['g-recaptcha-response'] : NULL;
      $captchaResponse = NULL;

      if($captcha) {
        // handling the captcha and checking if it's ok
        $secret = "6LfdPMUUAAAAAHLCoIoByDkL8IbGW3vI3_5aWzR5";
        $captchaResponse = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);
        if ($captchaResponse && $captchaResponse["success"] != false) $recaptchaIsValid = true;
      }

      if ( !empty($formdata['name']) && !empty($formdata['email']) && isset($formdata['message']) && $recaptchaIsValid ) { // check the data
        // process the data from here

        // send mail
        $to = '';
        $subject = '';
        $message = '';
        $headers = '';
        // mail($to, $subject, $message, $headers);

        // TODO: maybe store this in a db as well

        $answer = [
          'code' => 200,
          'message' => "Thank you {$formdata['name']}! We received your message and will get back at you as soon as we can.",
          // 'captchaResponse' => $captchaResponse,
        ];
      } else {
        $answer = [
          'code' => 900, // custom error code
          'message' => "Sorry, some of the data could not be processed. Please check your input and try again.",
        ];
      }
  }

} catch (Exception $e) {
  $errorMessage = $e->getMessage();
  // TODO: log this message with a date and the $_POST variable in a file on the server
  $answer = [
    'code' => 500,
    'message' => "Sorry, there was an error on our server. Please try again later.",
  ];
}

echo json_encode($answer);
