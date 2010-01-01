<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

class db {

	public function __construct($dbname,$dbuser,$dbpass)
	{
		if(!is_dir($cwd."/".$dbname))
		{
			print "Database doesnot exist\nDo u want to create it?(y/n)\n";
			//fscanf();
			$this->createdb($dbname);
		}
		else
		{
			//getting and checking username
		}
	}

}

?>
