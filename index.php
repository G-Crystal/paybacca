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
				echo $content['text'];
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
				<div class="imagebox"><a href="<?php echo GetRetailerLink($row_featured['retailer_id'], $row_featured['title']); ?>"><img src="<?php if (!stristr($row_featured['image'], 'http')) echo SITE_URL."img/"; echo $row_featured['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_featured['title']; ?>" title="<?php echo $row_featured['title']; ?>" border="0" /></a></div>
			<?php } ?>
			</div>
		<?php
				}
			} // end featured retailers 
		?>


		<?php
			if (TODAYS_COUPONS_LIMIT > 0)
			{
				// show today's top coupons //
				$result_todays_coupons = smart_mysql_query("SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND DATE(c.last_visit)=DATE(NOW()) AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY visits_today DESC LIMIT ".TODAYS_COUPONS_LIMIT);
				$total_todays_coupons = mysql_num_rows($result_todays_coupons);

				if ($total_todays_coupons > 0) { 
		?>
			<div style="clear: both;"></div>
			<h3 class="brd"><?php echo CBE1_HOME_TOP_COUPONS; ?></h3>
			<table align="center" width="100%" border="0" cellspacing="0" cellpadding="5">
			<?php while ($row_todays_coupons = mysql_fetch_array($result_todays_coupons)) { ?>
				<tr>
					<td class="td_coupon" width="<?php echo IMAGE_WIDTH; ?>" align="center" valign="top">
						<div class="imagebox"><a href="<?php echo GetRetailerLink($row_todays_coupons['retailer_id'], $row_todays_coupons['title']); ?>"><img src="<?php if (!stristr($row_todays_coupons['image'], 'http')) echo SITE_URL."img/"; echo $row_todays_coupons['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row_todays_coupons['title']; ?>" title="<?php echo $row_todays_coupons['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($row_todays_coupons['retailer_id'], $row_todays_coupons['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</td>
					<td width="80%" class="td_coupon" align="left" valign="top">
						<span class="coupon_name"><?php echo $row_todays_coupons['title']; ?> <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_todays_coupons['retailer_id']; ?>&c=<?php echo $row_todays_coupons['coupon_id']; ?>" target="_blank"><?php echo $row_todays_coupons['coupon_title']; ?></b></span>
						<?php echo ($row_todays_coupons['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$row_todays_coupons['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
						<br/>
						<?php if ($row_todays_coupons['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($row_todays_coupons['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
						<?php if ($row_todays_coupons['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row_todays_coupons['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row_todays_coupons['time_left']); ?></span>
						<?php } ?>
					</td>
					<td class="td_coupon" align="left" valign="bottom">
						<?php if ($row_todays_coupons['code'] != "") { ?><span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $row_todays_coupons['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row_todays_coupons['retailer_id']; ?>&c=<?php echo $row_todays_coupons['coupon_id']; ?>" target="_blank"><?php echo ($row_todays_coupons['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					</td>
				</tr>
			<?php } ?>
			</table>
		<?php
				}
			} // end today's top coupons
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