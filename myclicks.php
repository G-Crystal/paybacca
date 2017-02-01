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
	require_once("inc/pagination.inc.php");

	$cc = 0;

	$results_per_page = 10;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$query = "SELECT cashbackengine_clickhistory.*, DATE_FORMAT(cashbackengine_clickhistory.added, '".DATE_FORMAT." %h:%i %p') AS click_date, cashbackengine_retailers.* FROM cashbackengine_clickhistory cashbackengine_clickhistory, cashbackengine_retailers cashbackengine_retailers WHERE cashbackengine_clickhistory.user_id='$userid' AND cashbackengine_clickhistory.retailer_id=cashbackengine_retailers.retailer_id ORDER BY cashbackengine_clickhistory.added DESC LIMIT $from, $results_per_page";

	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_clickhistory WHERE user_id='$userid' ORDER BY added DESC");
	$total = mysql_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_CLICK_TITLE;

	require_once ("inc/header.inc.php");

?>
		<h1><?php echo CBE1_CLICK_TITLE; ?></h1>

		<?php if ($total > 0) { ?><p align="center"><?php echo CBE1_CLICK_TEXT; ?></p><?php } ?>

        <table align="center" width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
			<th width="5%">&nbsp;</th>
			<th width="40%"><?php echo CBE1_CLICK_STORE; ?></th>
			<th width="30%"><?php echo CBE1_CLICK_ID; ?></th>
			<th width="25%"><?php echo CBE1_CLICK_DATE; ?></th>
        </tr>
		<?php if ($total > 0) { ?>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
              <tr height="30" class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<td valign="middle" align="center"><img src="<?php echo SITE_URL; ?>images/check_blue.png" /></td>
				<td valign="middle" align="left"><a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a></td>
				<td nowrap="nowrap" valign="middle" align="center"><?php echo $row['click_id']; ?></td>
				<td nowrap="nowrap" valign="middle" align="center"><?php echo $row['click_date']; ?></td>
              </tr>
			<?php } ?>
			<tr>
			<td style="border-top: 1px solid #F5F5F5" colspan="4" align="center">
				<?php echo ShowPagination("clickhistory",$results_per_page,"myclicks.php?","WHERE user_id='$userid'"); ?>
			</td>
			</tr>
		<?php }else{ ?>
			<tr height="30"><td style="border-bottom: 1px solid #F5F5F5" colspan="4" align="center"><?php echo CBE1_CLICK_NO; ?></td></tr>
		<?php } ?>
		 </table>

<?php require_once ("inc/footer.inc.php"); ?>