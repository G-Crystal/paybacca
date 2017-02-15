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

	require_once("./inc/parsecsv.inc.php");





	if (isset($_POST["action"]) && $_POST["action"] == "import")

	{

		unset($errors);

		$errors = array();



		unset($_SESSION['total_transactions'], $_SESSION['members_credited'], $_SESSION['total_cashback']);



		$network_id	= (int)getPostParameter('network_id');

		$delimiter	= getPostParameter('delimiter');

		$csv_text	= $_POST['csv_text'];



		if (!$network_id)

		{

			$errors[] = "Please select affiliate network";

		}

		elseif (!($_FILES['csv_report']['tmp_name'] || $csv_text != ""))

		{

			$errors[] = "Please select CSV-report file or paste CSV";

		}

		elseif (!$delimiter)

		{

			$errors[] = "Please select delimiter";

		}

		else

		{

			$csv_report	= $_FILES['csv_report']['name'];



			if ($_FILES['csv_report']['name'] && preg_match('/\\.(csv)$/i', $csv_report) != 1)

			{

				$errors[] = "Please upload a CSV-report with the extension .csv";

				@unlink($_FILES['photo']['tmp_name']);

			}

			elseif ($_FILES['csv_report']['size'] > 52428800)

			{

				$errors[] = "The file size is too big. It exceeds 50Mb.";

			}



			$aff_result = smart_mysql_query("SELECT * FROM cashbackengine_affnetworks WHERE network_id='$network_id' AND status='active' LIMIT 1");

			$aff_row = mysql_fetch_array($aff_result);



			$network_csv_format = stripslashes($aff_row['csv_format']);



			$row_transactionID	= "{TRANSACTIONID}";

			$row_programID		= "{PROGRAMID}";

			$row_userID			= "{USERID}";

			$row_amount			= "{AMOUNT}";

			$row_commission		= "{COMMISSION}";

			$row_status			= "{STATUS}";



			if (!(strstr($network_csv_format, $row_transactionID) && strstr($network_csv_format, $row_programID) && strstr($network_csv_format, $row_userID) && strstr($network_csv_format, $row_amount) && strstr($network_csv_format, $row_commission) && strstr($network_csv_format, $row_status)))

			{

				$errors[] = "Sorry, you have wrong settings for affiliate network. Firstly, please <a target='_blank' style='color:#5E5E5E;' href='affnetwork_edit.php?id=$network_id'>check settings</a>";

			}

		}



		if (count($errors) == 0)

		{

			$csv = new parseCSV();

			

			$csv->delimiter = $delimiter;

			$separator = $csv->delimiter;



			$csv->parse($_FILES['csv_report']['tmp_name']);



			if (!isset($separator) || $separator == "")

			{

				header("Location: csv_import.php?err=delimiter");

				exit();

			}



			$network_csv_format = explode($separator, $network_csv_format);



			foreach ($network_csv_format as $k=>$value)

			{

				switch ($value)

				{

					case strstr($value, "{TRANSACTIONID}") == true:		$trans_id = $k; break;

					case strstr($value, "{PROGRAMID}") == true:			$program_id = $k; break;

					case strstr($value, "{USERID}") == true:			$sub_id = $k; break;

					case strstr($value, "{AMOUNT}") == true:			$amount_id = $k; break;

					case strstr($value, "{COMMISSION}") == true:		$commission_id = $k; break;

					case strstr($value, "{STATUS}") == true:			$status_id = $k; break;

				}

			}



			if (count($csv->data) > 0)

			{

				$alldata = array();

				$alldata = $csv->data;

			}

			else

			{

				$alldata = array();

				$alldatas = array();

				$alldatas = explode("\n", $csv_text);

				$alldatas = array_filter($alldatas, 'trim');

				

				foreach ($alldatas as $k=>$v)

				{

					$alldata[] = explode($delimiter, $v); 

				}

			}



			foreach ($alldata as $key => $row)

			{

				$new_row = array_values($row);

				

				$transaction_id_e	= $new_row[$trans_id];

				$program_id_e		= mysql_real_escape_string($new_row[$program_id]);

				$subid_e			= mysql_real_escape_string($new_row[$sub_id]);

				$amount_e			= preg_replace("/[^0-9,.]/", "", $new_row[$amount_id]);

				$commission_e		= preg_replace("/[^0-9,.]/", "", $new_row[$commission_id]);

				$amount_e			= str_replace(",",".",$amount_e);

				$commission_e		= str_replace(",",".",$commission_e);

				$status_e			= strtolower($new_row[$status_id]);



				if (!is_numeric($amount_e) || !is_numeric($commission_e))

				{

					header("Location: csv_import.php?err=amount");

					exit();

				}



				///  Convert Statuses ///

				$confirmed_statuses = explode("|", $aff_row['confirmeds']);

				$pending_statuses = explode("|", $aff_row['pendings']);

				$declined_statuses = explode("|", $aff_row['declineds']);



				if (in_array($status_e, array_map('strtolower', $confirmed_statuses)))

					$cashbackengine_status = "confirmed";

				elseif (in_array($status_e, array_map('strtolower', $pending_statuses)))

					$cashbackengine_status = "pending";

				elseif (in_array($status_e, array_map('strtolower', $declined_statuses)))

					$cashbackengine_status = "declined";

				else

					$cashbackengine_status = "unknown";





				if (!empty($cashbackengine_status))

				{

					$cashback_result = smart_mysql_query("SELECT cashback FROM cashbackengine_retailers WHERE network_id='$network_id' AND (title='$program_id_e' OR program_id='$program_id_e') LIMIT 1");

					$cashback_row = mysql_fetch_array($cashback_result);



					$cashback_store = mysql_real_escape_string($cashback_row['title']);

					$cashback		= $cashback_row['cashback'];



					$total_transactions	= 0;

					$total_cashback		= 0;

					unset($total_members);

					$total_members = array();



					if ($cashback != "")

					{

						if (strstr($cashback, '%'))

						{

							$member_money = CalculatePercentage($commission_e, CASHBACK_COMMISSION);

						}

						else

						{

							$member_money = $cashback;

						}



						if ($commission_e < $member_money)

						{

							$cashbackengine_status = "incomplete";

							$reason = "too hight cashback value";

						}



						if ($cashbackengine_status == "unknown")

						{

							$cashbackengine_status = "incomplete";

							$reason = "unknown transaction status";

						}

				

						$check_transaction_result = smart_mysql_query("SELECT * FROM cashbackengine_transactions WHERE reference_id='$transaction_id_e' AND network_id='$network_id' AND program_id='$program_id_e' AND user_id='$subid_e'");



						if (mysql_num_rows($check_transaction_result) != 0)

						{

							$transaction_query = "UPDATE cashbackengine_transactions SET amount='$member_money', status='$cashbackengine_status', reason='$reason', updated=NOW() WHERE reference_id='$transaction_id_e' AND network_id='$network_id' AND program_id='$program_id_e' AND status <>'confirmed'";

						}

						else

						{

							$transaction_query = "INSERT INTO cashbackengine_transactions SET reference_id='$transaction_id_e', network_id='$network_id', retailer='$cashback_store', program_id='$program_id_e', user_id='$subid_e', payment_type='Cashback', amount='$member_money', status='$cashbackengine_status', reason='$reason', created=NOW(), updated=NOW()";



							$total_cashback += $member_money;

						}

						

						$total_members[$subid_e] = 1;

						$total_transactions++;



						smart_mysql_query($transaction_query);

					}

				}

			}



			smart_mysql_query("UPDATE cashbackengine_affnetworks SET last_csv_upload=NOW() WHERE network_id='$network_id' LIMIT 1");



			$_SESSION['total_transactions'] = $total_transactions;

			$_SESSION['members_credited']	= count($total_members);

			$_SESSION['total_cashback']		= DisplayMoney($total_cashback);



			header("Location: csv_import.php?msg=done");

			exit();

		}

		else

		{

			$errormsg = "";

			foreach ($errors as $errorname)

				$errormsg .= "&#155; ".$errorname."<br/>";

		}

	}



	$title = "Upload CSV-report";

	require_once ("inc/header.inc.php");



