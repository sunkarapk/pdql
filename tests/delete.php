<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

include_once 'config.php';

$db->query("DELETE FROM users WHERE password='pkumar' ORDER BY cash DESC LIMIT 2,3")

?>
