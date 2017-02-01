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

	$cc = 0;

	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0 && in_array($_GET['show'], $results_on_page))
	{
		$results_per_page = (int)$_GET['show'];
		if (!(isset($_GET['go']) && $_GET['go'] == 1)) $page = 1;
	}
	else
	{
		$results_per_page = RESULTS_PER_PAGE;
	}


	if (isset($_GET['view']) && $_GET['view'] != "")
	{
		switch ($_GET['view'])
		{
			case "full":	$STORES_LIST_STYLE = 1; break;
			case "list":	$STORES_LIST_STYLE = 2; break;
			default:		$STORES_LIST_STYLE = STORES_LIST_STYLE; break;
		}

		$_SESSION['view'] = $STORES_LIST_STYLE;
	}

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

	$where = "";

	if (isset($_GET['cat']) && is_numeric($_GET['cat']) && $_GET['cat'] > 0)
	{
		$cat_id = (int)$_GET['cat'];

		$cat_query = "SELECT * FROM cashbackengine_categories WHERE category_id='$cat_id' LIMIT 1";
		$cat_result = smart_mysql_query($cat_query);
		if (mysql_num_rows($cat_result) > 0)
		{
			$cat_row = mysql_fetch_array($cat_result);
			$totitle = $cat_row['name'];
		}
		else
		{
			// if category not found //
			$not_found = 1;
			$totitle = CBE1_STORES_CNO;

			header ("Location: retailers.php");
			exit();
		}
		
		unset($retailers_per_category);
		$retailers_per_category = array();
		$retailers_per_category[] = "111111111111111111111";

		$sql_retailers_per_category = smart_mysql_query("SELECT retailer_id FROM cashbackengine_retailer_to_category WHERE category_id='$cat_id'");
		while ($row_retailers_per_category = mysql_fetch_array($sql_retailers_per_category))
		{
			$retailers_per_category[] = $row_retailers_per_category['retailer_id'];
		}

		$where .= "retailer_id IN (".implode(",",$retailers_per_category).") AND";
	}
	
	if (isset($_GET['letter']) && in_array($_GET['letter'], $alphabet))
	{
		$ltr = mysql_real_escape_string(getGetParameter('letter'));
		
		if ($ltr == "0-9")
		{
			$where .= " title REGEXP '^[0-9]' AND";
		}
		else
		{
			$ltr = substr($ltr, 0, 1);
			$where .= " UPPER(title) LIKE '$ltr%' AND";
		}

		$totitle = " $ltr";
	}

	$where .= " (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active'";
	
	if ($rrorder == "cashback")
		$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY ABS(cashback) $rorder LIMIT $from, $results_per_page";
	else
		$query = "SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY featured DESC, $rrorder $rorder LIMIT $from, $results_per_page";
	
	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $where ORDER BY title ASC");
	$total = mysql_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE	= $totitle." ".CBE1_STORES_STORES;
	
	if ($cat_id)
	{
		$PAGE_DESCRIPTION	= $cat_row['meta_description'];
		$PAGE_KEYWORDS		= $cat_row['meta_keywords'];
	}
	else
	{
		$PAGE_DESCRIPTION	= "";
		$PAGE_KEYWORDS		= "";
	}

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo $totitle." ".CBE1_STORES_STORES; ?></h1>

	<div class="breadcrumbs"><a href="<?php echo SITE_URL; ?>" class="home_link"><?php echo CBE1_BREADCRUMBS_HOME; ?></a> &#155; <a href="<?php echo SITE_URL; ?>retailers.php"><?php echo CBE1_BREADCRUMBS_STORES; ?></a> <?php echo ($totitle != "") ? "&#155; ".$totitle : ""; ?></div>

	<?php if ($cat_row['description'] != "") { ?>
		<p class="category_description"><?php echo $cat_row['description']; ?></p>
	<?php } ?>

	<div id="alphabet">
		<ul>
			<li><a href="<?php echo SITE_URL; ?>retailers.php" <?php if (empty($ltr)) echo 'class="active"'; ?>><?php echo CBE1_STORES_ALL; ?></a></li>
			<?php

				$numLetters = count($alphabet);
				$i = 0;

				foreach ($alphabet as $letter)
				{
					$i++;
					if ($i == $numLetters) $lilast = ' class="last"'; else $lilast = '';
					if (isset($ltr) && $ltr == $letter) $liclass = ' class="active"'; else $liclass = '';
					echo "<li".$lilast."><a href=\"".SITE_URL."retailers.php?".$view_a."letter=$letter\" $liclass>$letter</a></li>";
				}
			?>
		</ul>
	</div>

	<?php

		if ($total > 0) {

	?>
		<?php if (!isLoggedIn()) { ?><div class="login_msg"><?php echo CBE1_STORES_LOGIN; ?></div><?php } ?>

		<?php
			// show random featured retailers //
			$fwhere = $where." AND featured='1'";
			$result_featured = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE $fwhere ORDER BY RAND() LIMIT ".FEATURED_STORES_LIMIT);
			$total_fetaured = mysql_num_rows($result_featured);

			if ($total_fetaured > 0) { 
		?>
			<h3 class="featured_title"><?php echo $totitle." ".CBE1_STORES_FEATURED; ?></h3>
			<div id="scrollstores">
			<?php while ($row_featured = mysql_fetch_array($result_featured)) { $cc++; ?>
			<div>
				<div class="imagebox"><a href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>"><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></a></div>
				<?php if ($row_featured['cashback'] != "") { ?><span class="thumbnail-text"><span class="cashback"><?php echo DisplayCashback($row_featured['cashback']); ?></span> <?php echo CBE1_CASHBACK2; ?></span><?php } ?>
			</div>
			<?php } ?>
			</div>
			<div style="clear: both"></div>
		<?php } // end featured retailers ?>


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
					<?php if ($cat_id) { ?><input type="hidden" name="cat" value="<?php echo $cat_id; ?>" /><?php } ?>
					<?php if ($ltr) { ?><input type="hidden" name="letter" value="<?php echo $ltr; ?>" /><?php } ?>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="view" value="<?php echo $view; ?>" />
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
					&nbsp;&nbsp;
					<a href="?view=full"><img src="<?php echo SITE_URL; ?>images/list2.png" align="absmiddle" /></a>
					<a href="?view=list"><img src="<?php echo SITE_URL; ?>images/list.png" align="absmiddle" /></a>
				</form>
			</div>
			<div class="results">
				<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total; ?>
			</div>
		</div>

		<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php if (@$_SESSION['view'] == 2) { ?>
			<tr>
      			<th width="50%" align="left"><?php echo CBE1_STORES_NAME; ?></a></th>
				<th width="20%" align="center"><?php echo CBE1_CASHBACK2; ?></th>
				<th width="10%" align="center"><?php echo CBE1_STORES_COUPONS; ?></th>
				<th width="20%"align="center"><?php echo CBE1_STORES_VISIT; ?></th>
			</tr>
			<?php } ?>

			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				
				<?php if (@$_SESSION['view'] == 2) { ?>
				
				<tr class="rets_list <?php if ($row['featured'] == 1) echo "sfeatured"; ?>">
					<td align="left">
						<a class="fav" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>" title="<?php echo CBE1_ADD_FAVORITES; ?>"></a>
						<a class="retailer_title_s" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
					</td>
					<td align="center"><span class="cashback"><?php echo DisplayCashback($row['cashback']); ?></span></td>
					<td align="center">
					<?php
							$store_coupons_total = GetStoreCouponsTotal($row['retailer_id']);
							echo ($store_coupons_total > 0) ? "<span class='coupons'>".$store_coupons_total."</span>" : "";
					?>
					</td>
					<td align="center"><a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a></td>
				</tr>
				
				<?php }else{ ?>

				<tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td width="<?php echo IMAGE_WIDTH; ?>" align="center" valign="middle">
						<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
						<?php if ($row['featured'] == 1) { ?><span class="featured" alt="<?php echo CBE1_FEATURED_STORE; ?>" title="<?php echo CBE1_FEATURED_STORE; ?>"></span><?php } ?>
						<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></div>
						</a>
						<?php echo GetStoreRating($row['retailer_id'], $show_start = 1); ?>
					</td>
					<td align="left" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td colspan="2" width="80%" align="left" valign="middle">
									<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
								</td>
								<td nowrap="nowrap" width="20%" align="right" valign="middle">
									<a class="coupons" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons" title="<?php echo $row['title']; ?> Coupons"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?></a>
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="top" align="left">
									<div class="retailer_description"><?php echo TruncateText(stripslashes($row['description']), STORES_DESCRIPTION_LIMIT); ?>&nbsp;</div>
									<?php echo GetStoreCountries($row['retailer_id']); ?>
								</td>
								<td valign="top" align="center">
								<?php if ($row['cashback'] != "") { ?>
									<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
									<span class="cashback"><span class="value"><?php echo DisplayCashback($row['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?></span>
								<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="2" valign="middle" align="left">
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
			
			<?php } ?>
			</table>

			<?php
					$params = "";
					if (isset($cat_id) && $cat_id > 0) { $params = "cat=$cat_id&"; }
					if (isset($ltr) && $ltr != "") { $params .= "letter=$ltr&"; }

					echo ShowPagination("retailers",$results_per_page,"retailers.php?".$params."column=$rrorder&order=$rorder&show=$results_per_page&go=1&","WHERE ".$where);
			?>

	<?php }else{ ?>
		<p align="center"><?php echo CBE1_STORES_NO; ?></p>
		<div class="sline"></div>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>