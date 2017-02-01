<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	require_once("inc/config.inc.php");

	if (!(isset($_SESSION['userid']) && is_numeric($_SESSION['userid'])))
	{
		// check cookie
		if (!CheckCookieLogin())
		{
			header("Location: login.php?msg=3");
			exit();
		}
	}
	else
	{
		$userid	= (int)$_SESSION['userid'];
	}

?>