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


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$username = (int)$_GET['id'];
	}

	$query = "SELECT * FROM cashbackengine_users WHERE email != '' AND newsletter='1' AND status='active'";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$query2 = "SELECT * FROM cashbackengine_users WHERE email != ''";
	$result2 = smart_mysql_query($query2);
	$total2 = mysql_num_rows($result2);


	if (isset($_POST['action']) && $_POST['action'] == "email2users")
	{
		$msubject	= trim($_POST['msubject']);
		$allmessage = $_POST['allmessage'];
		$recipients = $_POST['recipients'];
		$username	= mysql_real_escape_string(getPostParameter('username'));

		unset($errs);
		$errs = array();

		if (!($msubject && $allmessage))
		{
			$errs[] = "Please enter subject and message";
		}
		else
		{
			switch ($recipients)
			{
				case "all":			$query = "SELECT * FROM cashbackengine_users WHERE email != ''"; break;
				case "subscribed":	$query = "SELECT * FROM cashbackengine_users WHERE email != '' AND newsletter='1' AND status='active'"; break;
				case "member":		$query = "SELECT * FROM cashbackengine_users WHERE user_id='$username' LIMIT 1"; break;
			}

			$result = smart_mysql_query($query);
			
			if (mysql_num_rows($result) == 0)
			{
				$errs[] = "Member not found";
			}
		}

		if (count($errs) == 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				////////////////////////////////  Send Message  //////////////////////////////
				$allmessage = str_replace("{first_name}", $row['fname'], $allmessage);
				$allmessage = str_replace("{unsubscribe_link}", SITE_URL."unsubscribe.php?key=".$row['unsubscribe_key'], $allmessage);
				$message = "<html>
							<head>
								<title>".$subject."</title>
							</head>
							<body>".$allmessage."</body>
							</html>";
				$to_email = $row['fname'].' '.$row['lname'].' <'.$row['email'].'>';

				SendEmail($to_email, $msubject, $message, $noreply_mail = 1);
				///////////////////////////////////////////////////////////////////////////////
			}

			header ("Location: email2users.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= $errorname."<br/>\n";
		}
	}
	else
	{
		$allmessage = "
			<p style='font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:12px;'>
			<br/><br/><br/><br/>
			<div style='font-family:tahoma,arial,sans-serif;padding-top:12px;clear:both;font-size:11px;color:#5B5B5B;text-align:left;'>	
			--------------------------------------------------------------------------------------------<br/>
			You are receiving this email as you have directly signed up to ".SITE_TITLE.".<br/>If you do not wish to receive these messages in the future, please <a href='{unsubscribe_link}' target='_blank'>unsubscribe</a>.</div></p>";
	}


	$title = "Send Email to All Members";
	require_once ("inc/header.inc.php");

?>

      <?php if ($total2 > 0) { ?>

        <h2>Send Email to All Members</h2>

		<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
			<div class="success_box">Your message has been successfully sent!</div>
		<?php }else{ ?>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

		<script type="text/javascript">
		function send_to(){
			recipient = $("#recipients").val();
			if(recipient == "member"){
				$("#single_member").show();
			}else{
				$("#single_member").hide();
			}
		}
		</script>

		<div class="subscribers">
			<span style="font-size:15px; color:#FFF; background:#777777; padding:3px 8px;"><?php echo $total2; ?></span>&nbsp; <?php echo ($total2 == 1) ? "member" : "members"; ?><br/><br/>
			<span style="font-size:15px; color:#FFF; background:#6BEB2B; padding:3px 8px;"><?php echo $total; ?></span>&nbsp; subscribed <?php echo ($total == 1) ? "member" : "members"; ?>
		</div>

        <form action="email2users.php" method="post">
          <table width="100%" bgcolor="#F9F9F9" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td nowrap="nowrap" valign="top" align="right" class="tb1">From:</td>
            <td valign="top"><b><?php echo EMAIL_FROM_NAME; ?></b> &lt;<?php echo NOREPLY_MAIL; ?>&gt; &nbsp; <a href="settings.php#mail"><img src="images/icon_edit.png" align="absmiddle" />edit</a></td>
          </tr>
          <tr>
            <td width="35" valign="middle" align="right" class="tb1">Send To:</td>
            <td valign="top">
				<select name="recipients" id="recipients" onchange="send_to();">
					<option value="all" <?php echo ($recipients == 'all') ? "selected='selected'" : ""; ?>>All Members (<?php echo $total2; ?>)</option>
					<?php if ($total > 0) { ?>
						<option value="subscribed" <?php echo ($recipients == 'subscribed') ? "selected='selected'" : ""; ?>>Subscribed Members (<?php echo $total; ?>)</option>
					<?php } ?>
					<option value="member" <?php echo ($recipients == 'member' || $_GET['id']) ? "selected='selected'" : ""; ?>>A single member</option>
				</select>
			</td>
          </tr>
		  <tr id="single_member" <?php if (@$recipients != "member" && !$_GET['id']) { ?>style="display:none;"<?php } ?>>
            <td valign="middle" align="right" class="tb1">User ID:</td>
            <td valign="top"><input type="text" class="textbox" name="username"  id="username" value="<?php echo @$username; ?>" size="17" /></td>
		  </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Subject:</td>
            <td valign="top"><input type="text" name="msubject" id="msubject" value="<?php echo @$msubject; ?>" size="80" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">&nbsp;</td>
            <td height="30" bgcolor="#F5F5F5" align="center" valign="middle">
				<p>The following variables can be used in message:</p>
				<table width="95%" align="center" cellpadding="2" cellspacing="2" border="0">
					<tr><td nowrap="nowrap" align="right"><b>{first_name}</b></td><td nowrap="nowrap" align="left"> - Member First Name</td></tr>
					<tr><td nowrap="nowrap" align="right"><b>{unsubscribe_link}</b></td><td nowrap="nowrap" align="left"> - Newsletter Unsubscribe Link</td></tr>
				</table>
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Message:</td>
            <td valign="top">
				<textarea cols="80" id="editor" name="allmessage" rows="10"><?php echo stripslashes($allmessage); ?></textarea>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor' );
				</script>		
			</td>
          </tr>
          <tr>
			<td colspan="2" align="center" valign="top">
				<input type="hidden" name="action" id="action" value="email2users" />
				<input type="submit" name="Send" id="Send" class="submit" value="Send Message" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='index.php'" />
            </td>
          </tr>
        </table>
      </form>

		<?php } ?>

      <?php }else{ ?>
				<div class="info_box">There are no members at this time.</div>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>