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

	if (ACCOUNT_ACTIVATION != 1)
	{
		header("Location: index.php");
		exit();
	}

	if (isset($_POST['action']) && $_POST['action'] == "send_activation")
	{
		$email = strtolower(mysql_real_escape_string(getPostParameter('email')));

		if (!($email) || $email == "")
		{
			header("Location: activation_email.php?msg=1");
			exit();
		}
		else
		{
			if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
			{
				header("Location: activation_email.php?msg=2");
				exit();
			}
		}
		
		$query = "SELECT * FROM cashbackengine_users WHERE email='$email' AND status='inactive' AND activation_key!='' LIMIT 1";
		$result = smart_mysql_query($query);

		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);

			$etemplate	= GetEmailTemplate('activate2');
			$esubject	= $etemplate['email_subject'];
			$emessage	= $etemplate['email_message'];

			$activate_link = SITE_URL."activate.php?key=".$row['activation_key'];
			
			$emessage = str_replace("{first_name}", $row['fname'], $emessage);
			$emessage = str_replace("{activate_link}", $activate_link, $emessage);	
			$to_email = $row['fname'].' '.$row['lname'].' <'.$email.'>';
			
			SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);

			header("Location: activation_email.php?msg=4");
			exit();
		}
		else
		{
			header("Location: activation_email.php?msg=3");
			exit();
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_ACTIVATIONEMAIL_TITLE;

	require_once "inc/header.inc.php";
	
?>

	<h1><?php echo CBE1_ACTIVATIONEMAIL_TITLE; ?></h1>

	<p><?php echo CBE1_ACTIVATIONEMAIL_TEXT; ?></p>
	<p><?php echo CBE1_ACTIVATIONEMAIL_TEXT2; ?></p>

	<?php if (isset($_GET['msg']) && is_numeric($_GET['msg']) && $_GET['msg'] != 4) { ?>
		<div class="error_msg">
			<?php if ($_GET['msg'] == 1) { echo CBE1_ACTIVATIONEMAIL_ERR1; } ?>
			<?php if ($_GET['msg'] == 2) { echo CBE1_ACTIVATIONEMAIL_ERR2; } ?>
			<?php if ($_GET['msg'] == 3) { echo CBE1_ACTIVATIONEMAIL_ERR3; } ?>
		</div>
	<?php }elseif($_GET['msg'] == 4) { ?>
		<div class="success_msg"><?php echo CBE1_ACTIVATIONEMAIL_SENT; ?></div>
	<?php } ?>

	<?php if (!(isset($_GET['msg']) && $_GET['msg'] == 4)) { ?>
	<div class="form_box">
      <form action="" method="post">
        <table width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
          <tr height="50">
            <td width="40%" align="right" valign="middle" nowrap="nowrap"><b><?php echo CBE1_LABEL_EMAIL; ?>:</b></td>
            <td width="20%" align="left" valign="middle" nowrap="nowrap"><input type="text" class="textbox" name="email" size="30" value="" /></td>
			<td width="40%" align="left" valign="middle" nowrap="nowrap">
		  		<input type="hidden" name="action" value="send_activation" />
				<input type="submit" class="submit" name="send" id="send" value="<?php echo CBE1_SUBMIT_BUTTON; ?>" />
			</td>
          </tr>
        </table>
      </form>
	</div>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>