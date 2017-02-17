<?php

	session_start();

	require_once("../inc/iflogged.inc.php");
	require_once("../inc/config.inc.php");

    $params = json_decode($_GET['params']);

	if (isset($params->action) && $params->action == "login")
	{
		$username	= mysql_real_escape_string($params->username);
		$pwd		= mysql_real_escape_string($params->password);
		$remember	= (int)$params->rememberme;
		$ip			= mysql_real_escape_string(getenv("REMOTE_ADDR"));

		if (!($username && $pwd))
		{
			// $errormsg = CBE1_LOGIN_ERR;
            echo json_encode(array('error_msg' => CBE1_LOGIN_ERR));
            exit();
		}
		else
		{
			$sql = "SELECT * FROM cashbackengine_users WHERE username='$username' AND password='".PasswordEncryption($pwd)."' LIMIT 1";
			$result = smart_mysql_query($sql);

			if (mysql_num_rows($result) != 0)
			{
                $row = mysql_fetch_array($result);

                if ($row['status'] == 'inactive')
                {
                    // header("Location: login.php?msg=2");
                    echo json_encode(array('error_msg' => CBE1_LOGIN_ERR2));
                    exit();
                }

                if (LOGIN_ATTEMPTS_LIMIT == 1)
                {
                    unset($_SESSION['attems_'.$username."_".$ip], $_SESSION['attems_left']);
                }

                if ($remember == 1)
                {
                    $cookie_hash = md5(sha1($username.$ip));
                    setcookie("usname", $cookie_hash, time()+3600*24*365, '/');
                    $login_sql = "login_session = '$cookie_hash', ";
                }

                smart_mysql_query("UPDATE cashbackengine_users SET ".$login_sql." last_ip='$ip', login_count=login_count+1, last_login=NOW() WHERE user_id='".(int)$row['user_id']."' LIMIT 1");

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

                // header("Location: ".$redirect_url);
                echo json_encode(array('url' => $redirect_url));
                exit();
			}
			else
			{
				if (LOGIN_ATTEMPTS_LIMIT == 1)
				{
					$check_sql = "SELECT * FROM cashbackengine_users WHERE username='$username' AND status!='inactive' AND block_reason!='login attempts limit' LIMIT 1";
					$check_result = smart_mysql_query($check_sql);

					if (mysql_num_rows($check_result) != 0)
					{
						if (!session_id()) session_start();
						$_SESSION['attems_'.$username."_".$ip] += 1;
						$_SESSION['attems_left'] = LOGIN_ATTEMPTS - $_SESSION['attems_'.$username.'_'.$ip];

						if ($_SESSION['attems_left'] == 0)
						{ 
							// block user //
							smart_mysql_query("UPDATE cashbackengine_users SET status='inactive', block_reason='login attempts limit' WHERE username='$username' LIMIT 1"); 
							unset($_SESSION['attems_'.$username."_".$ip], $_SESSION['attems_left']);

							// header("Location: login.php?msg=6");
                            echo json_encode(array('error_msg' => CBE1_LOGIN_ERR6));
							exit();
						}
						else
						{
							// header("Location: login.php?msg=5");
                            echo json_encode(array('error_msg' => CBE1_LOGIN_ERR1." ".(int)$_SESSION['attems_left']." ".CBE1_LOGIN_ATTEMPTS));
							exit();
						}
					}
				}

				// header("Location: login.php?msg=1");
                echo json_encode(array('error_msg' => CBE1_LOGIN_ERR1));
				exit();
			}
		}
	}

