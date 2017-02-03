<?php

/*******************************************************************\

 * CashbackEngine v3.0

 * http://www.CashbackEngine.net

 *

 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.

 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------

\*******************************************************************/



	$setts_sql = "SELECT * FROM cashbackengine_settings";

	$setts_result = smart_mysql_query($setts_sql);



	unset($settings);

	$settings = array();



	while ($setts_row = mysql_fetch_array($setts_result))

	{

		$settings[$setts_row['setting_key']] = $setts_row['setting_value'];

	}



	define('SITE_TITLE', $settings['website_title']);

	define('SITE_MAIL', $settings['website_email']);

	define('EMAIL_FROM_NAME', $settings['email_from_name']);

	define('NOREPLY_MAIL', $settings['noreply_email']);

	define('SITE_ALERTS_MAIL', $settings['alerts_email']);

	define('SITE_URL', $settings['website_url']);

	define('SITE_MODE', $settings['website_mode']);

	define('SITE_HOME_TITLE', $settings['website_home_title']);

	define('SITE_LANGUAGE', $settings['website_language']);

	define('MULTILINGUAL', $settings['multilingual']);

	define('SITE_TIMEZONE', $settings['website_timezone']);

	define('DATE_FORMAT', $settings['website_date_format']);

	define('SITE_CURRENCY', $settings['website_currency']);

	define('SITE_CURRENCY_FORMAT', $settings['website_currency_format']);

	define('SIGNUP_CAPTCHA', $settings['signup_captcha']);

	define('ACCOUNT_ACTIVATION', $settings['account_activation']);

	define('LOGIN_ATTEMPTS_LIMIT', $settings['login_attempts_limit']);

	define('LOGIN_ATTEMPTS', 5);

	define('STORES_LIST_STYLE', $settings['stores_list_style']);

	define('SHARE_ICONS_STYLE', $settings['share_icons_style']);

	define('STORES_DESCRIPTION_LIMIT', $settings['stores_description_limit']);

	define('COUPONS_DESCRIPTION_LIMIT', $settings['coupons_description_limit']);

	define('ONE_REVIEW', $settings['one_review']);

	define('HOMEPAGE_REVIEWS_LIMIT', $settings['homepage_reviews_limit']);

	define('TODAYS_COUPONS_LIMIT', $settings['todays_coupons_limit']);

	define('FEATURED_STORES_LIMIT', $settings['featured_stores_limit']);

	define('POPULAR_STORES_LIMIT', $settings['popular_stores_limit']);

	define('NEW_STORES_LIMIT', $settings['new_stores_limit']);

	define('RESULTS_PER_PAGE', $settings['results_per_page']);

	define('COUPONS_PER_PAGE', $settings['coupons_per_page']);

	define('SUBMIT_COUPONS', $settings['submit_coupons']);

	define('MEMBERS_SUBMIT_COUPONS', $settings['members_submit_coupons']);

	define('HIDE_COUPONS', $settings['hide_coupons']);

	define('MIN_PAYOUT_PER_TRANSACTION', $settings['min_transaction']);

	define('MIN_PAYOUT', $settings['min_payout']);

	define('CANCEL_WITHDRAWAL', $settings['cancel_withdrawal']);

	define('SIGNUP_BONUS', $settings['signup_credit']);

	define('REFER_FRIEND_BONUS', $settings['refer_credit']);

	define('CASHBACK_COMMISSION', $settings['cashback_commission']);

	define('REFERRAL_COMMISSION', $settings['referral_commission']);

	define('IMAGE_WIDTH', $settings['image_width']);

	define('IMAGE_HEIGHT', $settings['image_height']);

	define('SHOW_LANDING_PAGE', $settings['show_landing_page']);

	define('REVIEWS_APPROVE', $settings['reviews_approve']);

	define('MAX_REVIEW_LENGTH', $settings['max_review_length']);

	define('REVIEWS_PER_PAGE', $settings['reviews_per_page']);

	define('NEWS_PER_PAGE', $settings['news_per_page']);

	define('SHOW_CASHBACK_CALCULATOR', $settings['show_cashback_calculator']);

	define('SHOW_RETAILER_STATS', $settings['show_statistics']);

	define('SHOW_SITE_STATS', $settings['show_site_statistics']);

	define('NEW_COUPON_ALERT', $settings['email_new_coupon']);

	define('NEW_REVIEW_ALERT', $settings['email_new_review']);

	define('NEW_TICKET_ALERT', $settings['email_new_ticket']);

	define('NEW_TICKET_REPLY_ALERT', $settings['email_new_ticket_reply']);

	define('NEW_REPORT_ALERT', $settings['email_new_report']);

	define('SMTP_MAIL', $settings['smtp_mail']);

	define('SMTP_PORT', $settings['smtp_port']);

	define('SMTP_HOST', $settings['smtp_host']);

	define('SMTP_USERNAME', $settings['smtp_username']);

	define('SMTP_PASSWORD', $settings['smtp_password']);

	define('SMTP_SSL', $settings['smtp_ssl']);

	define('FACEBOOK_CONNECT', $settings['facebook_connect']);

	define('FACEBOOK_APPID', $settings['facebook_appid']);

	define('FACEBOOK_SECRET', $settings['facebook_secret']);

	define('FACEBOOK_PAGE', $settings['facebook_page']);

	define('SHOW_FB_LIKEBOX', $settings['show_fb_likebox']);

	define('TWITTER_PAGE', $settings['twitter_page']);

	define('REG_SOURCES', $settings['reg_sources']);

	define('ADDTHIS_ID', $settings['addthis_id']);

	define('GOOGLE_ANALYTICS', stripslashes($settings['google_analytics']));

	define('TIMENOW', time());

	define('HIDE_SUB_CATEGORIES', 1);

	define('ALLOW_API', 0);



	if (REG_SOURCES != "" && strstr(REG_SOURCES, ',')) $reg_sources = explode(",",REG_SOURCES);



	// letters for alphabetical order 

	$alphabet = array ("0-9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");



	// results per page dropdown

	$results_on_page = array("5", "10", "25", "50", "100", "111111");



	// site languages

	$languages = array();

	$languages_sql = "SELECT * FROM cashbackengine_languages WHERE status='active' ORDER BY sort_order, language";

	$languages_result = smart_mysql_query($languages_sql);

	if (mysql_num_rows($languages_result) > 0)

	{

		while ($languages_row = mysql_fetch_array($languages_result))

		{

			$language_code = $languages_row['language_code'];

			$language_name = $languages_row['language'];

			$languages[$language_code] = $language_name;

		}

	}



?>