<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

class error {

	public static $errfunct = NULL;
	public static $errclass = NULL;

	public function __construct($errfunct=NULL, $errclass=NULL)
	{
		self::$errclass = $errclass;
		self::$errfunct = $errfunct;
	}

	public function set($msg)
	{
		if(empty(self::$errclass) && empty(self::$errfunct))
			die($msg);
		else if(empty(self::$errclass) && !empty(self::$errfunct))
			eval(self::$errfunct."(\$msg);");
		else
			eval(self::$errclass."::".$errfunct."(\$msg);");
	}

}

?>

