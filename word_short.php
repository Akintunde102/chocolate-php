	<?php
/** 
*** This reduces the number of words in a string
**  
**  $wordsreturned represents the number of words to be returned
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
** Steemit: @akintunde 
** Web    : https://akin.com.ng/
**
**
*/
	
	
		
    function shorten_words($string, $wordsreturned)
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
	 
	 
	 ?>