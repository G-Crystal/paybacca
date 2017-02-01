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


	if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0)
	{
		$uid = (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y %h:%i %p') AS signup_date FROM cashbackengine_users WHERE ref_id='$uid' ORDER BY created DESC";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}

	$title = "User's Referrals";
	require_once ("inc/header.inc.php");

?>

	<h2>User's Referrals</h2>

	  <?php if ($total > 0) { ?>

            <table class="tbl" align="center" width="100%" border="0" cellspacing="0" cellpadding="3">
              <tr>
				<th width="24%">Name</th>
				<th width="24%">Username</th>              
                <th width="20%">Country</th>
				<th width="20%">Signup Date</th>
                <th width="17%">Status</th>
              </tr>
				<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
                <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
                  <td valign="middle" align="center"><a href="user_details.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
				  <td valign="middle" align="center"><?php echo $row['username']; ?></td>
				  <td valign="middle" align="center"><?php echo GetCountry($row['country']); ?></td>
                  <td valign="middle" align="center"><?php echo $row['signup_date']; ?></td>
				  <td valign="middle" align="center">
					<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
				  </td>
                </tr>
				<?php } ?>
           </table>
	  
	  <?php }else{ ?>
			<div class="info_box">User has not received any referrals at this time.</div>
      <?php } ?>

	  <p align="center"><input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" /></p>

<?php require_once ("inc/footer.inc.php"); ?>