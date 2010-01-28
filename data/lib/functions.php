<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

/*
 * Recursive function for converting an array into object
 */
function atoo($arr) 
{
	if(!is_array($arr)) {
		return $arr;
	}

	$obj = new stdClass();
	if(is_array($arr) && count($arr) > 0)
	{
		foreach($arr as $name=>$val)
		{
			$name = strtolower(trim($name));
			if(!empty($name)) {
				$obj->{$name} = atoo($val);
			}
		}
		return $obj;
	}
	else
		return false;
}

/*
 * Get string after needle (Case insensitive)
 */
function straft($haystack, $needle) 
{
    $pos = stripos($haystack, $needle);
    if (is_int($pos)) 
        return substr($haystack, $pos + strlen($needle));
    return NULL;
}

/*
 * Changing where condition to logic
 */
function changetoLogic($str)
{
	$newStr = array();
	$allow = array('(',' ',')','=','!','>','<','\'');
	
	for($i=0; $i < strlen($str); $i++)
	{
		$c = $str[$i];
		if(in_array($c,$allow))
			array_push($newStr,$c);
		else
		{
			$start = $i;
			while($i<strlen($str))
			{
				array_push($newStr,$c);
				$i++;
				$c = $str[$i];
				if(in_array($c,$allow))
				{
					$buf = '';
					for($j=$i-1;$j>=$start;$j--)
						$buf = array_pop($newStr).$buf;
					$buf = "\$row['".$buf."']";
					array_push($newStr,$buf);
					array_push($newStr,$c);
					break;
				}
			}
		}
	}
	
	$str = "";
	for($i=0;!empty($newStr[$i]);$i++)
		$str.= $newStr[$i];
	
	$newStr = explode(' = ',$str);
	
	$str = $newStr[0];
	for($i=1;!empty($newStr[$i]);$i++)
		$str.= " == ".$newStr[$i];
	
	return $str;
}

?>
