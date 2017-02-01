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
	require_once("inc/pagination.inc.php");


	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0 && in_array($_GET['show'], $results_on_page))
	{
		$results_per_page = (int)$_GET['show'];
		if (!(isset($_GET['go']) && $_GET['go'] == 1))$page = 1;
	}
	else
	{
		$results_per_page = RESULTS_PER_PAGE;
	}

	$cc = 0;

	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "title": $rrorder = "title"; break;
				case "added": $rrorder = "added"; break;
				case "visits": $rrorder = "visits"; break;
				case "cashback": $rrorder = "cashback"; break;
				default: $rrorder = "title"; break;
			}
		}
		else
		{
			$rrorder = "title";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "asc"; break;
			}
		}
		else
		{
			$rorder = "asc";
		}
	//////////////////////////////////////////////////

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$where = " featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'";

	if ($rrorder == "cashback")
		$order_by = "ABS(cashback) $rorder";
	else
		$order_by = "$rrorder $rorder";

	$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY $order_by LIMIT $from, $results_per_page";
	
	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY $rrorder $rorder");
	$total = mysql_num_rows($total_result);
	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_FEATURED_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><img src="<?php echo SITE_URL; ?>images/icon_featured.png" align="absmiddle" /> <?php echo CBE1_FEATURED_TITLE; ?></h1>

	<?php if ($total > 0) { ?>

		<p><?php echo CBE1_FEATURED_TEXT; ?></p>

		<?php if (!isLoggedIn()) { ?><div class="login_msg"><?php echo CBE1_STORES_LOGIN; ?></div><?php } ?>

		<div class="browse_top">
			<div class="sortby">
				<form action="" id="form1" name="form1" method="get">
					<span><?php echo CBE1_SORT_BY; ?>:</span>
					<select name="column" id="column" onChange="document.form1.submit()">
						<option value="title" <?php if ($_GET['column'] == "title") echo "selected"; ?>><?php echo CBE1_SORT_NAME; ?></option>
						<option value="visits" <?php if ($_GET['column'] == "visits") echo "selected"; ?>><?php echo CBE1_SORT_POPULARITY; ?></option>
						<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>><?php echo CBE1_SORT_DATE; ?></option>
						<option value="cashback" <?php if ($_GET['column'] == "cashback") echo "selected"; ?>><?php echo CBE1_SORT_CASHBACK; ?></option>
					</select>
					<select name="order" id="order" onChange="document.form1.submit()">
						<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
						<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
					</select>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					&nbsp;&nbsp;
					<span><?php echo CBE1_RESULTS; ?>:</span>
					<select name="show" id="show" onChange="document.form1.submit()">
						<option value="5" <?php if ($results_per_page == "5") echo "selected"; ?>>5</option>
						<option value="10" <?php if ($results_per_page == "10") echo "selected"; ?>>10</option>
						<option value="25" <?php if ($results_per_page == "25") echo "selected"; ?>>25</option>
						<option value="50" <?php if ($results_per_page == "50") echo "selected"; ?>>50</option>
						<option value="100" <?php if ($results_per_page == "100") echo "selected"; ?>>100</option>
						<option value="111111" <?php if ($results_per_page == "111111") echo "selected"; ?>><?php echo CBE1_RESULTS_ALL; ?></option>
					</select>
				</form>
			</div>
			<div class="results">
				<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total; ?>
			</div>
		</div>

			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				<tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td width="<?php echo IMAGE_WIDTH; ?>" align="center" valign="middle">
						<div class="imagebox"><a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a></div>
					</td>
					<td align="left" valign="bottom">
						<table width="100%" border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td width="80%" align="left" valign="top">
									<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
								</td>
								<td nowrap="nowrap" width="20%" align="right" valign="middle">
									<a class="coupons" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons" title="<?php echo $row['title']; ?> Coupons"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a>
								</td>
							</tr>
							<tr>
								<td valign="middle" align="left"><?php echo TruncateText(stripslashes($row['description']), STORES_DESCRIPTION_LIMIT); ?>&nbsp;</div></td>
								<td valign="middle" align="center">
								<?php if ($row['cashback'] != "") { ?>
									<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
									<span class="cashback"><span class="value"><?php echo DisplayCashback($row['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?></span>
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td valign="middle" align="left">
									<?php
										$share_title = urlencode($row['title']." ".CBE1_STORE_EARN." ".DisplayCashback($row['cashback'])." ".CBE1_CASHBACK2);
										if (isLoggedIn()) $share_add .= "&ref=".(int)$_SESSION['userid'];
										$share_link = urlencode(GetRetailerLink($row['retailer_id'], $row['title']).$share_add);
									?>
									<a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $share_link; ?>&t=<?php echo $share_title; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>" rel="nofollow"><img src="<?php echo SITE_URL; ?>images/icon_facebook.png"  alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" align="absmiddle" /></a>&nbsp;
									<a href="https://twitter.com/intent/tweet?text=<?php echo $share_title; ?>&url=<?php echo $share_link; ?>&via=<?php echo SITE_TITLE; ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>" rel="nofollow"><img src="<?php echo SITE_URL; ?>images/icon_twitter.png" alt="<?php echo CBE1_SHARE_TWITTER; ?>" align="absmiddle" /></a>
									&nbsp;&nbsp;
									<?php if ($row['conditions'] != "") { ?>
										<div class="cashbackengine_tooltip">
											<a class="conditions" href="#"><?php echo CBE1_CONDITIONS; ?></a> <span class="tooltip"><?php echo $row['conditions']; ?></span>
										</div>
									<?php } ?>
									<a class="favorites" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>"><?php echo CBE1_ADD_FAVORITES; ?></a>
								</td>
								<td valign="middle" align="right">
									<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php } ?>
			</table>

			<?php echo ShowPagination("retailers",$results_per_page,"featured.php?column=$rrorder&order=$rorder&show=$results_per_page&go=1&","WHERE ".$where); ?>

	<?php }else{ ?>
		<p align="center"><?php echo CBE1_FEATURED_NO; ?></p>
		<div class="sline"></div>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>