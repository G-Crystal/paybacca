<?php

/*******************************************************************\

 * CashbackEngine v3.0

 * http://www.CashbackEngine.net

 *

 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.

 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------

\*******************************************************************/



	if (file_exists("./install.php"))

	{

		header ("Location: install.php");

		exit();

	}



	session_start();

	require_once("inc/config.inc.php");



	// save referral id //////////////////////////////////////////////

	if (isset($_GET['ref']) && is_numeric($_GET['ref']))

	{

		$ref_id = (int)$_GET['ref'];

		setReferral($ref_id);



		// count ref link clicks

		if (!isLoggedIn())

		{

			smart_mysql_query("UPDATE cashbackengine_users SET ref_clicks=ref_clicks+1 WHERE user_id='$ref_id' LIMIT 1");

		}



		header("Location: index.php");

		exit();

	}



	// set language ///////////////////////////////////////////////////

	if (isset($_GET['lang']) && $_GET['lang'] != "")

	{

		$site_lang	= strtolower(getGetParameter('lang'));

		$site_lang	= preg_replace("/[^0-9a-zA-Z]/", " ", $site_lang);

		$site_lang	= substr(trim($site_lang), 0, 30);

		

		if ($site_lang != "")

		{

			setcookie("site_lang", $site_lang, time()+3600*24*365, '/');

		}



		header("Location: index.php");

		exit();

	}



	$content = GetContent('home');



	///////////////  Page config  ///////////////

	$PAGE_TITLE			= SITE_HOME_TITLE;

	$PAGE_DESCRIPTION	= $content['meta_description'];

	$PAGE_KEYWORDS		= $content['meta_keywords'];



	require_once("inc/header.inc.php");



?>

		<?php

			// hide welcome text from registered users

			//if (!isLoggedIn())

				// echo $content['text'];

		?>



		<?php



			if (FEATURED_STORES_LIMIT > 0)

			{

				// show featured retailers //

				$result_featured = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE featured='1' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY RAND() LIMIT ".FEATURED_STORES_LIMIT);

				$total_featured = mysql_num_rows($result_featured);



				if ($total_featured > 0) { 

		?>

			<div style="clear: both;"></div>

			<h3 class="brd"><?php echo CBE1_HOME_FEATURED_STORES; ?></h3>

			<div class="featured_stores">

			<?php while ($row_featured = mysql_fetch_array($result_featured)) { ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
					<div class="store-box-div">
						<div class="">
							<a class="retailer_title" href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>"><?php echo $row_featured['title']; ?></a>
						</div>
						<div class="store-icon-div">
							<a href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>">
								<div class="imagebox"><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" class="store-icon-img" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></div>
							</a>
						</div>
						<?php if ($row_featured['cashback'] != "") { ?>
							<div class="cashback">
								<?php if ($row_featured['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($row_featured['old_cashback']); ?></span><?php } ?>
								<span class="value"><?php echo DisplayCashback($row_featured['cashback']); ?></span><span class="value">Cash Back</span>
							</div>
						<?php } else { ?>
							<div class="cashback">
								<a class="common-btn" href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>">See More</a>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

			</div>

			<?php if (NEW_STORES_LIMIT > 0) { ?>
			<div style="clear: both;"></div>

			<h3 class="brd"><?php echo CBE1_BOX_NEW; ?></h3>

			<?php
				$n_query = "SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY added DESC LIMIT ".NEW_STORES_LIMIT;
				$n_result = smart_mysql_query($n_query);
				$n_total = mysql_num_rows($n_result);

				if ($n_total > 0)
				{
			?>

			<div class="new_stores">

			<?php while ($n_row = mysql_fetch_array($n_result)) { ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
					<div class="store-box-div">
						<div class="">
							<a class="retailer_title" href="<?php echo GetRetailerLink($n_row['retailer_id'], $n_row['title']); ?>"><?php echo $n_row['title']; ?></a>
						</div>
						<div class="store-icon-div">
							<a href="<?php echo GetRetailerLink($n_row['retailer_id'], $n_row['title']); ?>">
								<div class="imagebox"><img src="<?php if (!stristr($n_row['image'], 'http')) echo SITE_URL."img/"; echo $n_row['image']; ?>" class="store-icon-img" alt="<?php echo $n_row['title']; ?>" title="<?php echo $n_row['title']; ?>" border="0" /></div>
							</a>
						</div>
						<?php if ($n_row['cashback'] != "") { ?>
							<div class="cashback">
								<?php if ($n_row['old_cashback'] != "") { ?><span class="old_cashback"><?php echo DisplayCashback($n_row['old_cashback']); ?></span><?php } ?>
								<span class="value"><?php echo DisplayCashback($n_row['cashback']); ?></span><span class="value">Cash Back</span>
							</div>
						<?php } else { ?>
							<div class="cashback">
								<a class="common-btn" href="<?php echo GetRetailerLink($n_row['retailer_id'], $n_row['title']); ?>">See More</a>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php } ?>
			<?php } ?>
			
			</div>


		<?php

				}

			} // end featured retailers 

		?>



		<?php



			if (HOMEPAGE_REVIEWS_LIMIT > 0)

			{

				// Show recent reviews //

				$reviews_query = "SELECT r.*, DATE_FORMAT(r.added, '".DATE_FORMAT."') AS review_date, u.user_id, u.username, u.fname, u.lname FROM cashbackengine_reviews r LEFT JOIN cashbackengine_users u ON r.user_id=u.user_id WHERE r.status='active' ORDER BY r.added DESC LIMIT ".HOMEPAGE_REVIEWS_LIMIT;

				$reviews_result = smart_mysql_query($reviews_query);

				$reviews_total = mysql_num_rows($reviews_result);



				if ($reviews_total > 0) {

		?>

			<div style="clear: both"></div>

			<h3 class="brd"><?php echo CBE1_HOME_RECENT_REVIEWS; ?></h3>

			<?php while ($reviews_row = mysql_fetch_array($reviews_result)) { ?>

            <div id="review">

                <span class="review-author"><?php echo $reviews_row['fname']." ".substr($reviews_row['lname'], 0, 1)."."; ?></span>

				<span class="review-date"><?php echo $reviews_row['review_date']; ?></span><br/><br/>

				<b><a href="<?php echo GetRetailerLink($reviews_row['retailer_id'], GetStoreName($reviews_row['retailer_id'])); ?>"><?php echo GetStoreName($reviews_row['retailer_id']); ?></a></b><br/>

				<img src="<?php echo SITE_URL; ?>images/icons/rating-<?php echo $reviews_row['rating']; ?>.gif" />&nbsp;

				<span class="review-title"><?php echo $reviews_row['review_title']; ?></span><br/>

				<div class="review-text"><?php echo $reviews_row['review']; ?></div>

                <div style="clear: both;"></div>

            </div>

			<?php } ?>

			<div style="clear: both"></div>

		<?php

				}

			}

		?>



<?php require_once("inc/footer.inc.php"); ?>