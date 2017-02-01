<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_404_TITLE;

	require_once ("inc/header.inc.php");
	
?>

	<h1><?php echo CBE1_404_TITLE; ?></h1>

	<p align="center"><img src="<?php echo SITE_URL; ?>images/404.png" /></p>
	<p align="center"><?php echo CBE1_404_TEXT; ?></p><br/>
	<p align="center"><a class="goback" href="<?php echo SITE_URL; ?>"><?php echo CBE1_404_GOBACK; ?></a></p>


<?php require_once ("inc/footer.inc.php"); ?>