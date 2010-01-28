<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

 //define(MODE,"cli" or "web"); This should be in the initial including file in app
 define(CWD,getcwd()."/");

 if(!defined('MODE'))
 	die('You should define a mode before starting the usage of PDQL');

 if(MODE == "cli")
	define(N,"\n");
 else
	define(N,"<br>");

 if(substr(sprintf('%o', fileperms(CWD)), -4) != "0757")
	die("File permissions not set correctly. Check the INSTALL file".N);

 if ( !defined('MEMORY_LIMIT') )
	define('MEMORY_LIMIT', '64M');

 if ( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < abs(intval(MEMORY_LIMIT)) ) )
	@ini_set('memory_limit', MEMORY_LIMIT);

?>
