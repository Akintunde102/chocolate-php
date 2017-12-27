<?php
namespace akintunde102\chocolatephp;

class chocolate{
	
	
	/** 
*** This reduces the number of words in a string
**  
**  $wordsreturned represents the number of words to be returned
**
**
*/
	
	
		
   Public function shorten_words($string, $wordsreturned)
	{
           $retval = $string; // Just in case of a problem
           $array = explode(" ", $string);
           if (count($array)<=$wordsreturned)
           {
           $retval = $string;
            }
           else
          {
           array_splice($array, $wordsreturned);
         $retval = implode(" ", $array);
          }
         return $retval;
     }
	
	
	
/* This can check if a word is present in a block of text, I use it to auto-tag articles by checking the articles against some words
**  
**
**
*/



//$String refers to the block of text
//$word refers to the word you are checking for
Public function if_word_present($string,$word) 
	{ 
		$retval = $string;  //  Just in case of a problem
		$array = explode(" ", $string);
		$n = 0;
		foreach($array as $w)
		{   
		    $w = trim(preg_replace('/(\'s)/','',$w));  //To remove words éndings like 's in Jehovah 's
		    $w = trim(preg_replace('/(")/','',$w)); //like " in akin"
		    $w = trim(preg_replace('/(\')/','',$w));   //like ' in akin'
		    $w = trim(preg_replace('/(.)/','',$w));   //like . in akin.
			$array[$n] =trim(preg_replace('/^PL+|PLz/','',$w));
			$n = $n + 1;
		}
		if (in_array($word,$array))
		{
			return true;
		}
		
		//The below searches for words even if it's just part of another word like "mom" in "moment"
		/* else {
			
			if ((strstr( $string, $word ) ? "Yes" : "No" ) == "Yes"){return true;}
			else return false;
			
		} */
	}
	
	

	
	/** 
** This can shorten a block of text to a specific number of paragraphs as specified by you.
**
**
*/
	
	
	//To reduce the number of paragraphs in a string
Public function shorten_by_paragraph($string, $return)
			{
				$retval = $string;  //  Just in case of a problem
				
$array = explode("
", $string); //Don't edit this part of this code,leave it as it is

				if (count($array)<=$return)
				{
					$retval = $string;
				}
				else
				{
					array_splice($array, $return);
					$retval = implode(" ", $array);
				}
				return $retval;
			}

	
	
	
	/** Permalink Generation Function
***
***This converts strings  to equivalent url format. e.g. How to write a blog post  will become how-to-write-a-blog-post.. It allows  only numbers and letters in the url while it removes every other character.More so, you can also change the url format back to a domain name, it removes the '-' and replaces it with space,just change the $mth variable with 'up' and 'down'.
**
**
*/



//$in is the url or text to be formatted
//$mth can either be 'up' and 'down' 
Public Function text_to_url($in,$mth) 
{
	if ($mth == 'up')  
       {
          $in = preg_replace('/[^a-z0-9_]/i','-',$in); //allows only lower case alphabets and numbers
          while (preg_match('/--/',$in) == 1)
          {
           $in = str_replace('--','-',$in);
           }
          $in = preg_replace('/-$/','',$in);
          $in = preg_replace('/^-/','',$in);
       }

	if ($mth == 'down'){ $in = str_replace('-',' ',$in);}
        return $in;
 }
 
	
	
/**
*** OpenSSL Encrypt and Decrypt Function
***
**  These functions help you to encrypt and decrypt a string using your own secret key . It  will require that you have the openssl php extension     **  installed
**  
**
*/

/**  Encrypt Function  */
Public function ossl_encrypt($string) {
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
Public function ossl_decrypt($string) {
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


	
/**
*** MCRYPT Encrypt and Decrypt Function
***
** These functions help you to encrypt and decrypt a string using your own secret key and salt . It  does not require the installation of openssl.
**  
**
**
*/

Public function m_encrypt($string) {
 $salt = 'mysalt';
 $key = 'mykey';
 $key = substr(hash('sha256', $key.$salt), 0, 10);

 $iv_size = mcrypt_get_iv_size (MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
 $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
 $encrypted = base64_encode(mcrypt_encrypt (MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv));
 return trim($encrypted);
}


Public function m_decrypt($string) {
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



/** Fancy Date
** 
** This function is used to convert UNIX timestamp to very nice social network-type time format e.g. "5 minutes ago"  "an hour ago"
**  
**
**
*/

Public function fancy_date($timestamp) 
	{
		$stf = 0;
		$cur_time = time();
		$diff = $cur_time - $timestamp;
		$phrase = array('second','minute','hour','day','week','month','year','decade');
		$length = array(1,60,3600,86400,604800,2630880,31570560,315705600);
		for($i =sizeof($length)-1; ($i >=0)&&(($no =  $diff/$length[$i])<=1); $i--); 
                if($i < 0) $i=0; $_time = $cur_time  -($diff%$length[$i]);
		$no = floor($no); if($no <> 1)$phrase[$i] .='s'; $value=sprintf("%d %s ",$no,$phrase[$i]);
		if(($stf == 1)&&($i >= 1)&&(($cur_tm-$_time) > 0)) $value .= time_ago($_time);
		return $value.' ago ';
	}
	
}

?>