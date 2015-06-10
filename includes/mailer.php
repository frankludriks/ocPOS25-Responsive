<?php  
// includes/mailer.php

 
class Mailer
{
   /**
    * sendWelcome - Sends a welcome message to the newly
    * registered user, also supplying the username and
    * password.
    */
   function sendWelcome($user, $email, $pass){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "Welcome!";
      $body = $user.",\r\n"
             ."Welcome to OllaCart Point of Sale!"
             ."Login with the following information:\r\n"
             ."Username: ".$user."\r\n"
             ."Password: ".$pass."\r\n"
             ."If you ever lose or forget your password, a new "
             ."password will be generated for you and sent to this "
             ."email address, if you would like to change your "
             ."email address you can do so by going to the "
             ."Manage Account page after signing in.\r\n";

      return mail($email,$subject,$body,$from);
   }
   
   /**
    * sendNewPass - Sends the newly generated password
    * to the user's email address that was specified at
    * sign-up.
    */
   function sendNewPass($user, $email, $pass){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "Your new password";
      $body = $user.",\r\n"
             ."We've generated a new password for you at your "
             ."request, you can use this new password with your "
             ."username to log in.\r\n"
             ."Username: ".$user."\r\n"
             ."New Password: ".$pass."\r\n"
             ."It is recommended that you change your password "
             ."to something that is easier to remember, which "
             ."can be done by going to the My Account page "
             ."after signing in.\r\n";
             
      return mail($email,$subject,$body,$from);
   }
};

/* Initialize mailer object */
$mailer = new Mailer;
 
?>
