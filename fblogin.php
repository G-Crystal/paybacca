<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/iflogged.inc.php");
	require_once("inc/config.inc.php");

	if (!(FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != ""))
	{
		header ("Location: index.php");
		exit();
	}

	require_once("inc/facebook/facebook.php");

	$facebook = new Facebook(array(
		'appId'  => FACEBOOK_APPID,
		'secret' => FACEBOOK_SECRET,
		'cookie' => true
	));

	$user = $facebook->getUser();

	if ($user != "")
	{
	  try {

		$user_profile	= $facebook->api('/me');
		$logoutUrl		= $facebook->getLogoutUrl();
		$fuserid		= mysql_real_escape_string($user_profile["id"]);
		$username		= mysql_real_escape_string($user_profile["email"]);
		$fname			= mysql_real_escape_string($user_profile["first_name"]);
		$lname			= mysql_real_escape_string($user_profile["last_name"]);
		$email			= mysql_real_escape_string($user_profile["email"]);
		$gender			= mysql_real_escape_string($user_profile["gender"]);
		$ip				= mysql_real_escape_string(getenv("REMOTE_ADDR"));

		$check_query = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE auth_provider='facebook' AND auth_uid='$fuserid' LIMIT 1");

		if (mysql_num_rows($check_query) > 0)
		{
			$row = mysql_fetch_array($check_query);

			if ($row['status'] == 'inactive')
			{
				header("Location: login.php?msg=2");
				exit();
			}

			smart_mysql_query("UPDATE cashbackengine_users SET last_ip='$ip', login_count=login_count+1, last_login=NOW() WHERE user_id='".(int)$row['user_id']."' LIMIT 1");

			if (!session_id()) session_start();
			$_SESSION['userid']		= $row['user_id'];
			$_SESSION['FirstName']	= $row['fname'];

			if ($_SESSION['goto'])
			{
				$redirect_url = $_SESSION['goto'];
				unset($_SESSION['goto'], $_SESSION['goto_created']);						
			}
			else
			{
				$redirect_url = "myaccount.php";
			}

			header("Location: ".$redirect_url);
			exit();
		}
		else
		{
			//$access_token		= $facebook->getAccessToken();
			$password			= generatePassword(10);
			$unsubscribe_key	= GenerateKey($username);
			$ip					= getenv("REMOTE_ADDR");
			
			if (isset($_COOKIE['referer_id']) && is_numeric($_COOKIE['referer_id']))
				$ref_id = (int)$_COOKIE['referer_id'];
			else
				$ref_id = 0;

			$insert_query = "INSERT INTO cashbackengine_users SET username='$email', password='".PasswordEncryption($password)."', email='$email', fname='$fname', lname='$lname', country='0', phone='', ref_id='$ref_id', newsletter='1', ip='$ip', status='active', auth_provider='facebook', auth_uid='$fuserid', activation_key='', unsubscribe_key='$unsubscribe_key', last_login=NOW(), login_count='1', last_ip='$ip', created=NOW()";

			smart_mysql_query($insert_query);
			$new_user_id = mysql_insert_id();

			// save SIGN UP BONUS transaction //
			if (SIGNUP_BONUS > 0)
			{
				$reference_id = GenerateReferenceID();
				smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$new_user_id', payment_type='signup_bonus', amount='".SIGNUP_BONUS."', status='confirmed', created=NOW(), process_date=NOW()");
			}

			// add bonus to referral, save transaction //
			if (REFER_FRIEND_BONUS > 0 && isset($ref_id) && $ref_id > 0 && GetUsername($ref_id) != "User not found")
			{
				$reference_id = GenerateReferenceID();
				$ref_res = smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$ref_id', ref_id='$new_user_id', payment_type='friend_bonus', amount='".REFER_FRIEND_BONUS."', status='pending', created=NOW()");
			}

			////////////////////////////////  Send welcome message  /////////////////////////
			$etemplate = GetEmailTemplate('signup');
			$esubject = $etemplate['email_subject'];
			$emessage = $etemplate['email_message'];

			$emessage = str_replace("{first_name}", $fname, $emessage);
			$emessage = str_replace("{username}", $email, $emessage);
			$emessage = str_replace("{password}", $password, $emessage);
			$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);
			$to_email = $fname.' '.$lname.' <'.$email.'>';

			SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);
			////////////////////////////////////////////////////////////////////////////////

			if (!session_id()) session_start();
			$_SESSION['userid']		= $new_user_id;
			$_SESSION['FirstName']	= $fname;
				
			// forward new user to member dashboard
			header("Location: myaccount.php?msg=welcome");
			exit();
		}

	  } catch (FacebookApiException $e) {
		//echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
		$user = null;
	  }
	}
	else
	{
		$params = array(
			'canvas' => 1,
			'scope'  => 'public_profile,email',
			'fbconnect' => 1,
		);

		$fb_login_url = $facebook->getLoginUrl($params);
		header("Location: ".$fb_login_url);
		exit();
	}

?>