<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/auth.inc.php");
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");

	define('FRIENDS_INVITATIONS_LIMIT', 5);
	
	$ReferralLink	= SITE_URL."?ref=".$userid;
	$umessage		= CBE1_INVITE_EMAIL_MESSAGE;
	$umessage		= str_replace("<br/>", "&#13;&#10;", $umessage);
	$umessage		= str_replace("%site_title%", SITE_TITLE, $umessage);
	$umessage		= str_replace("%referral_link%", $ReferralLink, $umessage);


	if (isset($_POST['action']) && $_POST['action'] == "friend")
	{
		unset($errs);
		$errs = array();

		$uname		= $_SESSION['FirstName'];
		$fname		= array();
		$fname		= $_POST['fname'];
		$femail		= array();
		$femail		= $_POST['femail'];
		$umessage	= nl2br(getPostParameter('umessage'));

		if(!($fname[1] && $femail[1]))
		{
			$errs[] = CBE1_INVITE_ERR;
		}
		else
		{
			foreach ($fname as $k=>$v)
			{
				if ($femail[$k] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $femail[$k]))
				{
					$errs[] = CBE1_INVITE_ERR2." #".$k;
				}
			}
		}

		if (count($errs) == 0)
		{
			$etemplate = GetEmailTemplate('invite_friend');
				
			$recipients = "";

			foreach ($fname as $k=>$v)
			{
				if (isset($v) && $v != "" && isset($femail[$k]) && $femail[$k] != "")
				{
					$friend_name	= substr(htmlentities(trim($v)), 0, 25);
					$friend_email	= substr(htmlentities(trim($femail[$k])), 0, 70);
						
					$esubject = $etemplate['email_subject'];

					if ($umessage != "")
					{
						$emessage = $umessage;
						$emessage = str_replace("%friend_name%", $friend_name, $emessage);
						$emessage = str_replace("%referral_link%", $ReferralLink, $emessage);
						$emessage = preg_replace('/((www|http:\/\/)[^ ]+)/', '<a href="\1" target="_blank">\1</a>', $emessage);
						$emessage .= "<p><a href='$ReferralLink' target='_blank'>".$ReferralLink."</a></p>";
					}
					else
					{
						$emessage = $etemplate['email_message'];
						$emessage = str_replace("{friend_name}", $friend_name, $emessage);
						$emessage = str_replace("{first_name}", $uname, $emessage);
						$emessage = str_replace("{referral_link}", $ReferralLink, $emessage);
					}

					$recipients .= $friend_name." <".$friend_email.">||";

					$to_email = $friend_name.' <'.$friend_email.'>';					

					SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);
				}
			}

			// save invitations info //
			smart_mysql_query("INSERT INTO cashbackengine_invitations SET user_id='".(int)$userid."', recipients='".mysql_real_escape_string($recipients)."', message='".mysql_real_escape_string($umessage)."', sent_date=NOW()");

			header("Location: invite.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_INVITE_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><img src="<?php echo SITE_URL; ?>images/refer_friend.png" align="absmiddle" /> <?php echo CBE1_INVITE_TITLE; ?></h1>

	<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
	<?php if (REFER_FRIEND_BONUS > 0) { ?>
	<tr>
		<td align="left" valign="top">
			<?php echo str_replace("%amount%",DisplayMoney(REFER_FRIEND_BONUS),CBE1_INVITE_TEXT); ?>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td align="left" valign="middle">
			<div class="referral_link_share">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo str_replace("/?","/index.php?",$ReferralLink); ?>&t=<?php echo SITE_TITLE; ?>" target="_blank" title="<?php echo CBE1_SHARE_FACEBOOK; ?>"><img src="<?php echo SITE_URL; ?>images/facebook_share.png" align="absmiddle" alt="<?php echo CBE1_SHARE_FACEBOOK; ?>" /></a>
				<a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(SITE_TITLE); ?>&url=<?php echo urlencode($ReferralLink); ?>" target="_blank" title="<?php echo CBE1_SHARE_TWITTER; ?>"><img src="<?php echo SITE_URL; ?>images/twitter_share.png" align="absmiddle" alt="<?php echo CBE1_SHARE_TWITTER; ?>" /></a>
				<a href="https://plus.google.com/share?url=<?php echo urlencode($ReferralLink); ?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,top=100,left=400,height=600,width=600');return false;" title="<?php echo CBE1_SHARE_GOOGLE; ?>"><img src="<?php echo SITE_URL; ?>images/google_share.png" align="absmiddle" alt="<?php echo CBE1_SHARE_GOOGLE; ?>" /></a>
			</div>
		</td>
	</tr>
	</table>

	<div class="referral_link">
	<b><?php echo CBE1_INVITE_LINK; ?>:</b>
	<input type="text" id="invite_link" class="reflink_textbox" size="50" readonly="readonly" onfocus="this.select();" onclick="this.focus();this.select();" value="<?php echo $ReferralLink; ?>" />
	<a href="#" id="copy-button" class="submit"><img src="<?php echo SITE_URL; ?>images/icon_clipboard.png" align="absmiddle" /> <?php echo CBE1_CLIPBOARD_COPY; ?></a>
	</div>

	<script type="text/javascript">
		$("#copy-button").zclip({
			path: "<?php echo SITE_URL; ?>js/ZeroClipboard.swf",
			copy: function () {
				return $(this).prev().val();
			},
			afterCopy: function(){
					 $('#copy-button').text('<?php echo CBE1_CLIPBOARD_COPIED; ?>'); 
				}
		});
	</script>

	<?php if (REFER_FRIEND_BONUS > 0) { ?>
	<a name="refs"></a>
	<table width="100%" align="center" bgcolor="#F7F7F7" border="0" cellspacing="0" cellpadding="10">
	<tr>
		<td nowrap width="25%" align="center"><span class="count stats1"><?php echo GetRefClicksTotal($userid); ?></span><br/><br/><?php echo CBE1_INVITE_STATS1; ?></td>
		<td nowrap width="25%" align="center"><span class="count stats2"><a href="#referrals"><?php echo GetReferralsTotal($userid); ?></a></span><br/><br/><?php echo CBE1_INVITE_STATS2; ?></td>
		<td nowrap width="25%" align="center"><span class="count stats3"><?php echo GetReferralsPendingBonuses($userid); ?></span><br/><br/><?php echo CBE1_INVITE_STATS3; ?></td>
		<td nowrap width="25%" align="center"><span class="count stats4"><?php echo GetReferralsPaidBonuses($userid); ?></span><br/><br/><?php echo CBE1_INVITE_STATS4; ?></td>
	</tr>
	</table><br/>
	<?php } ?>

	<a name="send"></a>
	<h1><?php echo CBE1_INVITE_TITLE2; ?></h1>

	<?php if (REFER_FRIEND_BONUS > 0) { ?>
	<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td align="center" valign="top">
			<?php echo str_replace("%amount%",DisplayMoney(REFER_FRIEND_BONUS),CBE1_INVITE_TEXT2); ?><br/><br/>
		</td>
	</tr>
	</table>
	<?php } ?>


	<?php if (isset($_GET['msg']) and $_GET['msg'] == 1) { ?>
		<div class="success_msg"><?php echo CBE1_INVITE_SENT; ?></div>
		<p align="center"><a class="submit" href="<?php echo SITE_URL; ?>invite.php#send"><?php echo CBE1_INVITE_SEND_MORE; ?> &raquo;</a></p>
	<?php }else{ ?>
          
		<?php if (isset($allerrors) and $allerrors != "") { ?>
			<div class="error_msg" ><?php echo $allerrors; ?></div>
		<?php } ?>

		<form action="" method="post">
		<table bgcolor="#F7F7F7" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
			<td align="left" valign="top">
				<br/>
				<table align="center" width="70%" cellpadding="3" cellspacing="1" border="0">
                <tr>
					<td width="6%">&nbsp;</td>
					<td width="47%" align="left" valign="top"><?php echo CBE1_INVITE_FNAME; ?> <span class="req">* </span></td>
					<td width="47%" align="left" valign="top"><?php echo CBE1_INVITE_EMAIL; ?> <span class="req">* </span><br/>
				</tr>
				<?php for ($i=1; $i<=FRIENDS_INVITATIONS_LIMIT; $i++) { ?>
                <tr>
					<td align="center" valign="middle"><span style="color: #777"><?php echo $i; ?>.</span></td>
					<td align="left" valign="top"><input type="text" name="fname[<?php echo $i; ?>]" class="textbox" value="<?php echo $fname[$i]; ?>" size="25" /></td>
					<td align="left" valign="top"><input type="text" name="femail[<?php echo $i; ?>]" class="textbox" value="<?php echo $femail[$i]; ?>" size="25" /></td>
				</tr>
				<?php } ?>
                <tr>
					<td>&nbsp;</td>
					<td colspan="2" align="left" valign="top">
						<?php echo CBE1_INVITE_MESSAGE; ?>:<br>
						<textarea name="umessage" id="umessage" class="textbox2" cols="54" rows="8" style="width: 344px;"><?php echo ($_POST['umessage']) ? getPostParameter('umessage') : @$umessage; ?></textarea>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="40" align="center" valign="top">
				<input type="hidden" name="action" id="action" value="friend" />
				<input type="submit" class="submit" name="Send" id="Send" value="<?php echo CBE1_INVITE_BUTTON; ?>" />
			</td>
		</tr>
		</table>
		</form>

	<?php } ?>


	<?php

		$results_per_page = 10;
		$cc = 0;

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$refs_query = "SELECT *, DATE_FORMAT(created, '".DATE_FORMAT." %h:%i %p') AS signup_date FROM cashbackengine_users WHERE ref_id='$userid' ORDER BY created DESC LIMIT $from, $results_per_page";
		$total_refs_result = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE ref_id='$userid'");
		$total_refs = mysql_num_rows($total_refs_result);

		$refs_result = smart_mysql_query($refs_query);
		$total_refs_on_page = mysql_num_rows($refs_result);

	?>
		<h1><?php echo CBE1_INVITE_REFERRALS; ?><?php echo ($total_refs > 0) ? " (".$total_refs.")" : ""; ?></h1>
		<a name="referrals"></a>

		<?php if ($total_refs > 0) { ?>

			<table align="center" class="btb" width="100%" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<th width="3%">&nbsp;</th>
				<th width="40%"><?php echo CBE1_INVITE_SNAME; ?></th>
				<th width="17%"><?php echo CBE1_INVITE_SCOUNTRY; ?></th>
				<th width="20%"><?php echo CBE1_INVITE_SDATE; ?></th>
				<th width="20%"><?php echo CBE1_INVITE_STATUS; ?></th>
			</tr>
			<?php while ($refs_row = mysql_fetch_array($refs_result)) { $cc++; ?>
			<tr class="<?php if (($cc%2) == 0) echo "row_even"; else echo "row_odd"; ?>">
				<td align="center" valign="middle"><img src="<?php echo SITE_URL; ?>images/<?php echo ($refs_row['status'] == "active") ? "areferral_icon.png" : "referral_icon.png"; ?>" align="absmiddle" /></td>
				<td align="left" valign="middle"><?php echo $refs_row['fname']." ".substr($refs_row['lname'], 0, 1)."."; ?></td>
				<td align="center" valign="middle"><?php echo GetCountry($refs_row['country'], 1); ?></td>
				<td nowrap="nowrap" align="center" valign="middle"><?php echo $refs_row['signup_date']; ?></td>
				<td nowrap="nowrap" align="center" valign="middle"><?php if ($refs_row['status'] == "active") echo CBE1_INVITE_STATUS_ACTIVE; else echo CBE1_INVITE_STATUS_INACTIVE; ?></td>
			</tr>
			<?php } ?>
			</table>

			<?php echo ShowPagination("users",$results_per_page,"invite.php?", "WHERE ref_id='".(int)$userid."'"); ?>
		
		<?php }else{ ?>
			<p align="center"><?php echo CBE1_INVITE_NOREFS; ?></p>
		<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>