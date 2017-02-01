<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	if (isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))
	{
		header("Location: myaccount.php");
		exit();
	}

?>