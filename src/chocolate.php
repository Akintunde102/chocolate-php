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
	 
	 
	 
	 
	/** 
*** This is to initiate a direct download 
*** $file is the name of the file to be downloaded with the full path (e.g. 'image/popup.jpg')
**  $ext is the name of the file extension e.g. 'jpg'
**
*/	 
	 
	 Public Function InitiateDownload($file,$ext){
$fp = fopen($file, "r");
header("Content-Type:application/$ext");
	header("Content-Disposition:attachment;
	filename=$file");
	fpassthru($fp);}
	
	
	/** 
*** This is to remove html tags from a string , most especially when using sql dumps
** $in represents the file input
** Most sql dumps comes with minimal htmltags in the database for formatting reasons
*** This funtion helps remove them so as to add own format
*** This is also useful when using the text to image fucntion to avoid html tags showing up in the pictures
*/	 	
	
	Public Function removehtml($in){
		
	$in = str_replace('<u>','',$in);
	$in = str_replace('</u>','',$in);
	$in = str_replace('<strong>','',$in);
	$in = str_replace('</strong>','',$in);
	$in = str_replace('<em>','',$in);
	$in = str_replace('</em>','',$in);
	$in = str_replace('&nbsp;',' ',$in);
	$in = str_replace('&amp;','&',$in);
	$in = str_replace('#','',$in);
	
	$in = str_replace('<br />','
',$in);
   $in = str_replace('<br>','
   
',$in);

return $in;	
		
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
	



Public function checkPHP($ret='php',$target_file = 'php.html'){
 
    ob_start();
    phpinfo();
    $info = ob_get_contents();
    ob_end_clean();
 
    $fp = fopen($target_file, "w+");
    fwrite($fp, $info);
    fclose($fp);
	
	if (!empty($ret)){return '<a href="'.$target_file.'">'.$ret.'</a>';}
	
	
}


Public function checkDomain($domain){
 
    // fix the domain name:
    $domain = strtolower(trim($domain));
    $domain = preg_replace('/^http:\/\//i', '', $domain);
    $domain = preg_replace('/^www\./i', '', $domain);
    $domain = explode('/', $domain);
    $domain = trim($domain[0]);
 
    // split the TLD from domain name
    $_domain = explode('.', $domain);
    $lst = count($_domain)-1;
    $ext = $_domain[$lst];
 
    // You find resources and lists 
    // like these on wikipedia: 
    //
    // http://de.wikipedia.org/wiki/Whois
    //
    $servers = array(
        "biz" => "whois.neulevel.biz",
        "com" => "whois.internic.net",
        "us" => "whois.nic.us",
        "coop" => "whois.nic.coop",
        "info" => "whois.nic.info",
        "name" => "whois.nic.name",
        "net" => "whois.internic.net",
        "gov" => "whois.nic.gov",
        "edu" => "whois.internic.net",
        "mil" => "rs.internic.net",
        "int" => "whois.iana.org",
        "ac" => "whois.nic.ac",
        "ae" => "whois.uaenic.ae",
        "at" => "whois.ripe.net",
        "au" => "whois.aunic.net",
        "be" => "whois.dns.be",
        "bg" => "whois.ripe.net",
        "br" => "whois.registro.br",
        "bz" => "whois.belizenic.bz",
        "ca" => "whois.cira.ca",
        "cc" => "whois.nic.cc",
        "ch" => "whois.nic.ch",
        "cl" => "whois.nic.cl",
        "cn" => "whois.cnnic.net.cn",
        "cz" => "whois.nic.cz",
        "de" => "whois.nic.de",
        "fr" => "whois.nic.fr",
        "hu" => "whois.nic.hu",
        "ie" => "whois.domainregistry.ie",
        "il" => "whois.isoc.org.il",
        "in" => "whois.ncst.ernet.in",
        "ir" => "whois.nic.ir",
        "mc" => "whois.ripe.net",
        "to" => "whois.tonic.to",
        "tv" => "whois.tv",
        "ru" => "whois.ripn.net",
        "org" => "whois.pir.org",
        "aero" => "whois.information.aero",
        "nl" => "whois.domain-registry.nl"
    );
 
    if (!isset($servers[$ext])){
        die('Error: No matching nic server found!');
    }
 
    $nic_server = $servers[$ext];
 
    $output = '';
 
    // connect to whois server:
    if ($conn = fsockopen ($nic_server, 43)) {
        fputs($conn, $domain."\r\n");
        while(!feof($conn)) {
            $output .= fgets($conn,128);
        }
        fclose($conn);
    }
    else { die('Error: Could not connect to ' . $nic_server . '!'); }
 
 
ob_start();
    echo nl2br($output);
    $info = ob_get_contents();
    ob_end_clean();
 
    $fp = fopen($domain.'.html', "w+");
    fwrite($fp, $info);
    fclose($fp);
	return '<a href="'.$domain.'.html">'.$domain.'</a>';
	
}



Public function dirSize($dir,$unformatted=0) {
$size = 0;

if (is_dir($dir)) {
	
$objects = scandir($dir);


foreach ($objects as $object){
if ($object != "." && $object != ".." && $object != 'index.php')
if (filetype($dir."/".$object) == "dir")
$size += $this->dirsize($dir."/".$object,1);
else
$size += filesize($dir."/".$object);
reset($objects);
}


if ($unformatted == 0){return $this->human_filesize($size);}
else {return $size;}

}
else{return 'directory not found';}

}


Public function human_filesize($bytes, $decimals = 2) {
  $sz = array('Bytes','kb','Mb','Gb','Tb','Pb');
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}


Public function IsIPValid($ip){
 
    if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip)){
        return true;
    }
 
    return false;
}


Public function synthaxPHP($code){
 
    // this matches --> "foobar" <--
    $code = preg_replace(
        '/"(.*?)"/U', 
        '&quot;<span style="color: #007F00">$1</span>&quot;', $code
    );
 
    // hightlight functions and other structures like --> function foobar() <--- 
    $code = preg_replace(
        '/(\s)\b(.*?)((\b|\s)\()/U', 
        '$1<span style="color: #0000ff">$2</span>$3', 
        $code
    );
 
    // Match comments (like /* */): 
    $code = preg_replace(
        '/(\/\/)(.+)\s/', 
        '<span style="color: #660066; background-color: #FFFCB1;"><i>$0</i></span>', 
        $code
    );
 
    $code = preg_replace(
        '/(\/\*.*?\*\/)/s', 
        '<span style="color: #660066; background-color: #FFFCB1;"><i>$0</i></span>', 
        $code
    );
 
    // hightlight braces:
    $code = preg_replace('/(\(|\[|\{|\}|\]|\)|\->)/', '<strong>$1</strong>', $code);
 
    // hightlight variables $foobar
    $code = preg_replace(
        '/(\$[a-zA-Z0-9_]+)/', '<span style="color: #0000B3">$1</span>', $code
    );
 
    /* The \b in the pattern indicates a word boundary, so only the distinct
    ** word "web" is matched, and not a word partial like "webbing" or "cobweb" 
    */
 
    // special words and functions
    $code = preg_replace(
        '/\b(print|echo|new|function)\b/', 
        '<span style="color: #7F007F">$1</span>', $code
    );
 
    return $code;
}
}

?>
