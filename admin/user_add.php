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


	if (isset($_POST['action']) && $_POST['action'] == "adduser")
	{
		unset($errs);
		$errs = array();

		$fname			= mysql_real_escape_string(getPostParameter('fname'));
		$lname			= mysql_real_escape_string(getPostParameter('lname'));
		$email			= mysql_real_escape_string(strtolower(getPostParameter('email')));
		$username		= mysql_real_escape_string(strtolower(getPostParameter('email')));
		$pwd			= mysql_real_escape_string(getPostParameter('password'));
		$address		= mysql_real_escape_string(getPostParameter('address'));
		$address2		= mysql_real_escape_string(getPostParameter('address2'));
		$city			= mysql_real_escape_string(getPostParameter('city'));
		$state			= mysql_real_escape_string(getPostParameter('state'));
		$zip			= mysql_real_escape_string(getPostParameter('zip'));
		$country		= (int)getPostParameter('country');
		$phone			= mysql_real_escape_string(getPostParameter('phone'));
		$send_details	= (int)getPostParameter('send_details');
		$signup_bonus	= (int)getPostParameter('signup_bonus');
		$newsletter		= (int)getPostParameter('newsletter');
		$status			= mysql_real_escape_string(getPostParameter('status'));

		if(!($fname && $lname && $email && $pwd && $status))
		{
			$errs[] = "Please fill in all required fields";
		}
		else
		{
			if(isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				$errs[] = "Invalid email address";
			}

			if ((strlen($pwd) < 6) || (strlen($pwd) > 20))
			{
				$errs[] = "Password must be between 6-20 characters (letters and numbers)";
			}
			elseif (stristr($pwd, ' '))
			{
				$errs[] = "Password must not contain spaces";
			}
		}

		if (count($errs) == 0)
		{
			$unsubscribe_key = GenerateKey($username);

			$insert_query = "INSERT INTO cashbackengine_users SET username='$username', password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', address='$address', address2='$address2', city='$city', state='$state', zip='$zip', country='$country', phone='$phone', newsletter='$newsletter', ip='', status='$status', unsubscribe_key='$unsubscribe_key', created=NOW()"; //ip='111.111.111.111'
			smart_mysql_query($insert_query);
			$new_user_id = mysql_insert_id();

			// save SIGN UP BONUS transaction //
			if ($signup_bonus == 1 && SIGNUP_BONUS > 0)
			{
				$reference_id = GenerateReferenceID();
				smart_mysql_query("INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', user_id='$new_user_id', payment_type='signup_bonus', amount='".SIGNUP_BONUS."', status='confirmed', created=NOW(), process_date=NOW()");
			}

			// send login info //
			if ($send_details == 1)
			{
				$etemplate = GetEmailTemplate('signup');
				$esubject = $etemplate['email_subject'];
				$emessage = $etemplate['email_message'];

				$emessage = str_replace("{first_name}", $fname, $emessage);
				$emessage = str_replace("{username}", $email, $emessage);
				$emessage = str_replace("{password}", $pwd, $emessage);
				$emessage = str_replace("{login_url}", SITE_URL."login.php", $emessage);
				$to_email = $fname.' '.$lname.' <'.$email.'>';

				SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);
			}

			header("Location: users.php?msg=added&page=".$pn."&column=".$_GET['column']."&order=".$_GET['order']);
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}

	}


	$title = "Add User";
	require_once ("inc/header.inc.php");

?>

        <h2>Add User</h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

		<img src="images/user.png" class="imgs"  style="position: absolute; right: 10px;" />

        <form action="" method="post">
          <table width="100%" bgcolor="#F9F9F9" style="padding: 10px 0;" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="45%" valign="middle" align="right" class="tb1"><span class="req">* </span>First Name:</td>
            <td valign="top"><input type="text" name="fname" id="fname" value="<?php echo getPostParameter('fname'); ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Last Name:</td>
            <td valign="top"><input type="text" name="lname" id="lname" value="<?php echo getPostParameter('lname'); ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Email:</td>
            <td valign="top"><input type="text" name="email" id="email" value="<?php echo getPostParameter('email'); ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Password:</td>
            <td valign="top"><input type="password" name="password" id="password" value="" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Address Line 1:</td>
            <td valign="top"><input type="text" class="textbox" name="address" id="address" value="<?php echo getPostParameter('address'); ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Address Line 2:</td>
            <td valign="top"><input type="text" class="textbox" name="address2" id="address2" value="<?php echo getPostParameter('address2'); ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">City:</td>
            <td valign="top"><input type="text" class="textbox" name="city" id="city" value="<?php echo getPostParameter('city'); ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">State/Province:</td>
            <td valign="top"><input type="text" class="textbox" name="state" id="state" value="<?php echo getPostParameter('state'); ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Zip Code:</td>
            <td valign="top"><input type="text" class="textbox" name="zip" id="zip" value="<?php echo getPostParameter('zip'); ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Country:</td>
            <td valign="top">
				<select name="country" class="textbox2" id="country" style="width: 183px;">
				<option value="">-- Select country --</option>
				<?php

					$sql_country = "SELECT * FROM cashbackengine_countries WHERE signup='1' AND status='active' ORDER BY sort_order, name";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							if ($country == $row_country['country_id'])
								echo "<option value='".$row_country['country_id']."' selected>".$row_country['name']."</option>\n";
							else
								echo "<option value='".$row_country['country_id']."'>".$row_country['name']."</option>\n";
						}
					}

				?>
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Phone:</td>
            <td valign="top"><input type="text" name="phone" id="phone" value="<?php echo getPostParameter('phone'); ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">&nbsp;</td>
            <td align="left" valign="middle"><input type="checkbox" name="send_details" class="checkbox" value="1" <?php echo (@$send_details == 1) ? "checked" : "" ?>/> &nbsp;Send email with login info to member</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">&nbsp;</td>
            <td align="left" valign="middle"><input type="checkbox" name="signup_bonus" class="checkbox" value="1" <?php echo (@$signup_bonus == 1) ? "checked" : "" ?>/> &nbsp;Add signup bonus</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">&nbsp;</td>
            <td align="left" valign="middle"><input type="checkbox" name="newsletter" class="checkbox" value="1" <?php echo (@$newsletter == 1) ? "checked" : "" ?>/> &nbsp;Subscribe to newsletter</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($status == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($status == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="action" id="action" value="adduser" />
				<input type="submit" name="add" id="add" class="submit" value="Add User" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='users.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
            </td>
          </tr>
        </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>