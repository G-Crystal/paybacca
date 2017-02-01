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


	$title = "Add Language";
	require_once ("inc/header.inc.php");

?>
 
        <h2>Add Language</h2>

		<p align="center">Simply upload new language file(s) in to <b>/language/</b> directory.</p>
		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>


<?php require_once ("inc/footer.inc.php"); ?>