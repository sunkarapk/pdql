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

?>
