<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

if (!function_exists('CategoriesDropDown')) {
function CategoriesDropDown ($parent_id, $sep = "", $current = 0, $parent = 0)
{
	$result = smart_mysql_query("SELECT name, category_id FROM cashbackengine_categories WHERE category_id<>'$current' AND parent_id='$parent_id' ORDER BY sort_order, name");
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$category_id = $row['category_id'];
			$category_name = $row['name'];
			if ($parent > 0 && $category_id == $parent) $selected = " selected=\"selected\""; else $selected = "";
			echo "<option value=\"".$category_id."\"".$selected.">".$sep.$category_name."</option>\n";
			CategoriesDropDown($category_id, $sep.$category_name." &gt; ", $current, $parent);
		}
	}
}
}


if (!function_exists('CategoriesList')) {
function CategoriesList ($parent_id, $sep = "")
{
	static $allcategories;
	$result = smart_mysql_query("SELECT name, category_id FROM cashbackengine_categories WHERE parent_id='$parent_id' ORDER BY sort_order, name");
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$category_id = $row['category_id'];
			$category_name = $row['name'];
			$allcategories[$category_id] = $sep.$category_name;
			CategoriesList($category_id, $sep.$category_name." &gt; ");
		}
	}
	return $allcategories;
}
}


if (!function_exists('CategoryTotalItems')) {
function CategoryTotalItems ($category_id)
{
	$result = smart_mysql_query("SELECT COUNT(retailer_id) as total FROM cashbackengine_retailer_to_category WHERE category_id='$category_id'");
	$row = mysql_fetch_array($result);
	return $row['total'];
}
}


/**
 * Returns total of new messages for admin
 * @return	integer		total of new messages for admin from members
*/

if (!function_exists('GetMessagesTotal')) {
	function GetMessagesTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_messages WHERE is_admin='0' AND viewed='0'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


/**
 * Returns total of members friends invitations
 * @return	string	total
*/

if (!function_exists('GetInvitationsTotal')) {
	function GetInvitationsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_invitations");
		$row = mysql_fetch_array($result);
		return $row['total'];
	}
}


/**
 * Returns total of member's requested money
 * @return	string	total
*/

if (!function_exists('GetRequestsTotal')) {
	function GetRequestsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_transactions WHERE status='request'");
		$row = mysql_fetch_array($result);
		return $row['total'];
	}
}


/**
 * Returns store reports total number
 * @return	integer		store reports total
*/

