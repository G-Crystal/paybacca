<?php

	session_start();

	require_once("../inc/iflogged.inc.php");
	require_once("../inc/config.inc.php");

    $params = json_decode($_GET['params']);

	if (isset($params->action) && $params->action == "signup")
	{
		unset($errs);
		$errs = array();

		$fname		= mysql_real_escape_string(ucfirst(strtolower($params->fname)));
		$lname		= mysql_real_escape_string(ucfirst(strtolower($params->lname)));
		$email		= mysql_real_escape_string(strtolower($params->email));
		$username	= mysql_real_escape_string(strtolower($params->email));
		$pwd		= mysql_real_escape_string($params->pwd);
		$pwd2		= mysql_real_escape_string($params->pwd_cfm);
		$ref_id		= mysql_real_escape_string($params->referer_id);
		$ip			= mysql_real_escape_string(getenv("REMOTE_ADDR"));

		if (!($fname && $lname && $email && $pwd && $pwd2))
		{
			$errs[] = CBE1_SIGNUP_ERR;
		}

		if (isset($email) && $email != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			$errs[] = CBE1_SIGNUP_ERR4;
		}

		if (isset($pwd) && $pwd != "" && isset($pwd2) && $pwd2 != "")
		{
			if ($pwd !== $pwd2)
			{
				$errs[] = CBE1_SIGNUP_ERR6;
			}
			elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
			{
				$errs[] = CBE1_SIGNUP_ERR7;
			}
			elseif (stristr($pwd, ' '))
			{
				$errs[] = CBE1_SIGNUP_ERR8;
			}
		}

		if (count($errs) == 0)
		{
				$query = "SELECT username FROM cashbackengine_users WHERE username='$email' OR email='$email' LIMIT 1";
				$result = smart_mysql_query($query);

				if (mysql_num_rows($result) != 0)
				{
					// header ("Location: signup.php?msg=exists");
                    echo json_encode(array('error_msg' => 'exists'));
					exit();
				}

				// check referral
				if ($ref_id > 0)
				{
					$check_referral_query = "SELECT email FROM cashbackengine_users WHERE user_id='$ref_id' LIMIT 1";
					$check_referral_result = smart_mysql_query($check_referral_query);

					if (mysql_num_rows($check_referral_result) != 0)
						$ref_id = $ref_id;
					else
						$ref_id = 0;
				}

				$unsubscribe_key = GenerateKey($username);

				if (ACCOUNT_ACTIVATION == 1)
				{
					$activation_key = GenerateKey($username);
					$insert_query = "INSERT INTO cashbackengine_users SET username='$username', password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', country='$country', phone='$phone', reg_source='$reg_source', ref_id='$ref_id', newsletter='$newsletter', ip='$ip', status='inactive', activation_key='$activation_key', unsubscribe_key='$unsubscribe_key', created=NOW()";
				}
				else
				{
					$insert_query = "INSERT INTO cashbackengine_users SET username='$username', password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', country='$country', phone='$phone', reg_source='$reg_source', ref_id='$ref_id', newsletter='$newsletter', ip='$ip', status='active', activation_key='', unsubscribe_key='$unsubscribe_key', last_login=NOW(), login_count='1', last_ip='$ip', created=NOW()";
				}

				smart_mysql_query($insert_query);
				$new_user_id = mysql_insert_id();

				// save SIGN UP BONUS transaction //
				if (SIGNUP_BONUS > 0)
				{
					$reference_id = GenerateReferenceID();
					smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$new_user_id', payment_type='signup_bonus', amount='".SIGNUP_BONUS."', status='confirmed', created=NOW(), process_date=NOW()");
				}

				// add bonus to referral, save transaction //
				if (REFER_FRIEND_BONUS > 0 && isset($ref_id) && $ref_id > 0)
				{
					$reference_id = GenerateReferenceID();
    				$ref_res = smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$ref_id', ref_id='$new_user_id', payment_type='friend_bonus', amount='".REFER_FRIEND_BONUS."', status='pending', created=NOW()");
				}

				if (ACCOUNT_ACTIVATION == 1)
				{			
					////////////////////////////////  Send Message  //////////////////////////////
					$etemplate = GetEmailTemplate('activate');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];

					$activate_link = SITE_URL."activate.php?key=".$activation_key;

					$emessage = str_replace("{first_name}", $fname, $emessage);
					$emessage = str_replace("{username}", $email, $emessage);
					$emessage = str_replace("{password}", $pwd, $emessage);
					$emessage = str_replace("{activate_link}", $activate_link, $emessage);
					$to_email = $fname.' '.$lname.' <'.$email.'>';

					SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);
					////////////////////////////////////////////////////////////////////////////////

					// show activation message
					// header("Location: activate.php?msg=1");
                    echo json_encode(array('url' => "activate.php?msg=1"));
					exit();
				}
				else
				{
					////////////////////////////////  Send welcome message  ////////////////
					$etemplate = GetEmailTemplate('signup');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];
					$emessage = str_replace("{first_name}", $fname, $emessage);
					$emessage = str_replace("{username}", $email, $emessage);
					$emessage = str_replace("{password}", $pwd, $emessage);
					$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);
					$to_email = $fname.' '.$lname.' <'.$email.'>';

					SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);
					/////////////////////////////////////////////////////////////////////////
					if (!session_id()) session_start();

					$_SESSION['userid']		= $new_user_id;
					$_SESSION['FirstName']	= $fname;

					if ($_SESSION['goto'])
					{
						$redirect_url = $_SESSION['goto'];
						unset($_SESSION['goto'], $_SESSION['goto_created']);						
					}
					else
					{
						// forward new user to account dashboard
						$redirect_url = "myaccount.php?msg=welcome";
					}

					// header("Location: ".$redirect_url);
                    echo json_encode(array('url' => $redirect_url));
					exit();
				}
		}
		else
		{
			$allerrors = "";

			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";

            echo json_encode(array('error_msg' => $allerrors));
		}
	}

