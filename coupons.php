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



	$results_per_page = COUPONS_PER_PAGE;

	$cc = 0;





	////////////////// filter  //////////////////////

		if (isset($_GET['column']) && $_GET['column'] != "")

		{

			switch ($_GET['column'])

			{

				case "added": $rrorder = "c.added"; break;

				case "visits": $rrorder = "c.visits"; break;

				case "retailer_id": $rrorder = "c.retailer_id"; break;

				case "end_date": $rrorder = "c.end_date"; break;

				default: $rrorder = "c.added"; break;

			}

		}

		else

		{

			$rrorder = "c.added";

		}



		if (isset($_GET['order']) && $_GET['order'] != "")

		{

			switch ($_GET['order'])

			{

				case "asc": $rorder = "asc"; break;

				case "desc": $rorder = "desc"; break;

				default: $rorder = "desc"; break;

			}

		}

		else

		{

			$rorder = "desc";

		}

	//////////////////////////////////////////////////



	$exclusive_coupons_total = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE exclusive='1' AND status='active'"));

	$exclusive_coupons_total = $exclusive_coupons_total['total'];



	$expiring_coupons_total = mysql_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE end_date!='0000-00-00 00:00:00' AND (end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)) AND status='active'"));

	$expiring_coupons_total = $expiring_coupons_total['total'];





	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }

	$from = ($page-1)*$results_per_page;



	$where = " (start_date<=NOW() AND (end_date='0000-00-00 00:00:00' OR end_date > NOW())) AND status='active'";



	$query = "SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";

	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE $where ORDER BY title ASC");

	$total = mysql_num_rows($total_result);



	$result = smart_mysql_query($query);

	$total_on_page = mysql_num_rows($result);



	///////////////  Page config  ///////////////

	$PAGE_TITLE = CBE1_COUPONS_TITLE;



	require_once ("inc/header.inc.php");



?>



	<h3 class="brd"><?php echo CBE1_COUPONS_TITLE; ?></h3>

		<div id="exclusive" class="tab_content">

		<?php

				$cc = 0;

				// show exclusive coupons //

				$ex_query = "SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.exclusive='1' AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.added DESC LIMIT $results_per_page";

				$ex_result = smart_mysql_query($ex_query);

				$ex_total = mysql_num_rows($ex_result);



				if ($ex_total > 0)

				{

			?>

			<?php while ($ex_row = mysql_fetch_array($ex_result)) { $cc++; ?>
			<div class="row">
				<div class="col-md-2 col-sm-3 col-xs-12 text-center">
					<?php if ($ex_row['exclusive'] == 1) { ?><div class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></div><?php } ?>

					<div class="imagebox"><a href="<?php echo GetRetailerLink($ex_row['retailer_id'], $ex_row['title']); ?>"><img src="<?php if (!stristr($ex_row['image'], 'http')) echo SITE_URL."img/"; echo $ex_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $ex_row['title']; ?>" title="<?php echo $ex_row['title']; ?>" border="0" /></a></div>
				</div>

				<div class="col-md-8 col-sm-6 col-xs-12">
					<span class="coupon_name"><?php echo $ex_row['title']; ?> <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $ex_row['retailer_id']; ?>&c=<?php echo $ex_row['coupon_id']; ?>" target="_blank"><?php echo $ex_row['coupon_title']; ?></a></span>
					<?php echo ($ex_row['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$ex_row['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
					<br/>
					<?php if ($ex_row['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($ex_row['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
					<?php if ($ex_row['end_date'] != "0000-00-00 00:00:00") { ?>
						<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $ex_row['coupon_end_date']; ?></span> &nbsp; 
						<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($ex_row['time_left']); ?></span>
					<?php } ?>
				</div>

				<div class="col-md-2 col-sm-3 col-xs-12 text-right">
					<?php if ($ex_row['code'] != "") { ?><span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $ex_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><?php } ?>
					<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $ex_row['retailer_id']; ?>&c=<?php echo $ex_row['coupon_id']; ?>" target="_blank"><?php echo ($ex_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
				</div>
			</div>
			<?php } ?>


			<?php }else{ ?>

				<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>

				<div class="sline"></div>

			<?php } ?>

		</div>



<?php require_once ("inc/footer.inc.php"); ?>