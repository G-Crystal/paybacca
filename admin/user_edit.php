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


	$pn = (int)$_GET['pn'];


	if (isset($_POST['action']) && $_POST['action'] == "edituser")
	{
		unset($errs);
		$errs = array();

		$user_id	= (int)getPostParameter('userid');
		$fname		= mysql_real_escape_string(getPostParameter('fname'));
		$lname		= mysql_real_escape_string(getPostParameter('lname'));
		$email		= mysql_real_escape_string(strtolower(getPostParameter('email')));
		$address	= mysql_real_escape_string(getPostParameter('address'));
		$address2	= mysql_real_escape_string(getPostParameter('address2'));
		$city		= mysql_real_escape_string(getPostParameter('city'));
		$state		= mysql_real_escape_string(getPostParameter('state'));
		$zip		= mysql_real_escape_string(getPostParameter('zip'));
		$country	= (int)getPostParameter('country');
		$phone		= mysql_real_escape_string(getPostParameter('phone'));
		$pwd		= mysql_real_escape_string(getPostParameter('password'));
		$pwd2		= mysql_real_escape_string(getPostParameter('password2'));
		$newsletter	= (int)getPostParameter('newsletter');
		$status		= mysql_real_escape_string(getPostParameter('status'));

		$flag = 0;

		if(!($fname && $lname && $email && $status))
		{
			$errs[] = "Please fill in all required fields";
		}

		if(isset($email) && $email !="" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			$errs[] = "Invalid email address";
		}

		if (isset($pwd) && $pwd != "" && isset($pwd2) && $pwd2 != "")
		{
			if ($pwd !== $pwd2)
			{
				$errs[] = "Password confirmation is wrong";
			}
			elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
			{
				$errs[] = "Password must be between 6-20 characters (letters and numbers)";
			}
			elseif (stristr($pwd, ' '))
			{
				$errs[] = "Password must not contain spaces";
			}
			else
			{
				$flag = 1;
			}
		}

		if (count($errs) == 0)
		{
			if ($flag == 1)
			{
				$sql = "UPDATE cashbackengine_users SET password='".PasswordEncryption($pwd)."', email='$email', fname='$fname', lname='$lname', address='$address', address2='$address2', city='$city', state='$state', zip='$zip', country='$country', phone='$phone', newsletter='$newsletter', status='$status' WHERE user_id='$user_id' LIMIT 1";
			}
			else
			{
				$sql = "UPDATE cashbackengine_users SET email='$email', fname='$fname', lname='$lname', address='$address', address2='$address2', city='$city', state='$state', zip='$zip', country='$country', phone='$phone', newsletter='$newsletter', status='$status' WHERE user_id='$user_id' LIMIT 1";
			}

			if (smart_mysql_query($sql))
			{
				header("Location: users.php?msg=updated&page=".$pn."&column=".$_GET['column']."&order=".$_GET['order']);
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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$uid = (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_users WHERE user_id='$uid' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit User";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

        <h2>Edit User</h2>


		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

		<img src="images/user.png" class="imgs"  style="position: absolute; right: 10px;" />

        <form action="" method="post">
          <table width="100%" bgcolor="#F9F9F9" style="padding: 10px 0;" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="45%" valign="middle" align="right" class="tb1"><span class="req">* </span>Username:</td>
            <td valign="top"><span style="color: #000; font-weight: bold;"><?php echo $row['username']; ?></span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>First Name:</td>
            <td valign="top"><input type="text" name="fname" id="fname" value="<?php echo $row['fname']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Last Name:</td>
            <td valign="top"><input type="text" name="lname" id="lname" value="<?php echo $row['lname']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Email:</td>
            <td valign="top"><input type="text" name="email" id="email" value="<?php echo $row['email']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Address Line 1:</td>
            <td valign="top"><input type="text" class="textbox" name="address" id="address" value="<?php echo $row['address']; ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Address Line 2:</td>
            <td valign="top"><input type="text" class="textbox" name="address2" id="address2" value="<?php echo $row['address2']; ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">City:</td>
            <td valign="top"><input type="text" class="textbox" name="city" id="city" value="<?php echo $row['city']; ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">State/Province:</td>
            <td valign="top"><input type="text" class="textbox" name="state" id="state" value="<?php echo $row['state']; ?>" size="32" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Zip Code:</td>
            <td valign="top"><input type="text" class="textbox" name="zip" id="zip" value="<?php echo $row['zip']; ?>" size="32" /></td>
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
							if ($row['country'] == $row_country['country_id'])
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
            <td valign="top"><input type="text" name="phone" id="phone" value="<?php echo $row['phone']; ?>" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Newsletter:</td>
            <td align="left" valign="middle"><input type="checkbox" name="newsletter" class="checkbox" value="1" <?php echo (@$row['newsletter'] == 1) ? "checked" : "" ?>/> &nbsp;Subscribe to newsletter</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">New Password:</td>
            <td valign="top"><input type="password" name="password" id="password" value="" size="32" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Confirm New Password:</td>
            <td valign="top"><input type="password" name="password2" id="password2" value="" size="32" class="textbox" /></td>
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
			<input type="hidden" name="userid" id="userid" value="<?php echo (int)$row['user_id']; ?>" />
			<input type="hidden" name="action" id="action" value="edituser" />
			<input type="submit" name="update" id="update" class="submit" value="Update" />
			<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='users.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<p align="center">Sorry, no user found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>