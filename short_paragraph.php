	<?php
/** 
** This can shorten a block of text to a specific number of paragraphs as specified by you.
**  
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
** Steemit: @akintunde 
** Web    : https://akin.com.ng/
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
			
			?>