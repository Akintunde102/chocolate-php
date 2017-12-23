<?php
/**
*** OpenSSL Encrypt and Decrypt Function
***
**  These functions help you to encrypt and decrypt a string using your own secret key . It  will require that you have the openssl php extension     **  installed
**  
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
** Steemit: @akintunde 
** Web    : https://akin.com.ng/
**
**
*/

/**  Encrypt Function  */
function ossl_encrypt($string) {
$output = false;

$encrypt_method = "AES-256-CBC";
$secret_key = 'secret key';
$secret_iv = 'secret iv';

// hash
$key = hash('sha256', $secret_key);

// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
$iv = substr(hash('sha256', $secret_iv), 0, 16);

$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
$output = base64_encode($output);

return $output;
}

/** Decrypt Function */
function ossl_decrypt($string) {
$output = false;

$encrypt_method = "AES-256-CBC";
$secret_key = 'secret key';
$secret_iv = 'secret iv';

$key = hash('sha256', $secret_key);

// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
$iv = substr(hash('sha256', $secret_iv), 0, 16);

$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
return $output;
}

//Example Code
$string = "string";
echo "string = $stringn";

$encrypted_string = encrypt($string);
echo "Encrypted String = $encrypted_stringn";

$decrypted_string = decrypt($encrypted_string);
echo "Decrypted String = $decrypted_Stringn";

if( $plain_string === $decrypted_string ) echo "SUCCESS";
else echo "FAILED";

echo "n";

