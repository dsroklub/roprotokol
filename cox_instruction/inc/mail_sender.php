<?php
  require_once("Mail.php");
  function send_email( $subject, $template, $user ) {
      $smtp = Mail::factory('sendmail',
          array ());

      $res = false;
      if (isset($user['email']) && trim($user['email'])) {

          $email = trim($user['email']);

          $mail_headers = array(
                              'From'                      => "DSR Instruktionsrochef <instruktion@danskestudentersroklub.dk>",
          					  'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
                              'Content-Transfer-Encoding' => "8bit",
						      'Content-Type'              => 'text/plain; charset="utf8"',
						      'Date'                      => date('r'),
			      			  'Message-ID'                => "<".sha1(microtime(true))."@instruktion.danskestudentersroklub.dk>",
                              'MIME-Version'              => "1.0",
                              'X-Mailer'                  => "PHP-Custom",
                              'Subject'                   => "$subject"
                               );

          $mail_headers['To'] = $email;

          $mail_content = preg_replace_callback(
                              '/%([a-zA-Z0-9_-]+)%/',
                              function ($m) use ($user) {
                                 return $user[$m[1]];
                              },
                              $template
                         );

          $mail_status = $smtp->send($email, $mail_headers, $mail_content);
          if (PEAR::isError($mail_status)) {
   	      $res = "Kunne ikke sende mail til $email: " . $mail_status->getMessage();
          }
     } else {
        $res = "Medlemsnummer " . $user['ID'] . " har ingen email-adresse!";
     }
     return $res;
  }

?>

