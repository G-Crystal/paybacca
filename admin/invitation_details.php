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
		$id = (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(sent_date, '%e %b %Y %h:%i %p') AS date_sent FROM cashbackengine_invitations WHERE invitation_id='$id' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Invitation Details";
	require_once ("inc/header.inc.php");

?>
    
    
     <h2>Invitation Details</h2>

		<?php if ($total > 0) {

				$row = mysql_fetch_array($result);
		 ?>
            <table width="70%" align="center" cellpadding="3" cellspacing="5" border="0">
			  <tr>
                <td width="80" valign="middle" align="right" class="tb1">From:</td>
                <td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Recipients:</td>
                <td valign="top">
						<?php 
								$recipients = explode("||", $row['recipients']);

								foreach ($recipients as $v)
								{
									if ($v != "")
									{
										$recipient = explode("|", $v);
										echo $recipient[0]." / ".$recipient[1]."<br/>";
									}
								}
						?>				
				</td>
              </tr>
			  <?php if ($row['message'] != "") { ?>
              <tr>
				<td valign="middle" align="right" class="tb1">Message:</td>
				<td valign="top"><?php echo $row['message']; ?></td>
              </tr>
			  <?php } ?>
              <tr>
				<td valign="middle" align="right" class="tb1">Date Sent:</td>
				<td valign="top"><?php echo $row['date_sent']; ?></td>
              </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" />
			  </td>
            </tr>
          </table>

      <?php }else{ ?>
			<div class="info_box">Sorry, no invitation found.</div>
			<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>