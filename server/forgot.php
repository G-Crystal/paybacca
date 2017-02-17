<?php

	session_start();

	require_once("../inc/config.inc.php");

    $params = json_decode($_GET['params']);

	if (isset($params->action) && $params->action == "forgot")
	{
		$email		= strtolower(mysql_real_escape_string($params->femail));
		$captcha	= mysql_real_escape_string($params->captcha);

		if (!($email) || $email == "")
		{
			$errs[] = CBE1_FORGOT_MSG1;
		}
		else
		{
			if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				$errs[] = CBE1_FORGOT_MSG2;
			}

			if (!$captcha)
			{
				$errs[] = CBE1_SIGNUP_ERR2;
			}
			else
			{
				if (empty($_SESSION['captcha']) || strcasecmp($_SESSION['captcha'], $captcha) != 0)
				{
					$errs[] = CBE1_SIGNUP_ERR3;
				}
			}
		}

		if (count($errs) == 0)
		{
			$query = "SELECT * FROM cashbackengine_users WHERE email='$email' AND status='active' LIMIT 1";
			$result = smart_mysql_query($query);

			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);

				$newPassword = generatePassword(11);

				$update_query = "UPDATE cashbackengine_users SET password='".PasswordEncryption($newPassword)."' WHERE user_id='".(int)$row['user_id']."' LIMIT 1";

				if (smart_mysql_query($update_query))
				{
					////////////////////////////////  Send Message  //////////////////////////////
					$etemplate = GetEmailTemplate('forgot_password');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];
					$emessage = str_replace("{first_name}", $row['fname'], $emessage);
					$emessage = str_replace("{username}", $row['username'], $emessage);
					$emessage = str_replace("{password}", $newPassword, $emessage);
					$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);	
					$to_email = $row['fname'].' '.$row['lname'].' <'.$email.'>';

					SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);

					// header("Location: forgot.php?msg=sent");
                    echo json_encode(array('success_msg' => CBE1_FORGOT_MSG4));
					exit();
				}
			}
			else
			{
				// header("Location: forgot.php?msg=3");
                echo json_encode(array('error_msg' => CBE1_FORGOT_MSG3));
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
