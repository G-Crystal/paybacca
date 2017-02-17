<?php

/*******************************************************************\

 * CashbackEngine v3.0

 * http://www.CashbackEngine.net

 *

 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.

 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------

\*******************************************************************/



	session_start();

	

	unset($_SESSION['userid'], $_SESSION['FirstName'], $_SESSION['goto'], $_SESSION['goto_created'], $_SESSION['goRetailerID'], $_SESSION['goCouponID'], $_SESSION['password_verified']);

	

	session_destroy();



	setcookie("usname", "", time()-3600);



	header("Location: index.php");

	exit();

	

?>