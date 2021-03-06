<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	if (isset($_POST['action']) && $_POST['action'] == "edit")
	{
		unset($errs);
		$errs = array();

		$language_id	= (int)getPostParameter('languageid');
		$language		= mysql_real_escape_string(getPostParameter('language'));
		$status			= mysql_real_escape_string(getPostParameter('status'));

		if(!($language_id && $status))
		{
			$errs[] = "Please fill in all required fields";
		}
		else
		{
			if ($status == "inactive")
			{
				$check_query = smart_mysql_query("SELECT * FROM cashbackengine_languages WHERE language_id<>'$language_id' AND status='active'");
				if (mysql_num_rows($check_query) == 0)
				{
					$errs[] = "At least one language should be active";
				}
			}
		}

		if (count($errs) == 0)
		{
			smart_mysql_query("UPDATE cashbackengine_languages SET status='$status' WHERE language_id='$language_id' LIMIT 1");

			$check_query2 = smart_mysql_query("SELECT * FROM cashbackengine_languages WHERE status='active'");
			if (mysql_num_rows($check_query2) > 1)
			{
				smart_mysql_query("UPDATE cashbackengine_settings SET setting_value='1' WHERE setting_key='multilingual' LIMIT 1");

				$check_query3 = smart_mysql_query("SELECT * FROM cashbackengine_email_templates WHERE language='$language'");
				if (mysql_num_rows($check_query3) == 0)
				{
					smart_mysql_query("INSERT INTO `cashbackengine_email_templates` (`language`, `email_name`, `email_subject`, `email_message`, `modified`) VALUES
					('$language', 'signup', 'Welcome to cashback site!', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nDear {first_name},<br /><br />\r\nThank you for registering!<br /><br />\r\nStart earning cash back on your online purchases right away!<br /><br />\r\nHere is your login information:<br /><br />\r\nLogin: <b>{username}</b><br />\r\nPassword: <b>{password}</b><br /><br />\r\nPlease click at <a href=\'{login_url}\'>click here</a> to login in to your account.<br /><br />Thank you.\r\n</p>', NOW()),
					('$language', 'activate', 'Registration confirmation email', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nHi {first_name},<br /><br />\r\nThank you for registering!<br /><br />\r\nHere is your login information:<br /><br />\r\nUsername: <b>{username}</b><br />\r\nPassword: <b>{password}</b><br /><br />\r\n\r\nPlease click the following link to activate your account: <a href=\'{activate_link}\'>{activate_link}</a><br /><br />Thank you.\r\n</p>', NOW()),
					('$language', 'activate2', 'Account activation email', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nHi {first_name},<br /><br />\r\nPlease click the following link to activate your account: <a href=\'{activate_link}\'>{activate_link}</a><br /><br />Thank you.\r\n</p>', NOW()),
					('$language', 'forgot_password', 'Forgot password email', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nDear {first_name},<br /><br />\r\nAs you requested, here is new password for your account:<br /><br />\r\nLogin: <b>{username}</b><br />Password: <b>{password}</b> <br /><br />\r\nPlease <a href=\'{login_url}\'>click here</a> to log in.\r\n<br /><br />\r\nThank you.\r\n</p>', NOW()),
					('$language', 'invite_friend', 'Invitation from your friend', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nHello {friend_name}, <br /><br />\r\nYour friend <b>{first_name}</b> wants to invite you to register on our cashback site.<br /><br />\r\nPlease <a href=\'{referral_link}\'>click here</a> to accept his invitation.\r\n<br /><br />\r\nBest Regards.\r\n</p>', NOW()),
					('$language', 'cashout_paid', 'Your cash out request was paid', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nHello {first_name}, <br /><br />\r\nYour cash out request was paid.<br />Transaction ID: {transaction_id}<br />Amount: <b>{amount}</b><br /><br />\r\nThank you for choosing us.<br /><br />\r\nBest Regards.\r\n</p>', NOW()),
					('$language', 'cashout_declined', 'Your cash out request was declined', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nHello {first_name}, <br /><br />\r\nYour cash out request #<b>{transaction_id}</b> for {amount} was declined.<br />Reason: {reason}<br /><br />\r\n</p>', NOW()),
					('$language', 'manual_credit', 'Your account balance was updated', '<p style=\'font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px\'>\r\nHello {first_name}, <br /><br />\r\nYou received new payment.<br /><br /> Transaction ID: <b>{transaction_id}</b><br/>Payment name: <b>{payment_type}</b><br />Amount: <b>{amount}</b><br /><br />\r\n</p>', NOW())");
				}

				$check_query4 = smart_mysql_query("SELECT * FROM cashbackengine_content WHERE language='$language'");
				if (mysql_num_rows($check_query4) == 0)
				{
					smart_mysql_query("INSERT INTO `cashbackengine_content` (`language`, `name`, `title`, `description`, `modified`) VALUES
					('$language', 'home', 'Home page', '<img src=\'".SITE_URL."images/home_img.png\' align=\'left\' border=\'0\' alt=\'\' />\r\n<h1 style=\'border:none;text-align:center;\'>Welcome to our cashback website!</h1>\r\n<p style=\'text-align: justify;\'>Open your own free account now and start to earn cashback. Its totally free and simple. Save money on online shopping now! Our site helps you to earn on cash back rewards, simply sign up for free and you will start earning immediately on your purchases. Earn cashback by shopping with your favorite stores.</p>\r\n<p>Start earning cash back on your online purchases!</p>\r\n<br/><p align=\'center\'><a class=\'start_link\' href=\'signup.php\'>Start Earning!</a></p>', NOW()),
					('$language', 'aboutus', 'About Us', '', '0000-00-00 00:00:00'),
					('$language', 'howitworks', 'How it works', '', NOW()),
					('$language', 'help', 'Help', '', NOW()),
					('$language', 'terms', 'Terms and Conditions', '', NOW()),
					('$language', 'privacy', 'Privacy Policy', '', NOW()),
					('$language', 'contact', 'Contact Us', '', NOW())");
				}
			}

			header("Location: languages.php?msg=updated");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}

	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$languageid = (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_languages WHERE language_id='$languageid' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit Language";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2><?php echo $row['pmethod_title']; ?></h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box" style="width: 65%"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table width="70%" bgcolor="#F9F9F9" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="220" nowrap="nowrap" valign="middle" align="right" class="tb1">Language:</td>
            <td valign="top"><b><?php echo $row['language']; ?></b></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Code:</td>
            <td valign="top"><?php echo $row['language_code']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
			<input type="hidden" name="languageid" id="languageid" value="<?php echo (int)$row['language_id']; ?>" />
			<input type="hidden" name="language" id="language" value="<?php echo $row['language']; ?>" />
			<input type="hidden" name="action" id="action" value="edit" />
			<input type="submit" name="save" id="save" class="submit" value="Update" />
			<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='languages.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<p align="center">Sorry, no language found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>