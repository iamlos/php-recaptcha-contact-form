<?php

$subject = !empty($_POST['subject']) ? stripslashes(trim($_POST['subject'])) : '';
$email = !empty($_POST['email']) ? stripslashes(trim($_POST['email'])) : '';
$message = !empty($_POST['message']) ? stripslashes(trim($_POST['message'])) : '';

if(isset($_POST['submit']) && !empty($_POST['submit'])){
  //recaptcha test
  if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){

    //get recaptcha verify response data
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretkey.'&response='.$_POST['g-recaptcha-response']);
    $responseData = json_decode($verifyResponse);

    //recaptcha success
    if($responseData->success) {

      //Header injection check
      $pattern = '/[\r\n]|Content-Type:|Bcc:|Cc:/i';
      if (preg_match($pattern, $subject) || preg_match($pattern, $email))
        die("Header injection detected");

      //Sanitize subject
      $subject = str_ireplace(array("\r", "\n", "%0a", "%0d"), '', stripslashes($subject));

      // Validate e-mail
      $emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);

      if($emailIsValid){

        $subject_prefix = (isset($subject_prefix) && (strlen(trim($subject_prefix))>0)) ? $subject_prefix.' - ' : '';

        $content = $lang_translate['From'].': '.$email."\r\n";
        $content .= $lang_translate['Message'].": \r\n\r\n";
        $content .= $message;

        $headers = "MIME-Version: 1.0" . PHP_EOL;
        $headers .= "Content-type:text/plain;charset=UTF-8" . PHP_EOL;

        $headers .= "Date: " . date('r', $_SERVER['REQUEST_TIME']) . PHP_EOL;
        $headers .= "Message-ID: <" . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>' . PHP_EOL;
        $headers .= "From: ".$email . PHP_EOL;
        $headers .= "Return-Path: $email_to" . PHP_EOL;
        $headers .= "Reply-To: $email" . PHP_EOL;
        $headers .= "X-Mailer: PHP/". phpversion() . PHP_EOL;
        $headers .= "X-Originating-IP: " . $_SERVER['SERVER_ADDR'] . PHP_EOL;

        //send email
        @mail(
          $email_to,
          $subject_prefix . $subject,
          $content,
          $headers
        );
        
        //Success actions
        $succMsg = $lang_translate['success_msg'];
        $succMsg .= '<br><a href="/">'.$lang_translate['return_msg'].'</a>';
        //Hide contact form after success
        echo '<style>#contact-form {display:none;visibility:hidden;opacity:0;}</style>';
      }
      else
        //Error actions
        $errMsg = $lang_translate['error_msg'];
    }
    else
      //recaptcha error
      $errMsg = $lang_translate['recaptcha_error_msg'];
  }
  else
    //recaptcha solve prompt
    $errMsg = $lang_translate['recaptcha_prompt'];
}
else {
  $errMsg = '';
  $succMsg = '';
}
