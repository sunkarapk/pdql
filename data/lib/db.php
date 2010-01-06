<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

class db {

	public static $access = 0;

	public function __construct($dbname,$dbuser,$dbpass)
	{
		if(!is_dir(CWD.$dbname))
		{
			if(!defined('ADMIN') || ADMIN == 0)
				$this->nodb($dbname);
			else if(ADMIN == 1)
				$this->createdb($dbname);
		}
		else
		{
			//getting and checking username
			$fp = fopen(CWD."users","r");
			while(fscanf($fp,"%s\t%s\n",$tmpuser,$tmppass))
			{
				if($dbuser == $tmpuser && $dbpass == $tmppass)
				{
					self::$access = 1;
					break;
				}
			}
		}
	}

	public function createdb()
	{
		if(!is_dir(CWD.$dbname)
			mkdir(CWD.$dbname);
	}

}

?>