?>



	<div id="addnew"><a style="color: #87CE04;" href="http://www.cashbackengine.net/c/myproducts.php" target="_blank"><img src="images/icons/addons.png" align="absmiddle" /> <b>Buy Addons</b></a></div>



    <h2>Upload CSV-report</h2>



	<p align="center"><img src="images/icons/csv.gif" /></p>

	<p align="center">Here you have the ability to update your members accounts via uploading CSV-report from your affiliate networks.</p>

	

	<p align="center">

		<b><u>IMPORTANT</u></b>: Before you start uploading report, please make sure you have correct <a href="affnetworks.php">affiliate network settings</a>.<br/>

		<!--Also you need to use reports with same currency as on <a href="settings.php">site settings</a> page.-->

	</p>



	<?php if (isset($errormsg)) { ?>

			<div style="width:80%;" class="error_box"><?php echo $errormsg; ?></div>

	<?php } elseif (isset($_GET['err']) && $_GET['err'] != "") { ?>

			<div style="width:80%;" class="error_box">

				<?php 

					switch ($_GET['err'])

					{

						case "delimiter": echo "Sorry, wrong delimiter in your CSV report. Please check your CSV file."; break;

						case "amount": echo "Wrong amount and commission values, please check <a style='color:#5E5E5E;' href='affnetworks.php?id=$network_id'>affiliate network settings</a>"; break;

					}

				?>

			</div>

	<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "done") { ?>

			<div style="width:80%;" class="success_box">Your CSV has been successfully uploaded</div>

			 <table width="80%" bgcolor="#F9F9F9" style="padding: 5px;" align="center" cellpadding="3" cellspacing="0" border="0">

			 <tr>

				<td style="padding: 10px;" nowrap="nowrap" align="left" valign="middle">

					<p align="center"><b>Upload Statistics</b></p>

					Transactions: <b><?php echo $_SESSION['total_transactions']; ?></b><br/>

					Users credited: <b><?php echo $_SESSION['members_credited']; ?></b><br/>

					Cash Back: <b><?php echo $_SESSION['total_cashback']; ?></b>

				</td>

			</tr>

			</table>

			<br/>

	<?php } ?>



		<form enctype="multipart/form-data" action="" method="post" name="form1">

        <table bgcolor="#F9F9F9" style="padding: 5px;" width="85%" align="center" cellpadding="3" cellspacing="0" border="0">

		<tr>

			<th>Affiliate Network</th>

			<th>CSV file <span style="padding-left: 20px; font-weight: normal;"><a href="csv_examples/cj_report.csv" style="color: #87CE04"><img src="images/csv.png" align="absmiddle" /> example</a></span></th>

			<th>Delimiter</th>

		</tr>

		<tr>

            <td nowrap="nowrap" align="center" valign="middle">

				<select name="network_id">

				<option value="">-- Select Affiliate Network --</option>

				<?php



					$sql_affs = smart_mysql_query("SELECT * FROM cashbackengine_affnetworks WHERE status='active' ORDER BY network_name ASC");

				

					while ($row_affs = mysql_fetch_array($sql_affs))

					{

						if ($network_id == $row_affs['network_id']) $selected = " selected=\"selected\""; else $selected = "";



						echo "<option value=\"".$row_affs['network_id']."\"".$selected.">".$row_affs['network_name']."</option>";

					}

				?>					

				</select>

			</td>

            <td nowrap="nowrap" align="center" valign="middle">

				<input type="file" class="textbox2" name="csv_report" id="csv_report" value="" size="25" />

			</td>

            <td nowrap="nowrap" align="center" valign="middle">

				<select name="delimiter">

					<option value=",">, (comma)</option>

					<option value="\t">Tab</option>

					<option value="|">| (pipe)</option>

					<option value=";">;</option>

				</select>

			</td>

		</tr>

		<tr>

			<td style="border-top: 1px solid #DCEAFB;" colspan="3" align="left" valign="top">

				<p>Or paste CSV here:</p>

				<textarea name="csv_text" cols="115" rows="10" style="width: 630px;" class="textbox2"><?php echo ($_FILES['csv_report']['tmp_name']) ? "" : getPostParameter('csv_text'); ?></textarea>

			</td>

		</tr>

		<tr>

			<td colspan="3" align="center" valign="top">

				<input type="hidden" name="action" value="import" />

				<input type="submit" class="submit" name="uploadreport" id="uploadreport" value="Process CSV" />

			</td>

		</tr>

        </table>

		</form>



<?php require_once ("inc/footer.inc.php"); ?>