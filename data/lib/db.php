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
		if(preg_match('/^SELECT [\*,-a-zA-Z0-9_]+ FROM [-a-zA-Z0-9_]+( WHERE (([-a-zA-Z0-9_]+([\s><!=]+| IS NULL| LIKE | NOT LIKE | IS NOT NULL)\'[%-a-zA-Z0-9_/\(\)\s:;,@\.]+\')| AND | OR |(\s)?(\(|\))?(\s)?)+)?( ORDER BY [-a-zA-Z0-9_]+ (ASC|DESC)| LIMIT [0-9]+,[0-9]+|$)+/i',$str,$match) != 0 && $str == $match)
		{
			$str = substr($str,7);
			$fields = NULL;
			if(substr($str,0,2) != '* ')
				$fields = explode(",",stristr($str," FROM ",true));
			$str = straft($str," FROM ");
			$table = stristr($str," ",true);
			$str = straft($str," ");
			$limit = explode(",",straft($str," LIMIT "));
			$str = stristr($str," LIMIT ",true);
			$order = explode(" ",straft($str," ORDER BY "));
			$str = stristr ($str," ORDER BY ",true);
			$this->selectfrom($table,$fields,$limit,$order,$str);
		}
		else if(preg_match('/^DELETE FROM [-a-zA-Z0-9_]+( WHERE (([-a-zA-Z0-9_]+([\s><!=]+| IS NULL| LIKE | NOT LIKE | IS NOT NULL)\'[%-a-zA-Z0-9_/\(\)\s:;,@\.]+\')| AND | OR |(\s)?(\(|\))?(\s)?)+)?( ORDER BY [-a-zA-Z0-9_]+ (ASC|DESC)| LIMIT [0-9]+,[0-9]+|$)/i',$str,$match) != 0 && $str == $match)
		{
			$str = substr($str,12);
			$table = stristr($str," ",true);
			$str = straft($str," ");
			$limit = explode(",",straft($str," LIMIT "));
			$str = stristr($str," LIMIT ",true);
			$order = explode(" ",straft($str," ORDER BY "));
			$str = stristr ($str," ORDER BY ",true);
			$this->deletefrom($table,$limit,$order,$str);
		}
		else if(preg_match('/^INSERT INTO [-a-zA-Z0-9_]+ (\([,-a-ZA-Z0-9_]+\) )?VALUES \(\'[%-a-zA-Z0-9_/\(\)\s:;,@\.]+\'(,\'[%-a-zA-Z0-9_/\(\)\s:;,@\.]+\')*\)/i',$str,$match) != 0 && $str == $match)
		{
			$fields = null;
			$str = substr($str,12);
			$table = stristr($str," ",true);
			$str = straft($str," ");
			$buf = explode("VALUES ",$str);
			if(!empty($buf[0]))
			{
				$str = substr($buf[0],1);
				$str = stristr($str,") ",true);
				$fields = explode(",",$str);
			}
			$values = explode(",",$buf[1]);
			$this->insertinto($table,$fields,$values);
		}
		else if(preg_match('/^UPDATE [-a-zA-Z0-9_]+ SET [-a-zA-Z0-9_]+=(NULL|\'[%-a-zA-Z0-9_/\(\)\s:;,@\.]+\'|DEFAULT)(,[-a-zA-Z0-9_]+=(NULL|\'[%-a-zA-Z0-9_/\(\)\s:;,@\.]+\'|DEFAULT))*( WHERE (([-a-zA-Z0-9_]+([\s><!=]+| IS NULL| LIKE | NOT LIKE | IS NOT NULL)\'[%-a-zA-Z0-9_/\(\)\s:;,@\.]+\')| AND | OR |(\s)?(\(|\))?(\s)?)+)?( ORDER BY [-a-zA-Z0-9_]+ (ASC|DESC)| LIMIT [0-9]+,[0-9]+|$)/i',$str,$match) != 0 && $str == $match)
		{
			$str = substr($str,7);
			$table = stristr($str," ",true);
			$str = straft($str," ");
			$limit = explode(",",straft($str," LIMIT "));
			$str = stristr($str," LIMIT ",true);
			$order = explode(" ",straft($str," ORDER BY "));
			$str = stristr ($str," ORDER BY ",true);
			$this->update($table,$limit,$order,$str);
		}
		else
			self::$error->set("Not a valid mysql query. Query: ".$str);
	}

	protected function selectfrom($table,$fields,$limit,$order,$where)
	{
		$cond = changetoLogic($where);
	}

	protected function update($table,$limit,$order,$where)
	{
		$cond = changetoLogic($where);
	}

	protected function deletefrom($table,$limit,$order,$where)
	{
		$cond = changetoLogic($where);
	}

	protected function insertinto($table,$fields,$values)
	{
		
	}

}

?>
