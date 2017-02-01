<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");


	if (isset($_POST['action']) && $_POST['action'] == "report")
	{
		unset($errs);
		$errs = array();

		$retailer_id	= (int)getPostParameter('rid');
		$report			= mysql_real_escape_string(nl2br(getPostParameter('report')));

		if (!($report))
		{
			$errs[] = CBE1_REPORT_ERR1;
		}
		else
		{
			$check_query = smart_mysql_query("SELECT * FROM cashbackengine_reports WHERE reporter_id='$userid' AND retailer_id='$retailer_id'");
			if (mysql_num_rows($check_query) != 0)
			{
				$errs[] = CBE1_REPORT_ERR2;
			}
		}

		if (count($errs) == 0)
		{
			$query = "INSERT INTO cashbackengine_reports SET reporter_id='$userid', retailer_id='$retailer_id', report='$report', viewed='0', status='active', added=NOW()";
			$result = smart_mysql_query($query);

			// send email notification //
			if (NEW_REPORT_ALERT == 1)
			{
				SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT5, CBE1_EMAIL_ALERT5_MSG);
			}
			/////////////////////////////
		
			header("Location: report_retailer.php?id=$retailer_id&msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$retailer_id = (int)$_GET['id'];
	}
	else
	{		
		header ("Location: index.php");
		exit();
	}

	$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1"; 
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_REPORT_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_REPORT_TITLE; ?></h1>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div class="success_msg"><?php echo CBE1_REPORT_SENT; ?></div>
	<?php } ?>

	<?php if (isset($allerrors) && $allerrors != "") { ?>
		<div class="error_msg"><?php echo $allerrors; ?></div>
	<?php } ?>

	<?php if ($total > 0) { $row = mysql_fetch_array($result); ?>

		<?php if (!(isset($_GET['msg']) && $_GET['msg'] == 1)) { ?>		
			<img src="<?php echo SITE_URL; ?>images/report.png" align="right" />
			<b><?php echo $row['title']; ?></b>
			<p><?php echo CBE1_REPORT_TEXT; ?></p>
			<form action="" method="post">
			<textarea name="report" cols="55" rows="5" class="textbox2"><?php echo getPostParameter('report'); ?></textarea>
				<input type="hidden" name="rid" value="<?php echo (int)$row['retailer_id']; ?>" />
				<input type="hidden" name="action" value="report" /><br/><br/>
				<input type="submit" class="submit" value="<?php echo CBE1_SUBMIT_BUTTON; ?>" />
				<input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onclick="history.go(-1);return false;" />
			</form>
		<?php } ?>

	<?php }else{ ?>
		<p align="center"><?php echo CBE1_REPORT_NO; ?></p>
		<p align="center"><a class="goback" href="<?php echo SITE_URL; ?>"><?php echo CBE1_GO_BACK; ?></a></p>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>