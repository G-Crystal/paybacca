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

		$network_csv_format = getPostParameter('csv_format');
		$delimiter			= getPostParameter('delimiter');
		$num				= 0;

		if (!($_FILES['csv_file']['tmp_name']))
		{
			$errors[] = "Please select CSV file";
		}
		elseif (!$delimiter)
		{
			$errors[] = "Please select delimiter";
		}
		else
		{
			$csv_file = $_FILES['csv_file']['name'];

			if (preg_match('/\\.(csv)$/i', $csv_file) != 1)
			{
				$errors[] = "Please upload file with the extension .csv";
				@unlink($_FILES['photo']['tmp_name']);
			}
			elseif ($_FILES['csv_file']['size'] > 52428800)
			{
				$errors[] = "The file size is too big. It exceeds 50Mb.";
			}

			if (!(strstr($network_csv_format, "{TITLE}") && strstr($network_csv_format, "{CODE}") && strstr($network_csv_format, "{END_DATE}")))
			{
				$errors[] = "Sorry, you have wrong CSV format.";
			}
		}

		if (count($errors) == 0)
		{
			$csv = new parseCSV();
			
			$csv->delimiter = $delimiter;
			$separator = $csv->delimiter;

			$csv->parse($_FILES['csv_file']['tmp_name']);

			$network_csv_format = explode($separator, $network_csv_format);

			foreach ($network_csv_format as $k=>$value)
			{
				switch ($value)
				{
					case strstr($value, "{RETAILER}") == true:		$retailer_id = $k; break;
					case strstr($value, "{TITLE}") == true:			$title_id = $k; break;
					case strstr($value, "{CODE}") == true:			$code_id = $k; break;
					case strstr($value, "{LINK}") == true:			$link_id = $k; break;
					case strstr($value, "{START_DATE}") == true:	$start_date_id = $k; break;
					case strstr($value, "{END_DATE}") == true:		$end_date_id = $k; break;
					case strstr($value, "{DESCRIPTION}") == true:	$description_id = $k; break;
					case strstr($value, "{EXCLUSIVE}") == true:		$exclusive_id = $k; break;
					//case strstr($value, "{STATUS}") == true:		$status_id = $k; break;
				}
			}

			foreach ($csv->data as $key => $row)
			{
				$new_row = array_values($row);
				
				$retailer_e			= mysql_real_escape_string($new_row[$retailer_id]);
				$title_e			= mysql_real_escape_string($new_row[$title_id]);
				$code_e				= $new_row[$code_id];
				$link_e				= $new_row[$link_id];
				$start_date_e		= $new_row[$start_date_id];
				$end_date_e			= $new_row[$end_date_id];
				$description_e		= mysql_real_escape_string($new_row[$description_id]);
				$exclusive_e		= $new_row[$exclusive_id];
				//$status_e			= $new_row[$status_id];

				if ($title_e && $code_e && $end_date_e)
				{
					// check retailer name //
					if (is_numeric($retailer_e))
					{
						$retailer_e = $retailer_e;
					}
					else if (is_string($retailer_e))
					{
						$check_retailer = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE title='$retailer_e' LIMIT 1");
						if (mysql_num_rows($check_retailer) != 0)
						{
							$retailer_row = mysql_fetch_array($check_retailer);
							$retailer_e = $retailer_row['retailer_id'];
						}
						else
						{
							$retailer_e = 0;
						}
					}

					// check if exclusive coupon //
					switch ($exclusive_e)
					{
						case "yes": $exclusive_e = 1; break;
						case "no":	$exclusive_e = 0; break;
						case "1":	$exclusive_e = 1; break;
						case "0":	$exclusive_e = 0; break;
						default:	$exclusive_e = 0; break;
					}

					$check_result = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE retailer_id='$retailer_e' AND title='$title_e' AND code='$code_e'");

					if (mysql_num_rows($check_result) != 0)
					{
						$insert_sql = "UPDATE cashbackengine_coupons SET start_date='$start_date_e', end_date='$end_date_e', description='$description_e', exclusive='$exclusive_e' WHERE retailer_id='$retailer_e' AND title='$title_e' AND code='$code_e'";
					}
					else
					{
						$insert_sql = "INSERT INTO cashbackengine_coupons SET title='$title_e', retailer_id='$retailer_e', user_id='0', code='$code_e', link='$link_e', start_date='$start_date_e', end_date='$end_date_e', description='$description_e', exclusive='$exclusive_e', status='active', added=NOW()";
						$num++;
					}

					smart_mysql_query($insert_sql);
				}
			}

			header("Location: coupons_import.php?msg=done&num=".$num);
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}

	$title = "Import Coupons";
	require_once ("inc/header.inc.php");

?>
    
    <h2>Import Coupons</h2>

	<?php if (isset($errormsg)) { ?>
			<div class="error_box"><?php echo $errormsg; ?></div>
	<?php } elseif (isset($_GET['err']) && $_GET['err'] != "") { ?>
			<div class="error_box">
			<?php 
				switch ($_GET['err'])
				{
					case "delimiter": echo "Sorry, wrong delimiter. Please check your CSV file."; break;
				}
			?>
			</div>
	<?php } elseif (isset($_GET['msg']) && ($_GET['msg']) == "done") { ?>
			<div class="success_box"><span style="background: #31A506; color: #FFF; padding: 3px 8px;"><?php echo (int)$_GET['num']; ?></span> coupons have been added</div>
	<?php } ?>


		<form enctype="multipart/form-data" action="" method="post" name="form1">
        <table width="100%" style="background: #F9F9F9; padding: 10px;" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td nowrap="nowrap" align="left" valign="top">CSV format:</td>
			<td align="left" valign="top"><input type="text" class="textbox" name="csv_format" id="csv_format" value="<?php echo GetPostParameter('csv_format'); ?>" size="120" /><br/><small>example: {RETAILER},{TITLE},{CODE},{LINK},{START_DATE},{END_DATE},{DESCRIPTION},{EXCLUSIVE}</small></td>
		</tr>
		<tr>
			<td nowrap="nowrap" align="left" valign="middle">CSV file:</td>
			<td align="left" valign="middle">
				<input type="file" class="textbox2" name="csv_file" id="csv_file" value="" size="20" />
				<span style="margin-left: 10px;"><a href="csv_examples/coupons.csv" style="color: #87CE04"><img src="images/csv.png" align="absmiddle" /> example.csv</a></span>
			</td>
		</tr>
		<tr>
			<td align="left" valign="middle">Delimiter:</td>
			<td align="left" valign="middle">
				<select name="delimiter" class="textbox2">
					<option value="," <?php if ($delimiter == ",") echo "selected='selected'"; ?>>,</option>
					<option value="|" <?php if ($delimiter == "|") echo "selected='selected'"; ?>>|</option>
					<option value=";" <?php if ($delimiter == ";") echo "selected='selected'"; ?>>;</option>
				</select>			
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
		  		<input type="hidden" name="action" value="import" />
				<input type="submit" class="submit" name="upload" id="upload" value="Import Coupons" />
			</td>
		</tr>
        </table>
		</form>

<?php require_once ("inc/footer.inc.php"); ?>