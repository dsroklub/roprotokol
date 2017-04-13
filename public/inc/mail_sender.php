<?php
require_once("Mail.php");
function send_email( $subject, $body, $user) {
      $smtp = Mail::factory('sendmail',
          array ());
      $res = false;
      if (isset($user['Email']) && trim($user['Email'])) {
          $email = trim($user['Email']);

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
          $mail_content = $body;
          $mail_status = $smtp->send($email, $mail_headers, $mail_content);
          if (PEAR::isError($mail_status)) {
   	      $res = "Kunne ikke sende mail til $email: " . $mail_status->getMessage();
          }
     } else {
        $res = "Medlemsnummer " . $user['MemberId'] . " har ingen email-adresse!";
     }
     return $res;
  }
?>
