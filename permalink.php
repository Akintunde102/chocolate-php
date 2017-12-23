<?php
/** Permalink Generation Function
***
***This converts strings  to equivalent url format. e.g. How to write a blog post  will become how-to-write-a-blog-post.. It allows  only numbers and letters in the url while it removes every other character.More so, you can also change the url format back to a domain name, it removes the '-' and replaces it with space,just change the $mth variable with 'up' and 'down'.
**  
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
** Steemit: @akintunde 
** Web    : https://akin.com.ng/
**
**
*/



//$in is the url or text to be formatted
//$mth can either be 'up' and 'down' 
Function text_to_url($in,$mth) 
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
 
 ?>