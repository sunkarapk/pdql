<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */
  
define('MODE','cli');

include_once 'data/config.php';

define('DBNAME','exdb-cli');
define('DBUSER','pavan');
define('DBPASS','pkumar');

$db = new db(DBNAME,DBUSER,DBPASS);

?>
