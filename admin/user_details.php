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
		$uid	= (int)$_GET['id'];
		$pn		= (int)$_GET['pn'];

		if (isset($_GET['action']) && $_GET['action'] == "block") BlockUnblockUser($uid);
		if (isset($_GET['action']) && $_GET['action'] == "unblock") BlockUnblockUser($uid,1);

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS created, DATE_FORMAT(last_login, '%e %b %Y %h:%i %p') AS last_login FROM cashbackengine_users WHERE user_id='$uid'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total = mysql_num_rows($result);
	}


	$title = "User Details";
	require_once ("inc/header.inc.php");

?>   
    
      <?php if ($total > 0) { ?>

          <h2>User Details</h2>

          <img src="images/user.png" class="imgs" style="position: absolute; right: 10px;" />

          <table bgcolor="#F9F9F9" width="100%" align="center" cellpadding="3" cellspacing="5" border="0">
          <tr>
           <td width="44%" valign="middle" align="right" class="tb1">User ID:</td>
           <td align="left" valign="middle"><?php echo $row['user_id']; ?></td>
          </tr>
           <tr>
            <td valign="middle" align="right" class="tb1">Username:</td>
            <td align="left" valign="middle"><?php echo $row['username']; ?></td>
          </tr>
           <tr>
            <td valign="middle" align="right" class="tb1">First Name:</td>
            <td align="left" valign="middle"><?php echo $row['fname']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Last Name:</td>
            <td align="left" valign="middle"><?php echo $row['lname']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Email:</td>
            <td align="left" valign="middle"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
          </tr>
		  <?php if ($row['address'] != "") { ?>
		  <tr>
            <td valign="middle" align="right" class="tb1">Address Line 1:</td>
            <td align="left" valign="middle"><?php echo $row['address']; ?></td>
          </tr>
		  <?php } ?>
		  <?php if ($row['address2'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Address Line 2:</td>
            <td align="left" valign="middle"><?php echo $row['address2']; ?></td>
          </tr>
		  <?php } ?>
		  <?php if ($row['city'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">City:</td>
            <td align="left" valign="middle"><?php echo $row['city']; ?></td>
          </tr>
		  <?php } ?>
		  <?php if ($row['state'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">State/Province:</td>
            <td align="left" valign="middle"><?php echo $row['state']; ?></td>
          </tr>
		  <?php } ?>
		  <?php if ($row['zip'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Zip Code:</td>
            <td align="left" valign="middle"><?php echo $row['zip']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Country:</td>
            <td align="left" valign="middle"><?php echo GetCountry($row['country']); ?></td>
          </tr>
		  <?php if ($row['phone'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Phone:</td>
            <td align="left" valign="middle"><?php echo $row['phone']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Balance:</td>
            <td align="left" valign="middle">
				<span class="amount"><?php echo GetUserBalance($row['user_id']); ?></span>
				<?php if (GetUserBalance($row['user_id'], 1) > 0) { ?>&nbsp;<a href="user_payments.php?id=<?php echo $row['user_id']; ?>">view payments &#155;</a> <?php } ?>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Clicks:</td>
            <td align="left" valign="middle"><a href="clicks.php?user=<?php echo $row['user_id']; ?>"><?php echo GetUserClicksTotal($row['user_id']); ?></a></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Reviews:</td>
            <td align="left" valign="middle"><a href="reviews.php?user=<?php echo $row['user_id']; ?>"><?php echo GetUserReviewsTotal($row['user_id']); ?></a>
			</td>
          </tr>
		  <?php if ($row['ref_id'] > 0) { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Referred by:</td>
            <td align="left" valign="middle"><a href="user_details.php?id=<?php echo $row['ref_id']; ?>"><?php echo GetUsername($row['ref_id']); ?></a></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Referrals:</td>
            <td align="left" valign="middle">
				<table width="300" bgcolor="#F7F7F7" border="0" cellspacing="0" cellpadding="10">
				<tr>
					<td width="25%" align="center" valign="top"><span class="count" style="background:#939393;"><?php echo GetRefClicksTotal($row['user_id']); ?></span><br/><br/> referral link clicks</td>
					<td width="25%" align="center" valign="top"><span class="count" style="background:#6EB8C9;"><a href="user_referrals.php?id=<?php echo $row['user_id']; ?>"><?php echo GetReferralsTotal($row['user_id']); ?></a></span><br/><br/> referrals</td>
					<td width="25%" align="center" valign="top"><span class="count" style="background:#FFAD16;"><?php echo GetReferralsPendingBonuses($row['user_id']); ?></span><br/><br/> pending earnings</td>
					<td width="25%" align="center" valign="top"><span class="count" style="background:#7AD108;"><?php echo GetReferralsPaidBonuses($row['user_id']); ?></span><br/><br/> paid earnings</td>
				</tr>
				</table>
			</td>
          </tr>
		  <?php if ($row['reg_source'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">How did you hear about us:</td>
            <td align="left" valign="middle"><?php echo $row['reg_source']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Newsletter:</td>
            <td align="left" valign="middle"><?php echo ($row['newsletter'] == 1) ? "<img src='./images/icons/yes.png' align='absmiddle'>" : "<img src='./images/icons/no.png' align='absmiddle'>"; ?></td>
          </tr>
		  <?php if ($tow['auth_provider'] == "facebook" && $row['auth_uid'] != 0) { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Facebook Signup:</td>
            <td align="left" valign="middle"><img src="./images/icons/yes.png" align="absmiddle" /></td>
          </tr>
		  <?php } ?>
		  <?php if ($tow['auth_provider'] == "twitter" && $row['auth_uid'] != 0) { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Twitter Signup:</td>
            <td align="left" valign="middle"><img src="./images/icons/yes.png" align="absmiddle" /></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Signup Date:</td>
            <td align="left" valign="middle"><?php echo $row['created']; ?></td>
          </tr>
		  <?php if ($row['ip'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">IP Address:</td>
            <td align="left" valign="middle"><?php echo $row['ip']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Login Count:</td>
            <td align="left" valign="middle"><?php echo $row['login_count']; ?></td>
          </tr>
		  <?php if ($row['login_count'] > 0) { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Last Login:</td>
            <td align="left" valign="middle"><?php echo $row['last_login']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Last Login IP address:</td>
            <td align="left" valign="middle"><?php echo $row['last_ip']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td align="left" valign="middle">
				<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
				<?php if ($row['status'] == "inactive" && $row['activation_key'] != "") { ?> <sup>(awaiting activation by email)</sup><?php } ?>
			</td>
          </tr>
		  <?php if ($row['status'] == "inactive" && $row['block_reason'] != "") { ?>
          <tr>
            <td valign="middle" align="right" class="tb1">Block Reason:</td>
            <td align="left" valign="middle"><?php echo $row['block_reason']; ?></td>
          </tr>
		  <?php } ?>
          <tr>
            <td bgcolor="#F7F7F7" height="50" style="border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee;" colspan="2" align="center" valign="middle">
				<?php if ($row['status'] == "active") { ?>
					<a class="blockit" href="user_details.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $pn; ?>&action=block">Block User</a>
				<?php }else{ ?>
					<a class="unblockit" href="user_details.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $pn; ?>&action=unblock">UnBlock User</a>
				<?php } ?>
				<a class="emailit" href="email2users.php?id=<?php echo $row['user_id']; ?>">Send Email</a>
				<a class="moneyit" href="money2user.php?id=<?php echo $row['user_id']; ?>">Send Money</a>
			</td>
          </tr>  
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="button" class="submit" name="edit" value="Edit User" onClick="javascript:document.location.href='user_edit.php?id=<?php echo $row['user_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />&nbsp;
				<input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" /> &nbsp; 
				<input type="button" class="cancel" style="position: absolute; bottom: 40px; right: 15px; background: #D85B56; border: 1px solid #D85B56;" name="delete" value="Delete User" onclick="if (confirm('Are you sure you really want to delete this user?') )location.href='user_delete.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete">
		    </td>
          </tr>
          </table>
      
	  <?php }else{ ?>
			<p align="center">Sorry, no user found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>