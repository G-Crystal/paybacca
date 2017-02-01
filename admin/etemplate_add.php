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


	if (isset($_POST['action']) && $_POST['action'] == "addetemplate")
	{
		$email_name		= mysql_real_escape_string($_POST['email_name']);
		$language		= mysql_real_escape_string($_POST['language']);
		$email_subject	= mysql_real_escape_string($_POST['esubject']);
		$email_message	= mysql_real_escape_string($_POST['emessage']);

		if ($_POST['add'] && $_POST['add'] != "")
		{
			unset($errs);
			$errs = array();

			if (!($email_name && $language && $email_subject && $email_message))
			{
				$errs[] = "Please fill in all required fields";
			}
			else
			{
				$check_query = smart_mysql_query("SELECT * FROM cashbackengine_email_templates WHERE language='$language' AND email_name='$email_name'");
				if (mysql_num_rows($check_query) != 0)
				{
					$errs[] = "Sorry, that email template already exists";
				}
			}

			if (count($errs) == 0)
			{
				$sql = "INSERT INTO cashbackengine_email_templates SET language='$language', email_name='$email_name', email_subject='$email_subject', email_message='$email_message', modified=NOW()";

				if (smart_mysql_query($sql))
				{
					header("Location: etemplates.php?msg=added");
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
	}

	$title = "Create Email Template";
	require_once ("inc/header.inc.php");

?>

        <h2>Create Email Template</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" name="form1" method="post">
          <table bgcolor="#F9F9F9" width="100%" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1">Template Name:</td>
            <td valign="top">
				<select name="email_name" onChange="document.form1.submit()">
					<option value="">-- select template name --</option>
					<option value="signup" <?php if ($email_name == "signup") echo "selected='selected'"; ?>>Sign Up email</option>
					<option value="activate" <?php if ($email_name == "activate") echo "selected='selected'"; ?>>Registration Confirmation email</option>
					<option value="activate2" <?php if ($email_name == "activate2") echo "selected='selected'"; ?>>Account activation email</option>
					<option value="forgot_password" <?php if ($email_name == "forgot_password") echo "selected='selected'"; ?>>Forgot Password email</option>
					<option value="invite_friend" <?php if ($email_name == "invite_friend") echo "selected='selected'"; ?>>Invite a Friend email</option>
					<option value="cashout_paid" <?php if ($email_name == "cashout_paid") echo "selected='selected'"; ?>>Cash Out paid email</option>
					<option value="cashout_declined" <?php if ($email_name == "cashout_declined") echo "selected='selected'"; ?>>Cash Out declined email</option>
					<option value="manual_credit" <?php if ($email_name == "manual_credit") echo "selected='selected'"; ?>>Manual Payment email</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1">Language:</td>
            <td valign="top">
				<select name="language">
				<option value="">-- select language --</option>
				<?php

					$lang_sql = "SELECT * FROM cashbackengine_languages WHERE status='active' ORDER BY sort_order, language";
					$lang_result = smart_mysql_query($lang_sql);

					if (mysql_num_rows($lang_result) > 0) {
						while ($lang_row = mysql_fetch_array($lang_result)) {
				?>
					<option value="<?php echo $lang_row['language']; ?>" <?php if ($language == $lang_row['language']) echo 'selected="selected"'; ?>><?php echo $lang_row['language']; ?></option>

				<?php 
					}
						}
				?>
				</select>			
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1">Subject:</td>
            <td valign="top"><input type="text" name="esubject" id="esubject" value="<?php echo getPostParameter('esubject'); ?>" size="70" class="textbox" /></td>
          </tr>
		  <?php if (isset($email_name) && $email_name != "") { ?>
           <tr>
            <td>&nbsp;</td>
            <td height="50" bgcolor="#F7F7F7" align="center" valign="middle">
				<p>Please use following variables for this email template:</p>
				<table width="95%" align="center" cellpadding="2" cellspacing="2" border="0">
					<?php if ($email_name == "signup") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{login_url}</b></td><td nowrap="nowrap" align="left"> - Login Link</td></tr>
					<?php }elseif($email_name == "activate") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{activate_link}</b></td><td nowrap="nowrap" align="left"> - Activation Link</td></tr>
					<?php }elseif($email_name == "activate2") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{activate_link}</b></td><td nowrap="nowrap" align="left"> - Activation Link</td></tr>
					<?php }elseif($email_name == "forgot_password") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{username}</b></td><td nowrap="nowrap" align="left"> - Member Username</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{password}</b></td><td nowrap="nowrap" align="left"> - Member Password</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{login_url}</b></td><td nowrap="nowrap" align="left"> - Login Link</td></tr>
					<?php }elseif($email_name == "invite_friend") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{friend_name}</b></td><td nowrap="nowrap" align="left"> - Friend First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{referral_link}</b></td><td nowrap="nowrap" align="left"> - Referral Link</td></tr>
					<?php }elseif($email_name == "cashout_paid") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{amount}</b></td><td nowrap="nowrap" align="left"> - Amount</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{transaction_id}</b></td><td nowrap="nowrap" align="left"> - Transaction ID</td></tr>
					<?php }elseif($email_name == "cashout_declined") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{transaction_id}</b></td><td nowrap="nowrap" align="left"> - Transaction ID</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{amount}</b></td><td nowrap="nowrap" align="left"> - Amount</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{reason}</b></td><td nowrap="nowrap" align="left"> - Decline Reason</td></tr>
					<?php }elseif($email_name == "manual_credit") { ?>
						<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{transaction_id}</b></td><td nowrap="nowrap" align="left"> - Transaction ID</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{payment_type}</b></td><td nowrap="nowrap" align="left"> - Payment Type</td></tr>
						<tr><td nowrap="nowrap" align="right"><b>{amount}</b></td><td nowrap="nowrap" align="left"> - Amount</td></tr>
					<?php } ?>
				</table>
			</td>
          </tr>
		  <?php } ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Message:</td>
            <td valign="top">
				<textarea cols="80" id="editor" name="emessage" rows="10"><?php echo stripslashes($_POST['emessage']); ?></textarea>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor' );
				</script>		
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="action" id="action" value="addetemplate" />
				<input type="submit" name="add" id="add" class="submit" value="Add Email Template" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='etemplates.php'" />
            </td>
          </tr>
        </table>
      </form>

<?php require_once ("inc/footer.inc.php"); ?>