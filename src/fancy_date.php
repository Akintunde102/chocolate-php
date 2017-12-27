<?php 
/** 
** This function is used to convert UNIX timestamp to very nice social network-type time format e.g. "5 minutes ago"  "an hour ago"
**  
** Author : Jegede Akintunde
** Email : jegedeakintunde@gmail.com
**  Steemit: @akintunde 
** Web    : https://akin.com.ng/
**
**
*/

function fancy_date($timestamp) 
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

	
	?>