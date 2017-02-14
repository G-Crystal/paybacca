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



	$ADDTHIS_SHARE = 1;



	if (isset($_GET['id']) && is_numeric($_GET['id']))

	{

		$retailer_id = (int)$_GET['id'];

	}

	else

	{		

		header ("Location: index.php");

		exit();

	}



	$query = "SELECT *, DATE_FORMAT(added, '".DATE_FORMAT."') AS date_added FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1";

	$result = smart_mysql_query($query);

	$total = mysql_num_rows($result);



	if ($total > 0)

	{

		$row = mysql_fetch_array($result);

		

		$retailer_id	= $row['retailer_id'];

		$cashback		= DisplayCashback($row['cashback']);

		$retailer_url	= GetRetailerLink($row['retailer_id'], $row['title']);

		if (isLoggedIn()) $retailer_url .= "&ref=".(int)$_SESSION['userid'];



		// save referral //

		if (!isLoggedIn() && isset($_GET['ref']) && is_numeric($_GET['ref']))

		{

			$ref_id = (int)$_GET['ref'];

			setReferral($ref_id);

		}



		if ($row['seo_title'] != "")

		{

			$ptitle	= $row['seo_title'];

		}

		else

		{

			if ($cashback != "") 

				$ptitle	= $row['title'].". ".CBE1_STORE_EARN." ".$cashback." ".CBE1_CASHBACK2;

			else

				$ptitle	= $row['title'];

		}		



		//// ADD REVIEW ///////////////////////////////////////////////////////////////////////

		if (isset($_POST['action']) && $_POST['action'] == "add_review" && isLoggedIn())

		{

			$userid			= (int)$_SESSION['userid'];

			$retailer_id	= (int)getPostParameter('retailer_id');

			$rating			= (int)getPostParameter('rating');

			$review_title	= mysql_real_escape_string(getPostParameter('review_title'));

			$review			= mysql_real_escape_string(nl2br(trim(getPostParameter('review'))));

			$review			= ucfirst(strtolower($review));



			unset($errs);

			$errs = array();



			if (!($userid && $retailer_id && $rating && $review_title && $review))

			{

				$errs[] = CBE1_REVIEW_ERR;

			}

			else

			{

				$number_lines = count(explode("<br />", $review));

				

				if (strlen($review) > MAX_REVIEW_LENGTH)

					$errs[] = str_replace("%length%",MAX_REVIEW_LENGTH,CBE1_REVIEW_ERR2);

				else if ($number_lines > 5)

					$errs[] = CBE1_REVIEW_ERR3;

				else if (stristr($review, 'http'))

					$errs[] = CBE1_REVIEW_ERR4;

			}



			if (count($errs) == 0)

			{

				$review = substr($review, 0, MAX_REVIEW_LENGTH);

				

				if (ONE_REVIEW == 1)

					$check_review = mysql_num_rows(smart_mysql_query("SELECT * FROM cashbackengine_reviews WHERE retailer_id='$retailer_id' AND user_id='$userid'"));

				else

					$check_review = 0;



				if ($check_review == 0)

				{

					(REVIEWS_APPROVE == 1) ? $status = "pending" : $status = "active";

					$review_query = "INSERT INTO cashbackengine_reviews SET retailer_id='$retailer_id', rating='$rating', user_id='$userid', review_title='$review_title', review='$review', status='$status', added=NOW()";

					$review_result = smart_mysql_query($review_query);

					$review_added = 1;



					// send email notification //

					if (NEW_REVIEW_ALERT == 1) 

					{

						SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT2, CBE1_EMAIL_ALERT2_MSG);

					}

					/////////////////////////////

				}

				else

				{

					$errormsg = CBE1_REVIEW_ERR5;

				}



				unset($_POST['review']);

			}

			else

			{

				$errormsg = "";

				foreach ($errs as $errorname)

					$errormsg .= "&#155; ".$errorname."<br/>";

			}

		}

		//////////////////////////////////////////////////////////////////////////////////////////

	}

	else

	{

		$ptitle = CBE1_STORE_NOT_FOUND;

	}





	///////////////  Page config  ///////////////

	$PAGE_TITLE			= $ptitle;

	$PAGE_DESCRIPTION	= $row['meta_description'];

	$PAGE_KEYWORDS		= $row['meta_keywords'];



	require_once ("inc/header.inc.php");



