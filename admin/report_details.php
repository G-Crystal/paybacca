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

		$query = "SELECT reports.*, reports.user_id as ruser_id, reports.retailer_id as rretailer_id, DATE_FORMAT(reports.added, '%e %b %Y %h:%i %p') AS date_added, retailers.* FROM cashbackengine_reports reports LEFT JOIN cashbackengine_retailers retailers ON retailers.retailer_id=reports.retailer_id WHERE reports.report_id='$id' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Report Details";
	require_once ("inc/header.inc.php");

?>
    
    
     <h2>Report Details</h2>

		<img src="images/icons/alert.png" align="right" />

		<?php if ($total > 0) {

				smart_mysql_query("UPDATE cashbackengine_reports SET viewed='1' WHERE report_id='$id'");
				$row = mysql_fetch_array($result);
		 ?>
            <table width="70%" align="center" cellpadding="3" cellspacing="5" border="0">
			  <tr>
                <td width="80" valign="middle" align="right" class="tb1">Report ID:</td>
                <td valign="top"><?php echo $row['report_id']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">From:</td>
                <td valign="top"><a href="user_details.php?id=<?php echo $row['reporter_id']; ?>" class="user"><?php echo GetUsername($row['reporter_id']); ?></a></td>
              </tr>
			  <?php if ($row['rretailer_id'] != 0) { ?>
              <tr>
                <td valign="middle" align="right" class="tb1">Retailer ID:</td>
                <td valign="top"><a href="retailer_details.php?id=<?php echo $row['rretailer_id']; ?>"><?php echo $row['rretailer_id']; ?></a></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Retailer Name:</td>
                <td valign="top"><a href="retailer_details.php?id=<?php echo $row['rretailer_id']; ?>"><?php echo GetStoreName($row['rretailer_id']); ?></a></td>
              </tr>
			  <?php } ?>
              <tr>
				<td valign="middle" align="right" class="tb1">Reason:</td>
				<td valign="top"><div style="width:300px; min-height: 70px; padding:10px; background:#F7F7F7; border:1px solid #EEE; border-radius: 5px;"><?php echo $row['report']; ?></td>
              </tr>
              <tr>
                <td valign="middle" align="right" class="tb1">Created:</td>
                <td valign="top"><?php echo $row['date_added']; ?></td>
              </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="button" class="cancel" name="cancel" value="Go Back" onclick="history.go(-1);return false;" />
			  </td>
            </tr>
          </table>

      <?php }else{ ?>
			<div class="info_box">Sorry, no report found.</div>
			<p align="center"><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>