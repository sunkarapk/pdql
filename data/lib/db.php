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

	public function query($str)
	{
		if(preg_match('/^SELECT [\*,-a-zA-Z0-9_]+ FROM [-a-zA-Z0-9_]+( WHERE (([-a-zA-Z0-9_]+([\s><!=]+| IS NULL| LIKE | NOT LIKE | IS NOT NULL)\'[%-a-zA-Z0-9_]+\')| AND | OR |[\(\)\s]+))+)?( ORDER BY [-a-zA-Z0-9_]+ (ASC|DESC)| LIMIT [0-9]+,[0-9]+|$)+/i'))
		{
			$this->selectfrom();
		}
		else
			self::$error->set("Not a valid mysql query. Query: ".$str);
	}

	protected function selectfrom()
	{
		//SELECT fieldnames FROM tablename WHERE condn
	}

	protected function update()
	{
		//UPDATE tablename SET fieldnames=values WHERE condn
	}

	protected function deletefrom()
	{
		//DELETE FROM tablename WHERE condn
	}

	protected function insertinto()
	{
		//INSERT INTO tablename (fieldnames) VALUES (values)
	}

}

?>
