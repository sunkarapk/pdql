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
	
	protected $json;

	public function __construct($dbname,$dbuser,$dbpass,$error=array(NULL, NULL))
	{
		self::$error = new error($error[0],$error[1]);
		$this->json = new Services_JSON();

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
		if(!is_dir(CWD.$dbname))
			mkdir(CWD.$dbname);
	}

	protected function nodb($dbname)
	{
		self::$error->set("Database '".$dbname."' is not available");
		$this->closeconnection();
	}
	
	protected function nouser($dbuser)
	{
		self::$error->set("User named '".$dbuser."' is not found/Wrong password");
		$this->closeconnection();
	}

	protected function closeconnection()
	{
		self::$db = null;
	}

	protected function checkTableName($table)
	{
		$check = array('mysql','from','limit','select','delete','insert','update','where','values');
		if(in_array(strtolower($table),$check))
			self::$error->set($table." can't be used as a table name.");
	}

	public function query($str)
	{
		if(self::$db == NULL)
			self::$error->set("No database connection found");
	
		if(preg_match("/^SELECT [\*,-a-zA-Z0-9_]+ FROM [-a-zA-Z0-9_]+( WHERE ([-a-zA-Z0-9_]+(( = | != | <= | >= | < | > )'[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'| IS NULL|IS NOT NULL| LIKE '[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'| NOT LIKE '[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+')| AND | OR |(\s)?(\(|\))?(\s)?)+)?( ORDER BY [-a-zA-Z0-9_]+ (ASC|DESC)| LIMIT [0-9]+,[0-9]+|$)+/i",$str,$match) != 0 && $str == $match[0])
		{
			$str = substr($str,7);
			$fields = NULL;
			if(substr($str,0,2) != '* ')
				$fields = explode(",",strbef($str," FROM "));
			$str = straft($str," FROM ");
			$table = strbef($str," ");
			$this->checkTableName($table);
			$str = straft($str," ");
			$limit = explode(",",straft($str," LIMIT "));
			$str = strbef($str," LIMIT ");
			$order = explode(" ",straft($str," ORDER BY "));
			$str = strbef($str," ORDER BY ");
			$str = straft($str,"WHERE ");
			return $this->selectfrom($table,$fields,$limit,$order,$str);
		}
		else if(preg_match("/^DELETE FROM [-a-zA-Z0-9_]+( WHERE ([-a-zA-Z0-9_]+(( = | != | <= | >= | < | > )'[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'| IS NULL|IS NOT NULL| LIKE '[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'| NOT LIKE '[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+')| AND | OR |(\s)?(\(|\))?(\s)?)+)?( ORDER BY [-a-zA-Z0-9_]+ (ASC|DESC)| LIMIT [0-9]+,[0-9]+|$)+/i",$str,$match) != 0 && $str == $match[0])
		{
			$str = substr($str,12);
			$table = strbef($str," ");
			$this->checkTableName($table);
			$str = straft($str," ");
			$limit = explode(",",straft($str," LIMIT "));
			$str = strbef($str," LIMIT ");
			$order = explode(" ",straft($str," ORDER BY "));
			$str = strbef($str," ORDER BY ");
			$str = straft($str,"WHERE ");
			$this->deletefrom($table,$limit,$order,$str);
		}
		else if(preg_match("/^INSERT INTO [-a-zA-Z0-9_]+ (\([,-a-ZA-Z0-9_]+\) )?VALUES \('[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'(,'[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+')*\)/i",$str,$match) != 0 && $str == $match[0])
		{
			$fields = null;
			$str = substr($str,12);
			$table = strbef($str," ");
			$this->checkTableName($table);
			$str = straft($str," ");
			$buf = strbef($str,"VALUES ");
			$str = straft($str,"VALUES (");
			if(!empty($buf))
			{
				$buf = substr($buf,1);
				$buf = strbef($buf,") ");
				$fields = explode(",",$buf);
			}
			$values = explode(",",strbef($str,")"));
			$this->insertinto($table,$fields,$values);
		}
		else if(preg_match("/^UPDATE [-a-zA-Z0-9_]+ SET [-a-zA-Z0-9_]+ = ('NULL'|'[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'|'DEFAULT')(,[-a-zA-Z0-9_]+ = ('NULL'|'[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'|'DEFAULT'))*( WHERE ([-a-zA-Z0-9_]+(( = | != | <= | >= | < | > )'[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'| IS NULL|IS NOT NULL| LIKE '[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+'| NOT LIKE '[%-a-zA-Z0-9_\/\(\)\s:;,@\.]+')| AND | OR |(\s)?(\(|\))?(\s)?)+)?( ORDER BY [-a-zA-Z0-9_]+ (ASC|DESC)| LIMIT [0-9]+,[0-9]+|$)+/i",$str,$match) != 0 && $str == $match[0])
		{
			$str = substr($str,7);
			$table = strbef($str," ");
			$this->checkTableName($table);
			$str = straft($str," ");
			$limit = explode(",",straft($str," LIMIT "));
			$str = strbef($str," LIMIT ");
			$order = explode(" ",straft($str," ORDER BY "));
			$str = strbef($str," ORDER BY ");
			$where = straft($str,"WHERE ");
			$str = strbef($str,"WHERE ");
			$set = explode(",",straft($str,"SET "));
			foreach($set as $id=>$key)
				$set[$id] = explode(" = '",$key);
			$this->update($table,$set,$limit,$order,$where);
		}
		else
			self::$error->set("Not a valid mysql query. Query: ".$str);
	}

	protected function selectfrom($table,$fields,$limit,$order,$where)
	{
		$flag = false;
		$row = null;
		$cond = changetoLogic($where);
		
		$fp = fopen(self::$db."mysql","r");
		while(fscanf($fp,"%s\n",$hash))
		{
			$tbarr = $this->json->decode($hash);
			if($tbarr->name == $table);
			{
				$tbf = self::$db.$table;
				break;
			}
		}
		
		if(!empty($tbf))
		{
			$tbfp = fopen($tbf,"a+");
			$tbfl = $tbarr->fields;
			$tbvl = array();

			while(fscanf($tbfp,"%s",$hash))
			{
				if(!empty($hash))
				{
					$row = $this->json->decode($hash);
					$row = make_assoc_array($tbfl,$row);

					if(!empty($cond))
						eval("\$flag = (".$cond.");");
					else
						$flag = true;
						
					if($flag)
						array_push($tbvl,$row);
				}
				$flag = false;
				$hash = null;
			}

			if(is_array($order))
			{
				if(strtolower($order[1]) == "asc")
					$tbvl = array_key_sort($tbvl,$order[0]);
				else if(strtolower($order[1]) == "desc")
					$tbvl = array_key_rsort($tbvl,$order[0]);
			}

			$res = $tbvl;
			
			if(!empty($limit[0]))
			{
				$res = array();
				for($i=$limit[0];$i<=$limit[1];$i++)
					array_push($res,$tbvl[$i]);
			}
			
			return $res;
		}
	}

	protected function update($table,$set,$limit,$order,$where)
	{
		$flag = true;
		$row = null;
		$cond = changetoLogic($where);

		$fp = fopen(self::$db."mysql","r");
		while(fscanf($fp,"%s\n",$hash))
		{
			$tbarr = $this->json->decode($hash);
			if($tbarr->name == $table);
			{
				$tbf = self::$db.$table;
				break;
			}
		}
		
		if(!empty($tbf))
		{
			$tbfp = fopen($tbf,"a+");
			$tbfl = $tbarr->fields;
			$tbvl = array();
			$tbup = array();
			$tbud = array();

			while(fscanf($tbfp,"%s",$hash))
			{
				if(!empty($hash))
				{
					$row_orig = $this->json->decode($hash);
					$row = make_assoc_array($tbfl,$row_orig);

					if(!empty($cond))
						eval("\$flag = (".$cond.");");
					else
						$flag = true;
						
					if(!$flag)
						array_push($tbvl,$row_orig);
					else
						array_push($tbup,$row_orig);
				}
				$flag = true;
				$hash = null;
			}

			$order[0] = get_key_index($tbfl,$order[0]);

			if(is_array($order))
			{
				if(strtolower($order[1]) == "asc")
					$tbup = array_key_sort($tbup,$order[0]);
				else if(strtolower($order[1]) == "desc")
					$tbup = array_key_rsort($tbup,$order[0]);	
			}
			
			if(!empty($limit[0]))
			{
				for($i=0;!empty($tbup[$i]);$i++)
				{
					if($i<$limit[0] || $i>$limit[1])
						array_push($tbvl,$tbup[$i]);
					else
						array_push($tbud,$tbup[$i]);
				}
			}
			
			foreach($set as $id=>$key)
			{
				for($i=0;!empty($tbud[$i]);$i++)
				{
					$ind = get_key_index($tbfl,$key[0]);
					$tbud[$i][$ind] = stripquotes($key[1],false);
				}
			}

			for($i=0;!empty($tbud[$i]);$i++)
				array_push($tbvl,$tbud[$i]);

			$tbfp = fopen($tbf,"w");
			foreach($tbvl as $id=>$arr)
			{
				fwrite($tbfp,$this->json->encode($arr)."\n");
			}
		}
	}

	protected function deletefrom($table,$limit,$order,$where)
	{
		$flag = true;
		$row = null;
		$cond = changetoLogic($where);
		
		$fp = fopen(self::$db."mysql","r");
		while(fscanf($fp,"%s\n",$hash))
		{
			$tbarr = $this->json->decode($hash);
			if($tbarr->name == $table);
			{
				$tbf = self::$db.$table;
				break;
			}
		}
		
		if(!empty($tbf))
		{
			$tbfp = fopen($tbf,"a+");
			$tbfl = $tbarr->fields;
			$tbvl = array();
			$tblf = array();

			while(fscanf($tbfp,"%s",$hash))
			{
				if(!empty($hash))
				{
					$row_orig = $this->json->decode($hash);
					$row = make_assoc_array($tbfl,$row_orig);

					if(!empty($cond))
						eval("\$flag = (".$cond.");");
					else
						$flag = true;
						
					if(!$flag)
						array_push($tbvl,$row_orig);
					else
						array_push($tblf,$row_orig);
				}
				$flag = true;
				$hash = null;
			}

			$order[0] = get_key_index($tbfl,$order[0]);

			if(is_array($order))
			{
				if(strtolower($order[1]) == "asc")
					$tblf = array_key_sort($tblf,$order[0]);
				else if(strtolower($order[1]) == "desc")
					$tblf = array_key_rsort($tblf,$order[0]);	
			}
			
			if(!empty($limit[0]))
			{
				for($i=0;!empty($tblf[$i]);$i++)
					if($i<$limit[0] || $i>$limit[1])
						array_push($tbvl,$tblf[$i]);
			}
			
			$tbfp = fopen($tbf,"w");
			foreach($tbvl as $id=>$arr)
			{
				fwrite($tbfp,$this->json->encode($arr)."\n");
			}
		}
	}

	protected function insertinto($table,$fields,$values)
	{
		$fp = fopen(self::$db."mysql","r");
		while(fscanf($fp,"%s\n",$hash))
		{
			$tbarr = $this->json->decode($hash);
			if($tbarr->name == $table);
			{
				$tbf = self::$db.$table;
				break;
			}
		}
		
		if(!empty($tbf))
		{
			$tbfp = fopen($tbf,"a+");
			$tbfl = $tbarr->fields;
			$tbvl = array();
			
			if(empty($fields))
			{
				if(count($tbfl) != count($values))
					self::$error->set("The fields count doesn't match with values count");
				foreach($values as $key=>$value)
				{
					$value = stripquotes($value);
				
					if($value == 'NULL' || $value == 'null')
						$value = NULL;
					if($value == 'default' || $value == 'DEFAULT')
						$value = $tbfl[$key]->defaults;
					array_push($tbvl,$value);
				}
				fwrite($tbfp,$this->json->encode($tbvl)."\n");
			}
			else
			{
				if(count($fields) != count($values))
					self::$error->set("The fields count doesn't match with values count");
				foreach($values as $key=>$value)
				{
					$tbfr = array_child_search($fields[$key],$tbfl,'name');
					$value = stripslashes($value);
										
					if($value == 'NULL' || $value == 'null')
						$value = NULL;
					if($value == 'default' || $value == 'DEFAULT')
						$value = $tbfr['default'];
					array_push($tbvl,$value);
				}
				fwrite($tbfp,$this->json->encode($tbvl)."\n");
			}
		}
	}

}

?>
