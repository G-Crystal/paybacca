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


	if (isset($_POST['action']) && $_POST['action'] == "forgot")
	{
		$email		= strtolower(mysql_real_escape_string(getPostParameter('email')));
		$captcha	= mysql_real_escape_string(getPostParameter('captcha'));

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
					
					header("Location: forgot.php?msg=sent");
					exit();
					///////////////////////////////////////////////////////////////////////////////
				}
			}
			else
			{
				header("Location: forgot.php?msg=3");
				exit();
			}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_FORGOT_TITLE;

	require_once "inc/header.inc.php";
	
?>

	<h1><?php echo CBE1_FORGOT_TITLE; ?></h1>

	<?php if (isset($allerrors) || (isset($_GET['msg']) && is_numeric($_GET['msg']) && $_GET['msg'] != "sent")) { ?>
		<div class="error_msg">
			<?php if ($_GET['msg'] == 3) { echo CBE1_FORGOT_MSG3; }elseif (isset($allerrors)) { echo $allerrors; } ?>
		</div>
	<?php }elseif($_GET['msg'] == "sent"){ ?>
		<div class="success_msg"><?php echo CBE1_FORGOT_MSG4; ?></div>
		<p align="center"><a class="goback" href="<?php echo SITE_URL; ?>login.php"><?php echo CBE1_FORGOT_GOBACK; ?></a></p>
	<?php }else{ ?> 
		<p align="center"><?php echo CBE1_FORGOT_TEXT; ?></p>
	<?php } ?>

	<?php if (!(isset($_GET['msg']) && $_GET['msg'] == "sent")) { ?>
	<div class="form_box">
      <form action="" method="post">
        <table width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr>
            <td width="40%" align="right" valign="middle" nowrap="nowrap"><?php echo CBE1_FORGOT_EMAIL; ?>:</td>
            <td align="left" valign="middle"><input type="text" class="textbox" name="email" size="30" required="required" value="<?php echo getPostParameter('email'); ?>" /></td>
          </tr>
          <tr>
            <td align="right" valign="middle"><?php echo CBE1_SIGNUP_SCODE; ?>:</td>
            <td align="left" valign="middle">
				<input type="text" id="captcha" class="textbox" name="captcha" required="required" value="" size="8" />
				<img src="<?php echo SITE_URL; ?>captcha.php?rand=<?php echo rand(); ?>&bg=grey" id="captchaimg" align="absmiddle" /> <small><a href="javascript: refreshCaptcha();" title="<?php echo CBE1_SIGNUP_RIMG; ?>"><img src="<?php echo SITE_URL; ?>images/icon_refresh.png" align="absmiddle" alt="<?php echo CBE1_SIGNUP_RIMG; ?>" /></a></small>
			</td>
          </tr>
			<script language="javascript" type="text/javascript">
				function refreshCaptcha()
				{
					var img = document.images['captchaimg'];
					img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000+"&bg=grey";
				}
			</script>
          <tr>
			<td>&nbsp;</td>
		  	<td align="left" valign="middle">
		  		<input type="hidden" name="action" value="forgot" />
				<input type="submit" class="submit" name="send" id="send" value="<?php echo CBE1_FORGOT_BUTTON; ?>" />
			</td>
          </tr>
        </table>
      </form>
	</div>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>