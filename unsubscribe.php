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


	if (isset($_GET['key']) && is_string($_GET['key']) && preg_match('/^[a-z\d]{32}$/i', $_GET['key']))
	{
		$unsubscribe_key	= strtolower(mysql_real_escape_string(getGetParameter('key')));
		$unsubscribe_key	= preg_replace("/[^0-9a-zA-Z]/", " ", $unsubscribe_key);
		$unsubscribe_key	= substr(trim($unsubscribe_key), 0, 32);

		$check_result = smart_mysql_query("SELECT newsletter FROM cashbackengine_users WHERE unsubscribe_key='$unsubscribe_key' LIMIT 1");
        if (mysql_num_rows($check_result) > 0)
		{
			$check_row = mysql_fetch_array($check_result);

			if ($check_row['newsletter'] == "0")
			{
				header ("Location: unsubscribe.php?msg=1");
				exit();
			}
			elseif ($check_row['newsletter'] == "1")
			{
				smart_mysql_query("UPDATE cashbackengine_users SET newsletter='0' WHERE unsubscribe_key='$unsubscribe_key' LIMIT 1");
				header ("Location: unsubscribe.php?msg=1");
				exit();
			}
		}
		else
		{
			header ("Location: index.php");
			exit();
		}
	}


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_UNSUBSCRIBE_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_UNSUBSCRIBE_TITLE; ?></h1>

	<?php if (isset($_GET['msg']) && is_numeric($_GET['msg'])) { ?>
	
		<?php if ($_GET['msg'] == 1) { echo CBE1_UNSUBSCRIBE_MSG1; } ?>
		<?php if ($_GET['msg'] == 2) { echo CBE1_UNSUBSCRIBE_MSG2; } ?>
	
	<?php } ?>

<?php require_once("inc/footer.inc.php"); ?>