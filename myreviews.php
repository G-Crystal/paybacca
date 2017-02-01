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

	$retailer_id = (int)$_GET['id'];


	if (isset($_GET['act']) && $_GET['act'] == "del")
	{
		$del_query = "DELETE FROM cashbackengine_reviews WHERE user_id='$userid' AND retailer_id='$retailer_id'";
		if (smart_mysql_query($del_query))
		{
			header("Location: myreviews.php?msg=deleted");
			exit();
		}
	}

	
	$query = "SELECT cashbackengine_reviews.*, cashbackengine_retailers.* FROM cashbackengine_reviews cashbackengine_reviews, cashbackengine_retailers cashbackengine_retailers WHERE cashbackengine_reviews.user_id='$userid' AND cashbackengine_reviews.retailer_id=cashbackengine_retailers.retailer_id AND cashbackengine_retailers.status='active' ORDER BY cashbackengine_retailers.title ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_MYREVIEWS_TITLE;

	require_once ("inc/header.inc.php");
	
?>

	<h1><?php echo CBE1_MYREVIEWS_TITLE; ?></h1>


	<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
		<div class="success_msg">
		<?php
			switch ($_GET['msg'])
			{
				case "deleted": echo CBE1_MYREVIEWS_DELETED; break;
			}
		?>
		</div>
	<?php } ?>
	
	<table align="center" class="btb" width="100%" border="0" cellspacing="0" cellpadding="3">
	<tr>
		<th width="40%"><?php echo CBE1_MYREVIEWS_STORE; ?></th>
		<th width="10%"><?php echo CBE1_MYREVIEWS_RATING; ?></th>
		<th width="15%"><?php echo CBE1_MYREVIEWS_DATE; ?></th>
		<th width="10%"><?php echo CBE1_MYREVIEWS_STATUS; ?></th>
		<th width="5%"></th>
	</tr>
	<?php
			$cc = 0;
			$query_reviews = "SELECT *, DATE_FORMAT(added, '".DATE_FORMAT."') AS date_added FROM cashbackengine_reviews WHERE user_id='$userid' ORDER BY added DESC";
			$result_reviews = smart_mysql_query($query_reviews);
			$total_reviews = mysql_num_rows($result_reviews);

			if ($total_reviews > 0) {
	?>
		<p><?php echo CBE1_MYREVIEWS_TEXT; ?></p>

		<?php while ($row_reviews = mysql_fetch_array($result_reviews)) { $cc++; ?>
		 <tr>
			<td valign="middle" align="left"> <a href="<?php echo GetRetailerLink($row_reviews['retailer_id'], GetStoreName($row_reviews['retailer_id'])); ?>"><?php echo GetStoreName($row_reviews['retailer_id']); ?></a></td>
			<td valign="middle" align="center"><img src="<?php echo SITE_URL; ?>images/icons/rating-<?php echo $row_reviews['rating']; ?>.gif" /></td>
			<td valign="middle" align="center"><?php echo $row_reviews['date_added']; ?></td>
			<td valign="middle" align="center">
				<?php
						switch ($row_reviews['status'])
						{
							case "pending":		echo "<span class='pending_status'>".CBE1_STATUS_REVIEW."</span>"; break;
							case "active":		echo "<span class='active_s'>".CBE1_STATUS_ACTIVE."</span>"; break;
							case "inactive":	echo "<span class='inactive_s'>".CBE1_STATUS_INACTIVE."</span>"; break;
							default:			echo "<span class='default_status'>".$row_reviews['status']."</span>"; break;
						}
				?>			
			</td>
			<td valign="middle" align="center"><a href="#" onclick="if (confirm('<?php echo CBE1_MYREVIEWS_DELETE; ?>') )location.href='<?php echo SITE_URL; ?>myreviews.php?act=del&id=<?php echo $row_reviews['retailer_id']; ?>'" title="Delete"><img src="<?php echo SITE_URL; ?>images/delete.png" border="0" alt="Delete" /></a></td>
		</tr>
		<tr>
			<td class="review_brd" colspan="5" align="left" valign="top">
				<div class="myreview">
					<b><?php echo $row_reviews['review_title']; ?></b>
					<p><?php echo $row_reviews['review']; ?></p>
				</div>
			</td>
		</tr>
		<?php } ?>

	<?php }else{ ?>
			<tr height="30"><td colspan="5" align="center"><?php echo CBE1_MYREVIEWS_NO; ?></td></tr>
	<?php } ?>
	</table>

<?php require_once ("inc/footer.inc.php"); ?>