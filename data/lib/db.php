<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

class db {

	public static $access = 0;
	public static $error = null;
	public static $db = null;

	public function __construct($dbname,$dbuser,$dbpass,$error=array())
	{
		self::$error = new error($error[0],$error[1]);

                $fp = fopen(CWD."users","r");
                while(fscanf($fp,"%s\n",$hash))
                {
                	if($hash == md5($dbuser.$dbpass))
                        {
                        	self::$access = 1;
                                break;
                        }

			if(self::$access == 0)
				$this->nouser($dbuser);
		}

		if(self::$access == 1)
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
				self::$db = CWD.$dbname."/";
			}
		}
	}

	protected function createdb($dbname)
	{
		if(!is_dir(CWD.$dbname)
			mkdir(CWD.$dbname);
	}

	protected function nodb($dbname)
	{
		self::$error->set("Database \'".$dbname."\' is not available");
		$this->closeconnection();
	}
	
	protected function nouser($dbuser)
	{
		self::$error->set("User named \'".$dbuser."\' is not found/Wrong password");
		$this->closeconnection();
	}

	protected function closeconnection()
	{
		self::$db = null;
	}

}

?>
