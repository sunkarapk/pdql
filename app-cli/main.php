<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

include_once 'config.php';

echo "Database Username & Cash\n";

$r = $db->query("SELECT * FROM users");
for($i=0; !empty($r[$i]);$i++)
{
	echo "\n-------------------------\nName: ".$r[$i]['username']."\nCash: ".$r[$i]['cash']."\n";
}

?>
