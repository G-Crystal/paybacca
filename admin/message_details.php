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
		$mid	= (int)$_GET['id'];
		$pn		= (int)$_GET['pn'];

		$query = "SELECT m.*, DATE_FORMAT(m.created, '%e %b %Y %h:%i %p') AS message_date, u.fname, u.lname FROM cashbackengine_messages m, cashbackengine_users u WHERE m.user_id=u.user_id AND m.message_id='$mid'";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);

		if ($total > 0)
		{
			// mark message as viewed //
			smart_mysql_query("UPDATE cashbackengine_messages SET viewed='1' WHERE message_id='$mid'");
		}
	}

	$title = "View Message";
	require_once ("inc/header.inc.php");

?>   
    
	<?php

		if ($total > 0)
			{
				$row = mysql_fetch_array($result);
	?>

	   <h2>View Message</h2>

		<form action="" method="post" name="form1">
          <table width="550" align="center" cellpadding="5" cellspacing="5" border="0">
            <tr>
              <td width="20%" nowrap="nowrap" valign="middle" align="right" class="tb2">&nbsp;</td>
              <td width="80%" nowrap="nowrap" valign="top"><b><?php echo $row['subject']; ?></b></td>
            </tr>
           <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb2">Message:</td>
            <td height="100" style="border: 1px solid #EEE; border-radius: 5px;" valign="top">
				<div align="right"><small><font color="#A7A7A7"><?php echo $row['message_date']; ?></font></small></div>
				<a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo $row['fname']." ".$row['lname']; ?></a>
				<p><?php echo $row['message']; ?></p>
			</td>
          </tr>
		<?php

			$aquery = "SELECT *, DATE_FORMAT(answer_date, '%e %b %Y %h:%i %p') AS a_date FROM cashbackengine_messages_answers WHERE user_id='".$row['user_id']."' AND message_id='$mid' ORDER BY answer_date ASC";
			$aresult = smart_mysql_query($aquery);
			$atotal = mysql_num_rows($aresult);
			if ($atotal > 0) {
			
				while ($arow = mysql_fetch_array($aresult)) {
					if ($arow['is_admin'] == 1) {$sender = "Admin"; $bg = "#EBFDFF";}else{$sender = "Member"; $bg = "#F7F7F7";}
		?>
				<tr>
					<td nowrap="nowrap" valign="middle" align="right" class="tb2"><?php echo $sender; ?> Reply:</td>
					<td height="70" bgcolor="<?php echo $bg; ?>" style="border: 1px solid #EEE; border-radius: 5px;" valign="top">
						<div align="right"><small><font color="#A7A7A7"><?php echo $arow['a_date']; ?></font></small></div><?php echo $arow['answer']; ?>
					</td>
				</tr>
			<?php } ?> 

		<?php }	?>
          <tr>
          <td colspan="2" align="center" valign="bottom">
			<input type="button" class="submit" name="reply" value="Reply" onClick="javascript:document.location.href='message_reply.php?id=<?php echo $mid; ?>'" />
			<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='messages.php'" />
		  </td>
          </tr>
          </table>
		</form>

      <?php }else{ ?>
			<p align="center">Sorry, no message found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>