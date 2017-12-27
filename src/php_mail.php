<?php

/** Mail Sending Class
*** This class makes it really  easy to send emails from your server, most especially when you want to have control of all the email fields. To use *** this class,please follow my directions. You need two supporting classes which are Courier.class and Email.class which are listed below.
***
** These functions help you to encrypt and decrypt a string using your own secret key and salt . It  does not require the installation of openssl.
**  
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
** Steemit: @akintunde 
** Web    : https://akin.com.ng/
**
**
*/





// Courier.class.php

/**
 * This class emails converts and emails data from the Email class
 * It takes an Email and uses the mail() function to send it out
 *_FAIL
 */
class Courier {
 const SEND_OK = 0;
 const SENT_FAIL = 1;
 
 /**
 * Make text rfj2047 compliant 
 * We can convert HTML
 * character entities into ISO-8859-1, 
 * then converting the charset to 
 * Base64 for rfc2047 email subject compatibility.
 */
 public function rfc2047_sanitize($input) {
 $output = mb_encode_mimeheader(
 html_entity_decode(
 $input,
 ENT_QUOTES,
 'ISO-8859-1'),
 'ISO-8859-1','B',"n");
 return $output;
 }
 
 /**
 * Set the Email object to draw the information from
 *
 * @parameter $email the email to send
 */
 public function send( $Email=null ) {
 // let's create the headers to show where the email 
 // originated from.
 $headers[] = 'From: '.$Email->sender;
 $headers[] = 'Reply-To: '.$Email->sender;
 
 
 
 // Subjects are tricky. Even some 
 // sophisticated email clients don't
 // understand unicode subject lines. 
 $subject = $this->rfc2047_sanitize($Email->subject);
 
 $message = "";
 
 // if the email is HTML, then let's tell the MTA about the mime-type and all that
 if ($Email->message_html) {
 // set up a mime boundary so that we can encode
 // the email inside it, hiding it from clients
 // that can only read plain text emails
 $mime_boundary = '<<<--==+X['.md5(time()).']';
 $headers[] = 'MIME-Version: 1.0';
 $headers[] = 'Content-Type: multipart/mixed;';
 $headers[] = ' boundary="'.$mime_boundary.'"';
 $message = $Email->message_html;
 
 $message .= "rn";
 $message .= "--".$mime_boundary."rn";
 }
 
 // since this is a mime/multipart message, we need to re-iterate
 // the message contents in order for mime-aware clients to read it
 if ($Email->message_html) {
 $message .= "Content-Type: text/html; charset=\"iso-8859-1\"rn";
 $message .= "Content-Transfer-Encoding: 7bitrn";
 $message .= "rn";
 $message .= $Email->message_html;
 } else { 
 $message .= 'Co\ntent-type: text/plain; charset=iso-8859-1';
 $message .= "Content-Transfer-Encoding: 7bitrn";
 $message .= "rn";
 $message .= $email->message_text;
 }
 $message .= "rn";
 $message .= "--".$mime_boundary."rn";
 $message .= $Email->message_text;
 
 
 
 // try to send the email. 
 $result = @mail( $Email->recipient, 
 $subject, 
 $message, 
 implode("rn",$headers) 
 );
 
 // if it fails, let's throw up an error
 if ( !$result ) {
 return false;
 } // fi result
 
 return true;

 } // send
}




// classes/Email.class.php
/**
 * Email class conceptualizes an email. 
 * It contains the information that the Courier
 * needs to send an email message to a recipient
 */
class Email {
 var $recipient,
 $sender,
 $subject,
 $message_text,
 $message_html;
}




//Process the Mail
//require both classes
require_once('Email.class.php'); 
require_once('Courier.class.php');	

$courier = new courier(); 
$this->courier = $courier;
$Email = new Email();
$Email->sender = 'Example.Com <no-reply@example.com>'; //sender's email	
$Email->recipient = $to; //recipient's email
$Email->subject = $title; //email title
$Email->message_text = $email_txt; //the text format of the email 
$Email->message_html = $sentence['full_html'];	//the html format of the email 
if ($courier->send($Email)){echo 'Email sent';}; //then it gets sent




?>