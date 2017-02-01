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


	if (isset($_POST["action"]) && $_POST["action"] == "addmoney")
	{
			unset($errors);
			$errors = array();

			$transaction_id	= mysql_real_escape_string(getPostParameter('transaction_id'));
			$username		= mysql_real_escape_string(getPostParameter('username'));
			$amount			= mysql_real_escape_string(getPostParameter('amount'));
			$payment_type	= mysql_real_escape_string(getPostParameter('payment_type'));
			$retailer_id	= (int)getPostParameter('retailer_id');
			$cashback		= (int)getPostParameter('cashback');
			$status			= mysql_real_escape_string(getPostParameter('status'));
			$notification	= (int)getPostParameter('notification');

			if (!($username && $amount && $payment_type && $status))
			{
				$errors[] = "Please fill in all required fields";
			}
			else
			{
				if (!is_numeric($amount))
				{
					$errors[] = "Please enter correct amount";
					$amount = "";
				}

				if (is_numeric($username) && $username > 0)
					$ures = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE user_id='$username' LIMIT 1");
				else
					$ures = smart_mysql_query("SELECT * FROM cashbackengine_users WHERE username='$username' OR email='$username' LIMIT 1");

				if (mysql_num_rows($ures) == 0)
				{
					$errors[] = "Sorry, member not found";
				}
			}

		if (count($errors) == 0)
		{
				$urow = mysql_fetch_array($ures);

				$userid	= (int)$urow['user_id'];
				
				if ($transaction_id != "")
					$reference_id = $transaction_id;
				else
					$reference_id = GenerateReferenceID();

				switch ($status)
				{
					case "confirmed":	$status = "confirmed";	break;
					case "pending":		$status = "pending";	break;
					case "declined":	$status = "declined";	break;
					default:			$status = "confirmed";	break;
				}

				if ($retailer_id > 0)
				{
					$sresult = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' LIMIT 1");
					if (mysql_num_rows($sresult) > 0)
					{
						$srow = mysql_fetch_array($sresult);
						$retailer	= mysql_real_escape_string($srow['title']);
						$program_id = mysql_real_escape_string($srow['program_id']);
						$add_sql = "retailer_id='$retailer_id', retailer='$retailer', program_id='$program_id',";
					}
				}
		
				$sql = "INSERT INTO cashbackengine_transactions SET reference_id='$reference_id', ".$add_sql." user_id='$userid', payment_type='$payment_type', amount='$amount', status='$status', created=NOW(), process_date=NOW()";
				$result = smart_mysql_query($sql);

				if ($notification == 1)
				{
					// send email ///////////////////////////////////////////////////////////////
					// if (urow['newsletter'] == 1) //
					$etemplate = GetEmailTemplate('manual_credit');
					$esubject = $etemplate['email_subject'];
					$emessage = $etemplate['email_message'];

					$emessage = str_replace("{transaction_id}", $reference_id, $emessage);
					$emessage = str_replace("{first_name}", $urow['fname'], $emessage);
					$emessage = str_replace("{payment_type}", $payment_type, $emessage);
					$emessage = str_replace("{amount}", DisplayMoney($amount), $emessage);
					$to_email = $urow['fname'].' '.$urow['lname'].' <'.$urow['email'].'>';

					SendEmail($to_email, $esubject, $emessage, $noreply_mail = 1);
					//////////////////////////////////////////////////////////////////////////////
				}

				header("Location: money2user.php?msg=added");
				exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}

	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$squery = "SELECT * FROM cashbackengine_users WHERE user_id='$id' LIMIT 1";
		$sresult = smart_mysql_query($squery); 

		if (mysql_num_rows($sresult) != 0)
		{
			$srow = mysql_fetch_array($sresult);
			$username = $srow['username'];
		}
	}


	$title = "Credit Member";
	require_once ("inc/header.inc.php");

