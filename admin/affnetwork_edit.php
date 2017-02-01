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


	if (isset($_POST['action']) && $_POST['action'] == "editnetwork")
	{
		unset($errs);
		$errs = array();

		$network_id	= (int)getPostParameter('networkid');
		$subid		= mysql_real_escape_string(getPostParameter('subid'));
		$csv_format	= mysql_real_escape_string($_POST['csv_format']);
		$confirmeds	= mysql_real_escape_string(str_replace("\r\n", "|", getPostParameter('confirmeds')));
		$pendings	= mysql_real_escape_string(str_replace("\r\n", "|", getPostParameter('pendings')));
		$declineds	= mysql_real_escape_string(str_replace("\r\n", "|", getPostParameter('declineds')));
		$status		= mysql_real_escape_string(getPostParameter('status'));

		/*
		if(!($csv_format && $confirmeds && $pendings && $status))
		{
			$errs[] = "Please fill in all fields";
		}
		*/

		if (count($errs) == 0)
		{
			$sql = "UPDATE cashbackengine_affnetworks SET ".$add_sql." csv_format='$csv_format', confirmeds='$confirmeds', pendings='$pendings', declineds='$declineds', status='$status' WHERE network_id='$network_id' LIMIT 1";

			if (smart_mysql_query($sql))
			{
				header("Location: affnetworks.php?msg=updated");
				exit();
			}
		}
		else
		{
			$errormsg = "";
			foreach ($errs as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}

	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$nid = (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_affnetworks WHERE network_id='$nid' LIMIT 1";
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
	}


	$title = "Edit Affiliate Network";
	require_once ("inc/header.inc.php");

?>
 
      <?php if ($total > 0) {

		  $row = mysql_fetch_array($result);
		  
      ?>

		<div id="addnew"><a href="affnetwork_edit.php?id=1" target="_blank">Settings example</a> / <a href="csv_examples/cj_report.csv" style="color: #87CE04"><img src="images/csv.png" align="absmiddle" /> CSV Report example</a></div>

        <h2><?php echo $row['network_name']; ?></h2>

		<?php if (isset($errormsg) && $errormsg != "") { ?>
			<div class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

        <form action="" method="post">
        <table width="100%" align="center" cellpadding="2" cellspacing="3"  border="0">
		<?php if ($row['image'] != "") { ?>
          <tr>
            <td colspan="2" valign="middle" align="center">
				<a target="_blank" href="<?php echo $row['website']; ?>"><img src="images/networks/<?php echo $row['image']; ?>" alt="<?php echo $row['network_name']; ?>" title="<?php echo $row['network_name']; ?>" border="0" /></a>
			</td>
          </tr>
		<?php }else{ ?>
          <tr>
            <td colspan="2" align="center" valign="top"><span style="font-size:18px; color:#5392D5;"><?php echo $row['network_name']; ?></span></td>
          </tr>
		<?php } ?>
          <tr>
            <td bgcolor="#F9F9F9" nowrap="nowrap" colspan="2" valign="top" align="center">
				<p>Please enter one record from your CSV-report in the field below. And replace needed values with our required variables.</p>
				<p>
					<table width="60%" align="center" cellpadding="2" cellspacing="3"  border="0">
						<tr valign="middle"><td align="right"><b>{TRANSACTIONID}</b></td><td align="left">- Transaction ID (Order ID)</td></tr>
						<tr valign="middle"><td align="right"><b>{PROGRAMID}</b></td><td align="left">- Program ID from your affiliate network</td></tr>
						<tr valign="middle"><td align="right"><b>{USERID}</b></td><td align="left">- SubID (SID)</td></tr>
						<tr valign="middle"><td align="right"><b>{AMOUNT}</b></td><td align="left">- Sale Amount</td></tr>
						<tr valign="middle"><td align="right"><b>{COMMISSION}</b></td><td align="left">- Commission Amount</td></tr>
						<tr valign="middle"><td align="right"><b>{STATUS}</b></td><td align="left">- Transaction Status</td></tr>
					</table>
				</p>
				<p>
					<table width="98%" align="center" cellpadding="2" cellspacing="2" border="0">
						<tr valign="middle"><td align="left"><font color="#FF9213"><b>CSV Format example:</b></font></td></tr>
						<tr valign="middle"><td align="left"><b>original row from your CSV report</b>:</td></tr>
						<tr valign="middle"><td bgcolor="#EFDFFB" align="left">"4th January 2015","Sale","945643431","sim_sale","closed","No","40.95","12.28","3201921","UNIBET","296"</td></tr>
						<tr valign="middle"><td align="left"><b>must be replaced with</b>:</td></tr>
						<tr valign="middle"><td bgcolor="#E3FBD8" align="left">"4th January 2015","Sale","<span style="color: #E72085;">{TRANSACTIONID}</span>","sim_sale","<span style="color: #E72085;">{STATUS}</span>","No","<span style="color: #E72085;">{AMOUNT}</span>","<span style="color: #E72085;">{COMMISSION}</span>","<span style="color: #E72085;">{PROGRAMID}</span>","UNIBET","<span style="color: #E72085;">{USERID}</span>"</td></tr>
					</table>				
				</p>
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">CSV Format:</td>
            <td valign="top"><textarea cols="120" rows="1" wrap="off" name="csv_format" class="textbox2"><?php echo stripslashes($row['csv_format']); ?></textarea></td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Statuses:<br/><span class="help">(one status per row)</span></td>
            <td align="center" valign="top">
				<table bgcolor="#F9F9F9" style="border: 1px solid #EEE" width="100%" align="left" cellpadding="2" cellspacing="3"  border="0">
					<tr>
						<td align="center" valign="top">
							<font color="#07D706"><b>Confirmed</b></font><br/><br/>
							<textarea wrap="off" cols="20" rows="2" name="confirmeds" class="textbox2"><?php echo str_replace("|", "\r\n", $row['confirmeds']); ?></textarea>
						<td>
						<td align="center" valign="top">
							<font color="#F37007"><b>Pending</b></font><br/><br/>
							<textarea wrap="off" cols="20" rows="2" name="pendings" class="textbox2"><?php echo str_replace("|", "\r\n", $row['pendings']); ?></textarea>
						<td>
						<td align="center" valign="top">
							<font color="#FF000A"><b>Declined</b></font><br/><br/>
							<textarea wrap="off" cols="20" rows="2" name="declineds" class="textbox2"><?php echo str_replace("|", "\r\n", $row['declineds']); ?></textarea>
						<td>
					</tr>
				</table>
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">SubID parameter:</td>
            <td valign="top"><input type="text" name="subid" id="subid" value="<?php echo $row['subid']; ?>" size="8" class="textbox" /><span class="note">SubID parameter name</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="networkid" id="networkid" value="<?php echo (int)$row['network_id']; ?>" />
				<input type="hidden" name="action" id="action" value="editnetwork" />
				<input type="submit" name="save" id="save" class="submit" value="Save Settings" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='affnetworks.php'" />
            </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<p align="center">
					Sorry, no network found.<br/><br/>
					<a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a>
				</p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>