if (!function_exists('GetRetailerReportsTotal')) {
	function GetRetailerReportsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_reports WHERE retailer_id<>'0'"); // AND viewed='0'
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


if (!function_exists('GetNewCouponsTotal')) {
	function GetNewCouponsTotal()
	{
		$result = smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE viewed='0'");
		$row = mysql_fetch_array($result);
		return (int)$row['total'];
	}
}


if (!function_exists('NetworkTotalRetailers')) {
function NetworkTotalRetailers ($network_id)
{
	$result = smart_mysql_query("SELECT COUNT(retailer_id) as total FROM cashbackengine_retailers WHERE network_id='$network_id'");
	$row = mysql_fetch_array($result);
	return $row['total'];
}
}


if (!function_exists('DeleteAPI')) {
function DeleteAPI ($api_id)
{
	$api_id = (int)$api_id;
	smart_mysql_query("DELETE FROM cashbackengine_apis WHERE api_id='$api_id'");
}
}


if (!function_exists('DeleteUser')) {
function DeleteUser ($user_id)
{
	$userid = (int)$user_id;
	smart_mysql_query("DELETE FROM cashbackengine_users WHERE user_id='$userid'");
	smart_mysql_query("DELETE FROM cashbackengine_favorites WHERE user_id='$userid'");
	smart_mysql_query("DELETE FROM cashbackengine_clickhistory WHERE user_id='$userid'");
	smart_mysql_query("DELETE FROM cashbackengine_messages WHERE user_id='$userid'");
	smart_mysql_query("DELETE FROM cashbackengine_messages_answers WHERE user_id='$userid'");
	smart_mysql_query("DELETE FROM cashbackengine_transactions WHERE user_id='$userid'");
	smart_mysql_query("DELETE FROM cashbackengine_reports WHERE user_id='$userid'");
	smart_mysql_query("DELETE FROM cashbackengine_reviews WHERE user_id='$userid'");
}
}


if (!function_exists('DeleteRetailer')) {
function DeleteRetailer ($retailer_id)
{
	$rid = (int)($retailer_id);
	smart_mysql_query("DELETE FROM cashbackengine_retailers WHERE retailer_id='$rid'");
	smart_mysql_query("DELETE FROM cashbackengine_retailer_to_category WHERE retailer_id='$rid'");
	smart_mysql_query("DELETE FROM cashbackengine_clickhistory WHERE retailer_id='$rid'");
	smart_mysql_query("DELETE FROM cashbackengine_favorites WHERE retailer_id='$rid'");
	smart_mysql_query("DELETE FROM cashbackengine_reviews WHERE retailer_id='$rid'");
	smart_mysql_query("DELETE FROM cashbackengine_coupons WHERE retailer_id='$rid'");
	smart_mysql_query("DELETE FROM cashbackengine_reports WHERE retailer_id='$rid'");
}
}


if (!function_exists('DeleteCoupon')) {
function DeleteCoupon ($coupon_id)
{
	$couponid = (int)($coupon_id);
	smart_mysql_query("DELETE FROM cashbackengine_coupons WHERE coupon_id='$couponid'");
}
}


if (!function_exists('DeleteCountry')) {
function DeleteCountry ($country_id)
{
	$countryid = (int)$country_id;
	smart_mysql_query("DELETE FROM cashbackengine_countries WHERE country_id='$countryid'");
}
}


if (!function_exists('DeleteNews')) {
function DeleteNews ($news_id)
{
	$newsid = (int)$news_id;
	smart_mysql_query("DELETE FROM cashbackengine_news WHERE news_id='$newsid'");
}
}


if (!function_exists('DeleteReview')) {
function DeleteReview ($review_id)
{
	$reviewid = (int)($review_id);
	smart_mysql_query("DELETE FROM cashbackengine_reviews WHERE review_id='$reviewid'");
}
}


if (!function_exists('DeleteInvitation')) {
function DeleteInvitation ($invitation_id)
{
	$invitationid = (int)($invitation_id);
	smart_mysql_query("DELETE FROM cashbackengine_invitations WHERE invitation_id='$invitationid'");
}
}


if (!function_exists('DeleteReport')) {
function DeleteReport ($report_id)
{
	$report_id = (int)$report_id;
	smart_mysql_query("DELETE FROM cashbackengine_reports WHERE report_id='$report_id'");
}
}


if (!function_exists('DeleteMessage')) {
function DeleteMessage ($message_id)
{
	$mid = (int)$message_id;
	smart_mysql_query("DELETE FROM cashbackengine_messages WHERE message_id='$mid'");
	smart_mysql_query("DELETE FROM cashbackengine_messages_answers WHERE message_id='$mid'");
}
}


if (!function_exists('DeletePayment')) {
function DeletePayment ($payment_id)
{
	$pid = (int)$payment_id;
	smart_mysql_query("DELETE FROM cashbackengine_transactions WHERE transaction_id='$pid'");
}
}


if (!function_exists('BlockUnblockUser')) {
function BlockUnblockUser ($user_id, $unblock=0)
{
	$userid = (int)$user_id;

	if ($unblock == 1)
		smart_mysql_query("UPDATE cashbackengine_users SET status='active' WHERE user_id='$userid'");
	else
		smart_mysql_query("UPDATE cashbackengine_users SET status='inactive' WHERE user_id='$userid'");
}
}


if (!function_exists('GetETemplateTitle')) {
	function GetETemplateTitle($template_name)
	{
		switch ($template_name)
		{
			case "signup": $template_title = "Sign Up email"; break;
			case "activate": $template_title = "Registration Confirmation email"; break;
			case "activate2": $template_title = "Account Activation email"; break;
			case "forgot_password": $template_title = "Forgot Password email"; break;
			case "invite_friend": $template_title = "Invite a Friend email"; break;
			case "cashout_paid": $template_title = "Cash Out paid email"; break;
			case "cashout_declined": $template_title = "Cash Out declined email"; break;
			case "manual_credit": $template_title = "Manual Payment email"; break;
		}

		return $template_title;
	}
}


if (!function_exists('GetRetailerCategory')) {
function GetRetailerCategory($retailer_id)
{
		unset($retailer_cats);
		$retailer_cats = array();

		$sql_retailer_cats = smart_mysql_query("SELECT category_id FROM cashbackengine_retailer_to_category WHERE retailer_id='$retailer_id'");
		
		if (mysql_num_rows($sql_retailer_cats) > 0)
		{
			while ($row_retailer_cats = mysql_fetch_array($sql_retailer_cats))
			{
				$retailer_cats[] = $row_retailer_cats['category_id'];
			}

			$categories_list = array();
			$allcategories = array();
			$allcategories = CategoriesList(0);
			foreach ($allcategories as $category_id => $category_name)
			{
				if (is_array($retailer_cats) && in_array($category_id, $retailer_cats))
				{
					$categories_list[] = $category_name;
				}
			}
			
			foreach ($categories_list as $cat_name)
			{
				echo "&raquo; ".$cat_name."<br/>";
			}
		}
		else
		{
			echo "----";
		}
}
}


if (!function_exists('GetRetailerCountry')) {
function GetRetailerCountry($retailer_id)
{
		unset($retailer_countries);
		$retailer_countries = array();

		$sql_retailer_countries = smart_mysql_query("SELECT country_id FROM cashbackengine_retailer_to_country WHERE retailer_id='$retailer_id'");		
					
		while ($row_retailer_countries = mysql_fetch_array($sql_retailer_countries))
		{
			$retailer_countries[] = $row_retailer_countries['country_id'];
		}
		
		foreach ($categories_list as $cat_name)
		{
			echo "&raquo; ".$cat_name."<br/>";
		}
}
}


if (!function_exists('GetNetworkName')) {
function GetNetworkName($network_id)
{
	$sql = "SELECT network_name FROM cashbackengine_affnetworks WHERE network_id='$network_id' LIMIT 1";
	$result = smart_mysql_query($sql);
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		return $row['network_name'];
	}
	else
	{
		//return "---";
	}
}
}


if (!function_exists('GetVisitsTotal')) {
function GetVisitsTotal($retailer_id)
{
	$sql = "SELECT visits FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' LIMIT 1";
	$result = smart_mysql_query($sql);
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		return $row['visits'];
	}
}
}


if (!function_exists('GetMembersVisitsTotal')) {
function GetMembersVisitsTotal($retailer_id)
{
	$sql = "SELECT COUNT(*) AS total_visits FROM cashbackengine_clickhistory WHERE retailer_id='$retailer_id'";
	$result = smart_mysql_query($sql);
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		return $row['total_visits'];
	}
}
}


if (!function_exists('GetGuestsVisitsTotal')) {
function GetGuestsVisitsTotal($retailer_id)
{
	$total_visits = GetVisitsTotal($retailer_id);
	$members_visits = GetMembersVisitsTotal($retailer_id);
	$guest_visits = $total_visits - $members_visits;
	return $guest_visits;
}
}


?>