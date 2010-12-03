<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

/*
 * Printing function for arrays and objects
 */
function pr($obj) {
	print_r($obj,true);
}

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
 * Searches for needle in haystack's(object) key
 */
function array_child_search($needle,$haystack,$key)
{
	for($i=0;!empty($haystack[$i]);$i++)
	{
		if($needle == $haystack[$i]->$key)
			break;
	}
	return $haystack[$i];
}

/*
 * Make associative array from $fields object
 */
function make_assoc_array($fields,$array)
{
	for($i=0;!empty($fields[$i]);$i++)
	{
		if($fields[$i]->type == 'integer')
			$array[$i] = (int) $array[$i];
		$array[$fields[$i]->name] = $array[$i];
	}
	return $array;
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
	return $haystack;
}

/*
 * Strippng single quotes out of strings
 */
function stripquotes($str,$flag=true)
{
	if($flag)
		$str = straft($str,"'");
	$str = strbef($str,"'");
	return $str;
}

/*
 * Sorting an array of arrays according to the key
 */
function array_key_sort($big,$key)
{
	$n = count($big);
	for($j=1;$j<$n;$j++)
	{
		$buf = $big[$j];
		$i = $j-1;
		while($i>=0 && $big[$i][$key]>$buf[$key])
		{
			$big[$i+1] = $big[$i];
			$i--;
		}
		$big[$i+1] = $buf;
	}
	return $big;
}

/*
 * Sorting an array of arrays according to the key
 */
function array_key_rsort($big,$key)
{
	$n = count($big);
	for($j=1;$j<$n;$j++)
	{
		$buf = $big[$j];
		$i = $j-1;
		while($i>=0 && $big[$i][$key]<$buf[$key])
		{
			$big[$i+1] = $big[$i];
			$i--;
		}
		$big[$i+1] = $buf;
	}
	return $big;
}

/*
 * Get index of the given key
 */
function get_key_index($fields,$key)
{
	for($i=0;!empty($fields[$i]);$i++)
	{
		if($fields[$i]->name == $key)
			return $i;	
	}
	return NULL;
}

/*
 * Changing where condition to logic
 */
function changetoLogic($str)
{
	$newStr = array('\$flag = ');
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
						if(strtoupper($buf) == $special[$j])
						{
							$flag = 1;
							$buf = $specialTo[$j];
						}
					
					for($j=0;!empty($nonBreak[$j]);$j++)
						if(strtoupper($buf) == $nonBreak[$j])
						{
							$break = $flag = 1;
							$start = $start + $incBreak[$j];
						}
					
					if($flag == 0)
						$buf = "\$row[".$buf."]";
						
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
	for($i=1;!empty($newStr[$i]);$i++)
		$str.= $newStr[$i];

	$newStr = explode(' = ',$str);

	$str = $newStr[0];
	for($i=1;!empty($newStr[$i]);$i++)
		$str.= " == ".$newStr[$i];
	
	return $str;
}

?>
