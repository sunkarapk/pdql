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
 * The php stristr function replacement
 */
function strbef($haystack, $needle)
{
	$pos = stripos($haystack, $needle);
	if(is_int($pos))
		return substr($haystack, 0, $pos);
	return NULL;
}

/*
 * Changing where condition to logic
 */
function changetoLogic($str)
{
	$newStr = array();
	$allow = array('(',' ',')','=','!','>','<','\'');
	$special = array('AND','OR','IS NULL','IS NOT NULL','LIKE','NOT LIKE');
	$specialTo = array('&&','||','== NULL','!= NULL','==','!=');
	$nonBreak = array('IS','NOT','IS NOT');
	$incBreak = array(1,2,4);
	
	for($i=0; $i < strlen($str); $i++)
	{
		$flag = 0;
		$c = $str[$i];
		if(in_array($c,$allow))
			array_push($newStr,$c);
		else
		{
			$sign = array_pop($newStr);
			if($sign == '\'')
				$flag = 1;
			array_push($newStr,$sign);
			
			$start = $i;
			while($i<strlen($str))
			{
				$break = 0;
				array_push($newStr,$c);
				$i++;
				$c = $str[$i];
				if(in_array($c,$allow))
				{
					$buf = '';
					for($j=$i-1;$j>=$start;$j--)
						$buf = array_pop($newStr).$buf;

					for($j=0;!empty($special[$j]);$j++)
						if($buf == $special[$j])
						{
							$flag = 1;
							$buf = $specialTo[$j];
						}
					
					for($j=0;!empty($nonBreak[$j]);$j++)
						if($buf == $nonBreak[$j])
						{
							$break = $flag = 1;
							$start = $start + $incBreak[$j];
						}
					
					if($flag == 0)
						$buf = "\\\$row['".$buf."']";
						
					array_push($newStr,$buf);
					array_push($newStr,$c);
					
					if($break == 0)
						break;
					else
						$c = array_pop($newStr);
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
