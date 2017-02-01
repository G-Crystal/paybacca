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

	$userid	= (int)$_SESSION['userid'];


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$retailer_id	= (int)$_GET['id'];
		$click_ip		= mysql_real_escape_string(getenv("REMOTE_ADDR"));

		if (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] > 0) $coupon_id = (int)$_GET['c'];

		$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' LIMIT 1";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);

			if (SHOW_LANDING_PAGE == 1)
			{
				// show landing page
				if ($coupon_id)
					$goto = "redirect.php?id=".$retailer_id."&c=".$coupon_id;
				else
					$goto = "redirect.php?id=".$retailer_id;
			}
			else
			{
				$goto = str_replace("{USERID}", $userid, $row['url']);
			}

			if ($coupon_id)
			{
				$coupon_result = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE coupon_id='$coupon_id' LIMIT 1");
				if (mysql_num_rows($coupon_result) > 0)
				{
					$coupon_row = mysql_fetch_array($coupon_result);
					$coupon_link = $coupon_row['link'];

					if ($coupon_link != "")
					{
						$goto = str_replace("{USERID}", $userid, $coupon_link);
					}
				}
			}
			
			if (!isLoggedIn())
			{
				$_SESSION['goto']			= $goto;
				$_SESSION['goto_created']	= time();
				$_SESSION['goRetailerID']	= $retailer_id;
				$_SESSION['goCouponID']		= $coupon_id;

				header("Location: login.php?msg=4");
				exit();
			}	

			// update retailer visits
			smart_mysql_query("UPDATE cashbackengine_retailers SET visits=visits+1 WHERE retailer_id='$retailer_id' LIMIT 1");

			// update coupon visits
			if (isset($coupon_id) && is_numeric($coupon_id))
			{
				smart_mysql_query("UPDATE cashbackengine_coupons SET visits=visits+1, last_visit=NOW() WHERE coupon_id='$coupon_id' LIMIT 1");
				smart_mysql_query("UPDATE cashbackengine_coupons SET visits_today=visits_today+1 WHERE coupon_id='$coupon_id' AND DATE(last_visit)=DATE(NOW()) LIMIT 1");
			}

			// save click info //
			$click_ref = GenerateRandString(10, "0123456789");
			smart_mysql_query("INSERT INTO cashbackengine_clickhistory SET click_ref='$click_ref', user_id='$userid', retailer_id='$retailer_id', retailer='".mysql_real_escape_string($row['title'])."', click_ip='$click_ip', added=NOW()");

			if ($goto != "")
			{
				// redirect user
				header("Location: ".$goto);
				exit();
			}
		}
		else
		{
			// store not found
        	$PAGE_TITLE = CBE1_STORE_NOT_FOUND;
			
			require_once ("inc/header.inc.php");
			echo "<h1>".CBE1_STORE_NOT_FOUND."</h1>";
			echo "<p align='center'>".CBE1_STORE_NOT_FOUND2."<br/><br/><a class='goback' href='".SITE_URL."retailers.php'>".CBE1_GO_BACK."</a></p>";
			require_once ("inc/footer.inc.php");
		}
	}
	else
	{	
		header("Location: index.php");
		exit();
	}

?>