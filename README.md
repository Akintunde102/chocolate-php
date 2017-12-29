# chocolate-php

###### This project is to help php programmers have an easy time coding by having ready made functions for common and vital php operations

### Below is a list of codes that are present in this repository or folder

#### shorten_words($string, $wordsreturned)

$string refers to the string to be shortened <br/>
#wordsreturned refers to the number of words you want the word to be shortened to


#### if_word_present($string,$word)

$string represents the string to be checked<br/>
$word refers to the word to be checked

#### shorten_by_paragraph($string, $return)
$string refers to the string to be shortened by paragraphs <br/>
$return refers to the number of paragraphs to be returned


#### text_to_url($in,$mth) 
$in is the url or text to be formatted to url format such that the spaces are replaced with hyphen, e.g.  i love you becomes i-love-you, you can append this to a url on your own <br/>
$mth can either be 'up' and 'down', 'up' is when its word to url, 'down' is from url to word


#### ossl_encrypt($string) , ossl_decrypt($string)
These functions help you to encrypt and decrypt a string using your own secret key . It  will require that you have the openssl php extension     **  installed 
$string refers to the string to be encrypted or decrypted

#### m_encrypt($string) ,m_decrypt($string)
These functions help you to encrypt and decrypt a string using your own secret key . It  does not require that you have the openssl php extension     **  installed
$string refers to the string to be encrypted or decrypted

#### InitiateDownload($file,$ext)
This is to initiate a direct download 
$file is the name of the file to be downloaded with the full path (e.g. 'image/popup.jpg')
$ext is the name of the file extension e.g. 'jpg'

#### removehtml($in)
This is to remove html tags from a string , most especially when using sql dumps
$in represents the file input
Most sql dumps comes with minimal htmltags in the database for formatting reasons
This funtion helps remove them so as to add own format
This is also useful when using the text to image fucntion to avoid html tags showing up in the pictures



#### Courier Class
Below is an example of codes that can guide on how to use the courier the class

````
$courier = new courier(); 
$courier->sender = 'Example.Com <no-reply@example.com>'; //sender's email	
$courier->recipient = $to; //recipient's email
$courier->subject = $title; //email title
$courier->message_text = $courier_txt; //the text format of the email 
$courier->message_html = $sentence['full_html'];	//the html format of the email 
if ($courier->send($courier)){echo 'Email sent';}; //then it gets sent
````


With the above codes , use of the courier class is now clear.





### Installation

You can simply install this file by using composer

`composer require akintunde102/chocolate-php dev-master`


### Example
````php


<?php
require_once 'vendor/autoload.php';

$a = new akintunde102\chocolatephp\chocolate;

$string = 'All versions of PHP that support namespaces support three kinds of aliasing or importing: aliasing a class name, aliasing an interface name, and aliasing a namespace name. PHP 5.6+ also allows aliasing or importing function and constant names.';

$wordsreturned = 20;

$sh = $a->shorten_words($string, $wordsreturned);

echo $sh;
?>
````

The above example guides you on how to use the library after installation. You can test run this codes in the example folder which has just been recently added
