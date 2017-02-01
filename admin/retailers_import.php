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

			if (!(strstr($network_csv_format, "{TITLE}") && strstr($network_csv_format, "{NETWORK}") && strstr($network_csv_format, "{IMAGE_URL}") && strstr($network_csv_format, "{URL}") && strstr($network_csv_format, "{CASHBACK}") && strstr($network_csv_format, "{CASHBACK_SIGN}")))
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
					case strstr($value, "{TITLE}") == true:				$title_id = $k; break;
					case strstr($value, "{NETWORK}") == true:			$network_id = $k; break;
					case strstr($value, "{PROGRAM}") == true:			$program_id = $k; break;
					case strstr($value, "{CATEGORY}") == true:			$category_id = $k; break;
					case strstr($value, "{IMAGE_URL}") == true:			$image_id = $k; break;
					case strstr($value, "{URL}") == true:				$url_id = $k; break;
					case strstr($value, "{CASHBACK}") == true:			$cashback_id = $k; break;
					case strstr($value, "{CASHBACK_SIGN}") == true:		$cashback_sign_id = $k; break;
					case strstr($value, "{DESCRIPTION}") == true:		$description_id = $k; break;
					case strstr($value, "{CONDITIONS}") == true:		$conditions_id = $k; break;
					case strstr($value, "{FEATURED}") == true:			$featured_id = $k; break;
					//case strstr($value, "{STATUS}") == true:			$status_id = $k; break;
				}
			}

			foreach ($csv->data as $key => $row)
			{
				$new_row = array_values($row);
				
				$title_e			= mysql_real_escape_string($new_row[$title_id]);
				$network_e			= mysql_real_escape_string($new_row[$network_id]);
				$program_e			= $new_row[$program_id];
				$category_e			= mysql_real_escape_string($new_row[$category_id]);
				$image_e			= $new_row[$image_id];
				$url_e				= $new_row[$url_id];
				$cashback_e			= $new_row[$cashback_id];
				$cashback_sign_e	= $new_row[$cashback_sign_id];
				$description_e		= mysql_real_escape_string($new_row[$description_id]);
				$conditions_e		= mysql_real_escape_string($new_row[$conditions_id]);
				$featured_e			= $new_row[$featured_id];
				//$status_e			= $new_row[$status_id];

				switch ($network_e)
				{
					case "CJ":			$url_e .= "&sid={USERID}"; break;
					case "Linkshare":	$url_e .= "&u1={USERID}"; break;
				}

				if ($title_e && $network_e && $image_e && $url_e && $cashback_e && $cashback_sign_e)
				{
					// check network name //
					if (is_numeric($network_e))
					{
						$network_e = $network_e;
					}
					else if (is_string($network_e))
					{
						$check_network = smart_mysql_query("SELECT * FROM cashbackengine_affnetworks WHERE network_name='$network_e' LIMIT 1");
						if (mysql_num_rows($check_network) != 0)
						{
							$network_row = mysql_fetch_array($check_network);
							$network_e = $network_row['network_id'];
						}
						else
						{
							$network_e = 0;
						}
					}

					$check_result = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE title='$title_e' AND network_id='$network_e' AND program_id='$program_e'");

					if (mysql_num_rows($check_result) != 0)
					{
						$insert_sql = "UPDATE cashbackengine_retailers SET url='$url_e', image='$image_e', cashback='".$cashback_e.$cashback_sign_e."', conditions='$conditions_e', description='$description_e', featured='$featured_e' WHERE title='$title_e' AND network_id='$network_e' AND program_id='$program_e'";
						$result = smart_mysql_query($insert_sql);
					}
					else
					{
						$insert_sql = "INSERT INTO cashbackengine_retailers SET title='$title_e', network_id='$network_e', program_id='$program_e', url='$url_e', image='$image_e', cashback='".$cashback_e.$cashback_sign_e."', conditions='$conditions_e', description='$description_e', featured='$featured_e', deal_of_week='0', status='active', added=NOW()";
						$result = smart_mysql_query($insert_sql);
						$new_retailer_id = mysql_insert_id();

						$num++;

						// check category name //
						if ($category_e != "")
						{
							if (is_numeric($category_e))
							{
								$cats_insert_sql = "INSERT INTO cashbackengine_retailer_to_category SET retailer_id='$new_retailer_id', category_id='$category_e'";
								smart_mysql_query($cats_insert_sql);
							}
							else if (is_string($category_e))
							{
								unset($categories_list);
								$categories_list = array();

								if (strstr($category_e, ";"))
								{
									$categories_list = explode(";", $category_e);
								}
								else
								{
									$categories_list[] = $category_e;
								}

								foreach ($categories_list as $kk=>$category_name)
								{
									$check_category = smart_mysql_query("SELECT * FROM cashbackengine_categories WHERE name='$category_name' LIMIT 1");
									if (mysql_num_rows($check_category) != 0)
									{
										$category_row = mysql_fetch_array($check_category);
										smart_mysql_query("INSERT INTO cashbackengine_retailer_to_category SET retailer_id='$new_retailer_id', category_id='".$category_row['category_id']."'");
									}
									else
									{
										$new_category_sql = "INSERT INTO cashbackengine_categories SET parent_id='0', name='$category_name'";
										$new_category_result = smart_mysql_query($new_category_sql);
										$new_category_id = mysql_insert_id();

										smart_mysql_query("INSERT INTO cashbackengine_retailer_to_category SET retailer_id='$new_retailer_id', category_id='$new_category_id'");
									}
								}
							}
						}
					}
				}
			}

			header("Location: retailers_import.php?msg=done&num=".$num);
			exit();
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}

	$title = "Import Retailers";
	require_once ("inc/header.inc.php");

?>
    
    <h2>Import Retailers</h2>

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
			<div class="success_box"><span style="background: #31A506; color: #FFF; padding: 3px 8px;"><?php echo (int)$_GET['num']; ?></span> retailers have been added</div>
	<?php } ?>


		<form enctype="multipart/form-data" action="" method="post" name="form1">
        <table width="100%" style="background: #F9F9F9; padding: 10px;" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td nowrap="nowrap" align="left" valign="top">CSV format:</td>
			<td align="left" valign="top"><input type="text" class="textbox" name="csv_format" id="csv_format" value="<?php echo GetPostParameter('csv_format'); ?>" size="120" /><br/><small>example:<br/> {TITLE},{NETWORK},{PROGRAM},{CATEGORY},{IMAGE_URL},{URL},{CASHBACK},{CASHBACK_SIGN},{DESCRIPTION},{CONDITIONS}</small></td>
		</tr>
		<tr>
			<td nowrap="nowrap" align="left" valign="middle">CSV file:</td>
			<td align="left" valign="middle">
				<input type="file" class="textbox2" name="csv_file" id="csv_file" value="" size="20" />
				<span style="margin-left: 10px;"><a href="csv_examples/retailers.csv" style="color: #87CE04"><img src="images/csv.png" align="absmiddle" /> example.csv</a></span>
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
				<input type="submit" class="submit" name="upload" id="upload" value="Import Retailers" />
			</td>
		</tr>
        </table>
		</form>

<?php require_once ("inc/footer.inc.php"); ?>