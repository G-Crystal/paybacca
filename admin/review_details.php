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
		$pn			= (int)$_GET['pn'];
		$reviewid	= (int)$_GET['id'];

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS date_added FROM cashbackengine_reviews WHERE review_id='$reviewid' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Review Details";
	require_once ("inc/header.inc.php");

?>
    
     <h2>Review Details</h2>

	 <?php if ($total > 0) {
	
		 $row = mysql_fetch_array($result);

	 ?>
        <table bgcolor="#F9F9F9" width="90%" cellpadding="2" cellspacing="3"  border="0" align="center">
          <tr>
			<tr>
				<td width="45%" valign="middle" align="right" class="tb1">By:</td>
				<td valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a></td>
			</tr>
			<tr>
				<td width="30%" valign="middle" align="right" class="tb1">Store:</td>
				<td width="70%" valign="top"><a href="retailer_details.php?id=<?php echo $row['retailer_id']; ?>"><?php echo GetStoreName($row['retailer_id']); ?></a></td>
			</tr>
			<tr>
				<td width="30%" valign="middle" align="right" class="tb1">Rating:</td>
				<td width="70%" valign="top"><img src="images/icons/rating-<?php echo $row['rating']; ?>.gif" alt="<?php echo $row['rating']; ?> of 5" title="<?php echo $row['rating']; ?> of 5" /></td>
			</tr>
			<tr>
				<td width="30%" valign="middle" align="right" class="tb1">Title:</td>
				<td width="70%" valign="top"><b><?php echo $row['review_title']; ?></b></td>
			</tr>
			<tr>
				<td valign="top" align="right" class="tb1">Review:</td>
				<td valign="top"><?php echo $row['review']; ?></td>
            </tr>
			<tr>
				<td valign="top" align="right" class="tb1">Date Added:</td>
				<td valign="top"><?php echo $row['date_added']; ?></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Status:</td>
				<td valign="top">
				<?php
						switch ($row['status'])
						{
							case "pending": echo "<span class='pending_status'>awaiting approval</span>"; break;
							case "active": echo "<span class='active_s'>".$row['status']."</span>"; break;
							case "inactive": echo "<span class='inactive_s'>".$row['status']."</span>"; break;
							default: echo "<span class='default_status'>".$row['status']."</span>"; break;
						}
				?>
				</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="button" class="submit" name="edit" value="Edit Review" onClick="javascript:document.location.href='review_edit.php?id=<?php echo $row['review_id']; ?>&page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" /> &nbsp; 
				<input type="button" class="cancel" name="cancel" value="Go Back" onClick="javascript:document.location.href='reviews.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
			  </td>
            </tr>
          </table>
      
	  <?php }else{ ?>
			<p align="center">Sorry, no review found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>