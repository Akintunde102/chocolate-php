<?php
/**
*** MCRYPT Encrypt and Decrypt Function
***
** These functions help you to encrypt and decrypt a string using your own secret key and salt . It  does not require the installation of openssl.
**  
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
**  Steemit: @akintundee 
** Web    : https://akin.com.ng/
**
**
*/

function m_encrypt($string) {
 $salt = 'mysalt';
 $key = 'mykey';
 $key = substr(hash('sha256', $key.$salt), 0, 10);

 $iv_size = mcrypt_get_iv_size (MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
 $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
 $encrypted = base64_encode(mcrypt_encrypt (MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv));
 return trim($encrypted);
}


function m_decrypt($string) {
 $length = strlen($data);
 $salt = 'mysalt';
 $key = 'mykey';
 $key = substr(hash('sha256', $key.$salt), 0, 10);
 $iv_size = mcrypt_get_iv_size (MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
 $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
 $decrypted = mcrypt_decrypt (MCRYPT_RIJNDAEL_256, $key, base64_decode 
($data), MCRYPT_MODE_ECB, $iv);
 return trim($decrypted);
}     

$encrypted_string = encrypt($string);
echo "Encrypted String = $encrypted_stringn";

$decrypted_string = decrypt($encrypted_string);
echo "Decrypted String = $decrypted_Stringn";

if( $plain_string === $decrypted_string ) echo "SUCCESS";
else echo "FAILED";

echo "n";
