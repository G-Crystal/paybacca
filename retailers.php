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

	

	if (isset($cat_id))

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


	<?php if ($cat_row['description'] != "") { ?>

		<p class="category_description"><?php echo $cat_row['description']; ?></p>

	<?php } ?>


	<?php

		if ($total > 0) {

	?>

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

				</form>

			</div>

		</div>

		<div id="retailers">
			<?php if (@$_SESSION['view'] == 2) { ?>
			<div class="row">
				<div class="col-sm-6 col-xs-12"><?php echo CBE1_STORES_NAME; ?></div>
				<div class="col-sm-2 col-xs-4"><?php echo CBE1_CASHBACK2; ?></div>
				<div class="col-sm-2 col-xs-4"><?php echo CBE1_STORES_COUPONS; ?></div>
				<div class="col-sm-2 col-xs-4"><?php echo CBE1_STORES_VISIT; ?></div>
			</div>
			<?php } ?>

			<?php $cc = 0; ?>

			<?php if (@$_SESSION['view'] == 2) { ?>
				<div class="row">
				<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
					<div class="col-sm-6 col-xs-12">
						<a class="fav" href="<?php echo SITE_URL; ?>myfavorites.php?act=add&id=<?php echo $row['retailer_id']; ?>" title="<?php echo CBE1_ADD_FAVORITES; ?>"></a>
						<a class="retailer_title_s" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
					</div>
					<div class="col-sm-2 col-xs-4">
						<span class="cashback"><?php echo DisplayCashback($row['cashback']); ?></span>
					</div>
					<div class="col-sm-2 col-xs-4">
					<?php
						$store_coupons_total = GetStoreCouponsTotal($row['retailer_id']);
						echo ($store_coupons_total > 0) ? "<span class='coupons'>".$store_coupons_total."</span>" : "";
					?>
					</div>
					<div class="col-sm-2 col-xs-4">
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>" target="_blank"><?php echo CBE1_GO_TO_STORE; ?></a>
					</div>
				<?php } ?>
				</div>
			<?php }else{ ?>
				<div class="row">
				<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
					<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
						<div class="store-box-div">
							<div class="">
								<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
							</div>
							<div class="store-icon-div">
								<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
								<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></div>
								</a>
							</div>
							<?php if ($row['cashback'] != "") { ?>
								<div class="cashback">
									<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
									<span class="value"><?php echo DisplayCashback($row['cashback']); ?></span> <?php echo CBE1_CASHBACK; ?>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				</div>
			<?php } ?>
		</div>

		<p align="center"><?php echo CBE1_STORES_NO; ?></p>

		<div class="sline"></div>

	<?php } ?>



<?php require_once ("inc/footer.inc.php"); ?>