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
	require_once("./inc/ce.inc.php");


	$today = date("Y-m-d");
	$yesterday = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d") - 1, date("Y")));

	$clicks_today = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date(added)='$today'"));
	$clicks_today = $clicks_today['total'];
	if ($clicks_today > 0) $clicks_today = "+" . $clicks_today;

	$clicks_yesterday = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date(added)='$yesterday'"));
	$clicks_yesterday = $clicks_yesterday['total'];

	$clicks_7days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date_sub(curdate(), interval 7 day) <= added"));
	$clicks_7days = $clicks_7days['total'];

	$clicks_30days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_clickhistory WHERE date_sub(curdate(), interval 30 day) <= added"));
	$clicks_30days = $clicks_30days['total'];

	$users_yesterday = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date(created)='$yesterday'"));
	$users_yesterday = $users_yesterday['total'];

	$users_today = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date(created)='$today'"));
	$users_today = $users_today['total'];
	if ($users_today > 0) $users_today = "+" . $users_today;

	$users_7days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date_sub(curdate(), interval 7 day) <= created"));
	$users_7days = $users_7days['total'];

	$users_30days = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users WHERE date_sub(curdate(), interval 30 day) <= created"));
	$users_30days = $users_30days['total'];

	$all_users = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_users"));
	$all_users = $all_users['total'];

	$all_retailers = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_retailers"));
	$all_retailers = $all_retailers['total'];

	$all_coupons = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons"));
	$all_coupons = $all_coupons['total'];

	$all_reviews = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reviews"));
	$all_reviews = $all_reviews['total'];

	$title = "Admin Home";
	require_once ("inc/header.inc.php");

?>

	<h2>Admin Home</h2>

	<?php if (file_exists("../install.php")) { ?>
		<div class="error_box">You must now delete "install.php" from your server. Failing to delete these files is a serious security risk!</div>
	<?php } ?>

	 <table align="center" width="100%" border="0" cellpadding="2" cellspacing="2">
	 <tr>
		<td width="40%" align="left" valign="top">

			<table align="center" width="95%" border="0" cellpadding="6" cellspacing="2">
			<tr>
				<td nowrap="nowrap" align="left" valign="middle" class="tb2"><font color="#84C315"><b>Cashback</b></font><font color="#5392D5"><b>Engine</b></font> version:</td>
				<td align="right" valign="middle"><?php echo $cashbackengine_version; ?></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">License Key:</td>
				<td nowrap="nowrap" align="right" valign="middle"><?php echo GetSetting('license'); ?></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">Last Login:</td>
				<td nowrap="nowrap" align="right" valign="middle"><?php $last_login = strtotime(GetSetting('last_admin_login')); echo date("d M Y h:i A", $last_login); ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="sline"></div></td>
			</tr>
			</table>

		</td>
		<td width="30%" align="left" valign="top">

			<table align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
			<tr>
				<td align="left" valign="middle" class="tb2">Clicks Today:</td>
				<td align="right" valign="middle" class="stat_s"><a href="clicks.php?date=today"><font color="#2F97EB"><?php echo $clicks_today; ?></font></a></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">Clicks Yesterday:</td>
				<td align="right" valign="middle" class="stat_s"><a href="clicks.php?date=yesterday"><font color="#2F97EB"><?php echo $clicks_yesterday; ?></font></a></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">Last 7 Days Clicks:</td>
				<td align="right" valign="middle" class="stat_s"><a href="clicks.php?date=7days"><font color="#2F97EB"><?php echo $clicks_7days; ?></font></a></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">Last 30 Days Clicks:</td>
				<td align="right" valign="middle" class="stat_s"><a href="clicks.php?date=30days"><font color="#2F97EB"><?php echo $clicks_30days; ?></font></a></td>
			</tr>
			<tr>
				<td colspan="2"><div class="sline"></div></td>
			</tr>
			</table>

		</td>
		<td width="30%" align="left" valign="top">

			<table align="center" width="100%" border="0" cellpadding="3" cellspacing="2">
			<tr>
				<td align="left" valign="middle" class="tb2">Users Today:</td>
				<td align="right" valign="middle" class="stat_s"><a href="users.php"><font color="#2F97EB"><?php echo $users_today; ?></font></a></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">Users Yesterday:</td>
				<td align="right" valign="middle" class="stat_s"><?php echo $users_yesterday; ?></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">Last 7 Days Users:</td>
				<td align="right" valign="middle" class="stat_s"><?php echo $users_7days; ?></td>
			</tr>
			<tr>
				<td align="left" valign="middle" class="tb2">Last 30 Days Users:</td>
				<td align="right" valign="middle" class="stat_s"><?php echo $users_30days; ?></td>
			</tr>
			<tr>
				<td colspan="2"><div class="sline"></div></td>
			</tr>
			</table>

		</td>
	 </tr>
	 </table>

	<!--All Time Stats-->
	<table bgcolor="#F9F9F9" width="95%" align="center" border="0" cellpadding="2" cellspacing="2">
	 <tr>
		<td height="70" width="20%" align="center" valign="middle">
			<span class="stats_total"><?php echo $all_users; ?></span><br/>
			<?php echo ($all_users == 1) ? "member" : "members"; ?>
		</td>
		<td width="20%" align="center" valign="middle">
			<span class="stats_total"><?php echo $all_retailers; ?></span><br/>
			<?php echo ($all_retailers == 1) ? "retailer" : "retailers"; ?>
		</td>
		<td width="20%" align="center" valign="middle">
			<span class="stats_total"><?php echo $all_coupons; ?></span><br/>
			<?php echo ($all_coupons == 1) ? "coupon" : "coupons"; ?>
		</td>
		<td width="20%" align="center" valign="middle">
			<span class="stats_total"><?php echo $all_reviews; ?></span><br/>
			<?php echo ($all_reviews == 1) ? "review" : "reviews"; ?>
		</td>
		<td width="20%" align="center" valign="middle">
			<span class="stats_total" style="color: #8CD706;"><?php echo GetCashbackTotal(); ?></span><br/> cashback
		</td>
	 </tr>
	 </table>


<?php require_once ("inc/footer.inc.php"); ?>