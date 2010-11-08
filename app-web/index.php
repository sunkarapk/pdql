<?php
/* Name: PDQL
 * Description: Php based Database and Query Language
 * Author: Pavan Kumar Sunkara <pavan.sss1991@gmail.com>
 * Copyright: Copyright (C) 2009 Sun Web dev, Inc.
 * Licence: You may redistribute this under Creative Commons License
 */

include_once 'config.php';

if(empty($_GET['a']))
	$_GET['a'] = "Index";
$action = $_GET['a'];

?>
<html>
	<head>
		<title>PDQL Web Application Example - <? echo $action; ?></title>
	</head>
	<body>		
<?
	if($action == "Index")
	{
		echo "<table>
			<tr>
				<th>Name</th>
				<th>Cash</th>
				<th>Actions</th>
			</tr>";

		$r = $db->query("SELECT * FROM users");
		for($i=0;!empty($r[$i]);$i++)
		{
			echo "<tr>
				<td>".$r[$i]['username']."</td>
				<td>$ ".$r[$i]['cash']."</td>
				<td><a href=index.php?a=Delete&v=$i>Delete</a> <a href=index.php?a=Edit&v=$i>Edit</a></td>
			</tr>";
		}

		echo "</table>";
		echo "<br><a href=index.php?a=Insert>Add new user</a>";
	}
	else if($action == "Delete")
	{
		$db->query("DELETE FROM users LIMIT ".$_GET['v'].",".($_GET['v']+1));
		echo "User deleted<br>Click <a href=index.php>here</a> to continue";
	}
	else if($action == "Edit")
	{
		$v = $_GET['v'];
		$r = $db->query("SELECT * FROM users LIMIT $v,".($v+1));
		echo "<form action=index.php?a=Update&v=$v method=post>
			Username: <input type=text value=".$r[0]['username']." name=username><br>
			Password: <input type=password value=".$r[0]['password']." name=password><br>
			Cash: <input type=text value=".$r[0]['cash']." name=cash><br>
			<input type=submit value='Edit this entry!'>
		</form>";
	}
	else if($action == "Update")
	{
		$v = $_GET['v'];
		$db->query("UPDATE users SET username = '".$_POST['username']."',password = '".$_POST['password']."',cash = '".$_POST['cash']."' LIMIT $v,".($v+1));
		echo "User updated<br>Click <a href=index.php>here</a> to continue";
	}
	else if($action == "Insert")
	{
		if(empty($_POST['submit']))
		{
			echo "<form action=index.php?a=Insert method=post>
				Username: <input type=text name=username><br>
				Password: <input type=password name=password><br>
				Cash: <input type=text name=cash><br>
				<input type=submit name=submit value='Add this entry!'>
			</form>";
		}
		else
		{
			$db->query("INSERT INTO users VALUES ('".$_POST['username']."','".$_POST['password']."','".$_POST['cash']."')");
			echo "User added<br>Click <a href=index.php>here</a> to continue";
		}
	}
?>
	</body>
</html>
