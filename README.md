# CHOCOLATE-PHP

**Name:** CHOCOLATE-PHP<br/>
**Contributors:** Akintunde Jegede <br/>
**TYPE:** LIBRARY<br/>
**Programmed with:** PHP<br/>
**Requires at least:** php 5.* <br/>
**Major Dependency:** PHP<br/>
**License:** APACHE LICENSE  2.0<br/>
**License URI:** https://www.apache.org/licenses/LICENSE-2.0 <br/>
**Composer Installation:** composer require akintunde102/chocolate- <br/>

# Short Summary:
This project is to help php programmers have an easy time coding by having ready made functions for common and vital php operations.

# Description:
This library is meant to become a  got-to-library for vital php functions. It's a continous project that will always welcome more functions to fulfil every "functional" needs a php developer could need. At the moment, it has 2 classes and about 15 vital php functions.

Below is a preview:
![Preview](http://akin.com.ng/a.PNG)


# Further Description:
These script has two major classes:

## Chocolate Class:
This class contains generic functions that performs all kinds of vital operations. It houses majority of the functions which are listed below:

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

#### checkPHP()
This is used to import PHPInfo() which shows all the sever details in a clickable file that is autogenrated as php.html

#### checkDomain($domain)
This connects to several domain registries and returns the deatils of the specified domain in an autogenerated clickable file that contains the full WHOIS details

####  dirSize($dir)
This gives the size of a whole folder/directory as specified as $dir. It returns the human readable format of the file size using the below function.

####  human_filesize($bytes)
This returns the human readable format of the size of a file as division could be messy when using performing direct division operations. This function takes care of that.

####  IsIPValid($ip)
This is use to check if an ip is valid

####  synthaxPHP($code)
This simply highlights php code. The $code represents the codes passed as a string 


Below is an example of codes that can guide on how to initiate and use chocolate as a class

````
$a = new akintunde102\chocolatephp\chocolate;

$string = 'All versions of PHP that support namespaces support three kinds of aliasing or importing: aliasing a class name, aliasing an interface name, and aliasing a namespace name. PHP 5.6+ also allows aliasing or importing function and constant names.';
$wordsreturned = 20;

$a->shorten_words($string, $wordsreturned); //Function example, You can use other functions in the class in the same way

````

## Courier Class
This class is majorly for performing mail operations. Below is an example of codes that can guide on how to initiate and use the courier the class

````
$courier = new akintunde102\chocolatephp\courier;
$courier->sender = 'Example.Com <no-reply@example.com>'; //sender's email	
$courier->recipient = $to; //recipient's email
$courier->subject = $title; //email title
$courier->message_text = $courier_txt; //the text format of the email 
$courier->message_html = $sentence['full_html'];	//the html format of the email 
if ($courier->send($courier)){echo 'Email sent';}; //then it gets sent
````



### Installation

You can simply install this file by using composer. 

If you don't know about composer, read about it (here)[https://getcomposer.org/doc/01-basic-usage.md]

`composer require akintunde102/chocolate-php dev-master`


### Example
````php


<?php
require_once 'vendor/autoload.php';

//Initiating and Using the Chocolate Class
$a = new akintunde102\chocolatephp\chocolate;

$string = 'All versions of PHP that support namespaces support three kinds of aliasing or importing: aliasing a class name, aliasing an interface name, and aliasing a namespace name. PHP 5.6+ also allows aliasing or importing function and constant names.';

$wordsreturned = 20;

$sh = $a->shorten_words($string, $wordsreturned);

echo $sh; 

//Initiating and Using the Courier Class
$courier = new akintunde102\chocolatephp\courier;
$courier->sender = 'Example.Com <no-reply@example.com>'; //sender's email	
$courier->recipient = $to; //recipient's email
$courier->subject = $title; //email title
$courier->message_text = $courier_txt; //the text format of the email 
$courier->message_html = $sentence['full_html'];	//the html format of the email 
if ($courier->send($courier)){echo 'Email sent';}; //then it gets sent


?>
````


## Testing
To test the libary before use, follow the steps below:
1) Go to vendor\akintunde102\chocolate-php\example and copy the index.php
2) Paste the index.php to main directory
3) Then you can test the codes from your browser or command line as you may like

                                       OR 
1) Create a file in the main directory and name it (for instance,'test.html')
2) Then copy the codes below into the file
````php


<?php
require_once 'vendor/autoload.php';

$a = new akintunde102\chocolatephp\chocolate;

$string = 'All versions of PHP that support namespaces support three kinds of aliasing or importing: aliasing a class name, aliasing an interface name, and aliasing a namespace name. PHP 5.6+ also allows aliasing or importing function and constant names.';
$wordsreturned = 20;

$a->shorten_words($string, $wordsreturned);

echo $a->checkPHP('errorfile');

echo '<br/><br/>';


echo $a->checkDomain('timiweb.com');

echo $a->dirSize('vendor/');

if ($a->IsIPValid('10.199.212.2')){echo 'IP is valid';}
else {echo 'IP is invalid';}



//Initiating and Using the Courier Class
$courier = new akintunde102\chocolatephp\courier;
$courier->sender = 'Example.Com <no-reply@example.com>'; //sender's email	
$courier->recipient = $to; //recipient's email
$courier->subject = $title; //email title
$courier->message_text = $courier_txt; //the text format of the email 
$courier->message_html = $sentence['full_html'];	//the html format of the email 
if ($courier->send($courier)){echo 'Email sent';}; //then it gets sent


?>
````

## Present Version
1.1

## Contact Me
**Discord**: @akintunde <br/>
**Email:** jegedeakintunde[at]gmail.com<br/>
**utopian.io:** @akintunde <br/>
**github:** @akintunde102<br/>




 