?>	



	<?php

		if ($total > 0) {

	?>

			<h3 class="brd"><?php echo $ptitle; ?></h3>

			<div class="row">
				<div class="col-xs-8 col-xs-offset-2">
					<div class="text-center">
						<div class="">
							<a class="retailer_title" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><?php echo $row['title']; ?></a>
						</div>
						<div class="store-icon-div">
							<a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>">
								<div class="imagebox"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" class="store-icon-img" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></div>
							</a>
						</div>
						<div class="cashback">
							<?php if ($row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row['old_cashback']); ?></span><?php } ?>
							<span class="value"><?php echo DisplayCashback($row['cashback']); ?></span>
							<span class="value">Cash Back</span>
						</div>
						<div>
							<a class="coupons" href="#coupons"><?php echo GetStoreCouponsTotal($row['retailer_id']); ?> <?php echo CBE1_STORE_COUPONS1; ?></a><br/><br/>
						</div>
						<div class="info_links" style="width: 100%; padding-top: 15px;">
							<p align="center">
							<a class="common-btn" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>">Get Cash Back</a>
							</p>
						</div>
					</div>
				</div>
			</div>

		<?php
			// start store coupons //
			$ee = 0;

			$query_coupons = "SELECT *, DATE_FORMAT(end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(end_date) - UNIX_TIMESTAMP() AS time_left FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND (start_date<=NOW() AND (end_date='0000-00-00 00:00:00' OR end_date > NOW())) AND status='active' ORDER BY sort_order, added DESC";

			$result_coupons = smart_mysql_query($query_coupons);
			$total_coupons = mysql_num_rows($result_coupons);

			if ($total_coupons > 0)
			{
		?>

			<a name="coupons"></a>

			<h3 class="store_coupons"><?php echo $row['title']; ?> <?php echo CBE1_STORE_COUPONS; ?></h3>

			<div class="coupons-list row">
			<?php while ($row_coupons = mysql_fetch_array($result_coupons)) { $ee++; ?>
				<div class="coupon col-xs-12">
					<div class="scissors"></div>
					<div class="col-xs-12">
						<a class="coupon_title" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><?php echo $row_coupons['title']; ?></a>
						<?php if ($row_coupons['exclusive'] == 1) { ?> <sup><span class="exclusive_s"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span></sup><?php } ?>
					</div>
					<?php echo ($row_coupons['visits'] > 0) ? "<div class='coupon_times_used'><sup>".$row_coupons['visits']." ".CBE1_COUPONS_TUSED."</sup></div>" : ""; ?>
					<?php if ($row_coupons['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($row_coupons['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
					<?php if ($row_coupons['code'] != "") { ?>
					<div class="col-xs-12">
						<span class="coupon_note"><?php echo CBE1_COUPONS_MSG; ?></span><br/><br/>
					</div>
					<div class="col-xs-12 text-right">
						<span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $row_coupons['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span>
					</div>
					<?php } ?>
					<?php if ($row_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
					<div class="col-xs-12">
						<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row_coupons['coupon_end_date']; ?></span> &nbsp; 
						<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row_coupons['time_left']); ?></span>
					</div>
					<div class="col-xs-12 text-center">
						<a class="common-btn" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row_coupons['coupon_id']; ?>" target="_blank"><?php echo ($row_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
			</div>

			<div style="clear: both"></div>

		<?php } // end store coupons // ?>

			<div class="row">
				<div class="col-xs-12 text-center">
					<div class="">
						<div class="retailer_description"><?php echo TruncateText(stripslashes($row['description']), STORES_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div>
						<?php echo GetStoreCountries($row['retailer_id']); ?>
						<?php if ($row['tags'] != "") { ?><p><span class="tags"><?php echo $row['tags']; ?></span></p><?php } ?>
						<?php if ($row['conditions'] != "") { ?>
							<p><b><?php echo CBE1_CONDITIONS; ?></b><br/>
							<span class="conditions_desc"><?php echo $row['conditions']; ?></span>
							</p>
						<?php } ?>
					</div>					
				</div>
			</div>


		<?php
			// start expired coupons //
			$ee = 0;

			$query_exp_coupons = "SELECT *, DATE_FORMAT(end_date, '".DATE_FORMAT."') AS coupon_end_date FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' AND end_date != '0000-00-00 00:00:00' AND end_date < NOW() AND status!='inactive' ORDER BY sort_order, added DESC";

			$result_exp_coupons = smart_mysql_query($query_exp_coupons);
			$total_exp_coupons = mysql_num_rows($result_exp_coupons);

			if ($total_exp_coupons > 0)
			{
		?>

			<h3 class="store_exp_coupons"><?php echo CBE1_STORE_ECOUPONS; ?></h3>

			<div class="coupons-list row">
				<?php while ($row_exp_coupons = mysql_fetch_array($result_exp_coupons)) { $ee++; ?>
				<div class="coupon col-xs-12" style="opacity: 0.5; filter: alpha(opacity=50);">
					<span class="scissors"></span>
					<b><?php echo $row_exp_coupons['title']; ?></b>
					<?php echo ($row_exp_coupons['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$row_exp_coupons['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
					<?php if ($row_exp_coupons['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($row_exp_coupons['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
					<?php if ($row_exp_coupons['code'] != "") { ?><span class="coupon_code" style="background: #EEE; border-color: #EEE"><?php echo $row_exp_coupons['code']; ?></span><?php } ?>
					<span class="expires"><?php echo CBE1_COUPONS_ENDED; ?>: <?php echo $row_exp_coupons['coupon_end_date']; ?></span>
				</div>
				<?php } ?>
			</div>

			<div style="clear: both"></div>

		<?php } // end expired coupons // ?>

	<?php }else{ ?>

		<h1><?php echo $ptitle; ?></h1>

		<p align="center"><?php echo CBE1_STORE_NOT_FOUND2; ?></p>

		<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;"><?php echo CBE1_GO_BACK; ?></a></p>

	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>