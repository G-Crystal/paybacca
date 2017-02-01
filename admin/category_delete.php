<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		smart_mysql_query("DELETE FROM cashbackengine_categories WHERE category_id='$id'");
		smart_mysql_query("DELETE FROM cashbackengine_retailer_to_category WHERE category_id='$id'");

		$res = smart_mysql_query("SELECT category_id FROM cashbackengine_categories WHERE parent_id='$id'");

		if (mysql_num_rows($res) > 0)
		{
			while ($row = mysql_fetch_array($res))
			{
				smart_mysql_query("DELETE FROM cashbackengine_categories WHERE category_id='".$row['category_id']."'");
			}
		}

		header("Location: categories.php?msg=deleted");
		exit();
	}

?>