?>

		<h2><img src="images/icons/transfer.png" align="absmiddle" /> Manual Credit</h2>

		<p align="center">Here you have the ability to credit/withdraw funds to any account.</p>

		<?php if (isset($_GET['msg']) && ($_GET['msg']) == "added") { ?>
			<div class="success_box">Transaction has been successfully processed</div>
		<?php } ?>

		<?php if (isset($errormsg)) { ?>
			<div class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

		<form action="money2user.php" method="post" name="form1">
        <table width="100%" bgcolor="#F9F9F9" style="padding: 15px 0; border-radius: 5px; border: 1px solid #F5F5F5;" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td width="40%" align="right" valign="middle" class="tb1">Transaction ID:</td>
			<td align="left" valign="middle"><input type="text" class="textbox" name="transaction_id" value="<?php echo getPostParameter('transaction_id'); ?>" size="30" /><span class="note">will be auto generated if empty</span></td>
		</tr>
		<tr>
            <td align="right" valign="middle" class="tb1"><span class="req">* </span>Member:</td>
			<td align="left" valign="middle"><input type="text" class="textbox" name="username" value="<?php echo $username; ?>" size="30" required="required" /><span class="note">user ID or username or email</span></td>
		</tr>
		<tr>
			<td align="right" valign="middle" class="tb1"><span class="req">* </span>Amount:</td>
			<td align="left" valign="middle"><?php echo SITE_CURRENCY; ?><input type="text" class="textbox" name="amount" value="<?php echo $amount; ?>" size="6" required="required" /><span class="note">e.g. 50 or -100</span></td>
		</tr>
		<tr>
            <td align="right" valign="middle" class="tb1"><span class="req">* </span> Payment Type:</td>
			<td align="left" valign="middle"><input type="text" class="textbox" name="payment_type" value="Credit Account" size="30" required="required" /></td>
		</tr>
		<tr>
            <td align="right" valign="middle" class="tb1">Store:</td>
			<td align="left" valign="middle">
				<select name="retailer_id" id="retailer_id" style="width: 175px;" class="textbox2">
				<option value="">-----------</option>
				<?php
					$sql_retailers = smart_mysql_query("SELECT * FROM cashbackengine_retailers ORDER BY title ASC");
					if (mysql_num_rows($sql_retailers) > 0)
					{
						while ($row_retailers = mysql_fetch_array($sql_retailers))
						{
							if ($retailer_id == $row_retailers['retailer_id']) $selected = " selected=\"selected\""; else $selected = "";
							echo "<option value=\"".$row_retailers['retailer_id']."\"".$selected.">".$row_retailers['title']."</option>";
						}
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
            <td align="right" valign="middle" class="tb1">Status:</td>
			<td align="left" valign="middle">
				<select name="status" class="textbox2">
					<option value="confirmed" <?php if ($status == "confirmed") echo "selected='selected'"; ?>>confirmed</option>
					<option value="pending" <?php if ($status == "pending") echo "selected='selected'"; ?>>pending</option>
					<option value="declined" <?php if ($status == "declined") echo "selected='selected'"; ?>>declined</option>
				</select>
			</td>
		</tr>
		<!--
		<tr>
			<td align="right" valign="middle" class="tb1">&nbsp;</td>
            <td align="left" valign="middle">
				<input type="checkbox" class="checkbox" name="cashback" value="1" <?php if (@$cashback == 1) echo "checked=\"checked\""; ?> /> calculate cashback from store info
			</td>
		</tr>
		-->
		<tr>
			<td align="right" valign="middle" class="tb1">&nbsp;</td>
            <td align="left" valign="middle">
				<input type="checkbox" class="checkbox" name="notification" value="1" <?php if (!$_POST['action'] || @$notification == 1) echo "checked=\"checked\""; ?> /> send email notification to member
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
		  		<input type="hidden" name="action" value="addmoney" />
				<input type="submit" class="submit" name="addmoney" id="addmoney" value="Submit" />
			</td>
		</tr>
        </table>
		</form>

<?php require_once ("inc/footer.inc.php"); ?>