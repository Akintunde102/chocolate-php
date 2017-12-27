<?php

/** 
** This can check if a word is present in a block of text, I use it to auto-tag articles by checking the articles against some words
**  
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
** Steemit: @akintunde 
** Web    : https://akin.com.ng/
**
**
*/



//$String refers to the block of text
//$word refers to the word you are checking for
function if_word_present($string,$word) 
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
	
	?>