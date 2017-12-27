# chocolate-php

###### This project is to help php programmers have an easy time coding by having ready made functions for common and vital php operations

### Below is a list of codes that are present in this reposittory or folder

#### shorten_words($string, $wordsreturned)

$string refers to the string to be shortened
#wordsreturned refers to the number of words you want the word to be shortened to


#### if_word_present($string,$word)

$string represents the string to be checked
$word refers to the word to be checked

#### shorten_by_paragraph($string, $return)
$string refers to the string to be shortened by paragraphs
$return refers to the number of paragraphs to be returned


#### text_to_url($in,$mth) 
$courier$in is the url or text to be formatted to url format such that the spaces are replaced with hyphen, e.g.  i love you becomes i-love-you, you can append this to a url on your own
$courier$mth can either be 'up' and 'down', 'up' is when its word to url, 'down' is from url to word


#### ossl_encrypt($string) , ossl_decrypt($string)

$courierThese functions help you to encrypt and decrypt a string using your own secret key . It  will require that you have the openssl php extension     **  installed
$string refers to the string to be encrypted or decrypted

#### m_encrypt($string) ,m_decrypt($string)
$courierThese functions help you to encrypt and decrypt a string using your own secret key . It  does not require that you have the openssl php extension     **  installed
$string refers to the string to be encrypted or decrypted



#### fancy_date($timestamp)
$courierThis function is used to convert UNIX timestamp to very nice social network-type time format e.g. "5 minutes ago"  "an hour ago"
$timestamp refers to a unix timestamp


#### Courier Class
Below is an example of codes that can guide on how to use the courier the class

$courier = new courier(); 
$courier->sender = 'Example.Com <no-reply@example.com>'; //sender's email	
$courier->recipient = $to; //recipient's email
$courier->subject = $title; //email title
$courier->message_text = $courier_txt; //the text format of the email 
$courier->message_html = $sentence['full_html'];	//the html format of the email 
if ($courier->send($courier)){echo 'Email sent';}; //then it gets sent


With the above codes , use of the courier is now clear.





### Installation

You can simply install this file by using composer

`composer require akintunde102/chocolate-php dev-master`
