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


	$pn = (int)$_GET['pn'];


	if (isset($_POST["action"]) && $_POST["action"] == "edit")
	{
			unset($errors);
			$errors = array();

			$retailer_id		= (int)getPostParameter('rid');
			$network_id			= (int)getPostParameter('network_id');
			$program_id			= mysql_real_escape_string(getPostParameter('program_id'));
			$category			= array();
			$category			= $_POST['category_id'];
			$country			= array();
			$country			= $_POST['country_id'];
			$rname				= mysql_real_escape_string(getPostParameter('rname'));
			$img				= mysql_real_escape_string(trim($_POST['image_url']));
			$img_save			= (int)getPostParameter('image_save');
			$url				= mysql_real_escape_string(trim($_POST['url']));
			$old_cashback		= mysql_real_escape_string(getPostParameter('old_cashback'));
			$old_cashback_sign	= mysql_real_escape_string(getPostParameter('old_cashback_sign'));
			$cashback			= mysql_real_escape_string(getPostParameter('cashback'));
			$cashback_sign		= mysql_real_escape_string(getPostParameter('cashback_sign'));
			$description		= mysql_real_escape_string($_POST['description']);
			$conditions			= mysql_real_escape_string(nl2br(getPostParameter('conditions')));
			$website			= mysql_real_escape_string(getPostParameter('website'));
			if ($website != "" && !strstr($website, 'http://') && !strstr($website, 'https://')) $website = "http://".$website;
			$tags				= mysql_real_escape_string(getPostParameter('tags'));
			$seo_title			= mysql_real_escape_string(getPostParameter('seo_title'));
			$meta_description	= mysql_real_escape_string(getPostParameter('meta_description'));
			$meta_keywords		= mysql_real_escape_string(getPostParameter('meta_keywords'));
			$end_date			= mysql_real_escape_string(getPostParameter('end_date'));
			$end_time			= mysql_real_escape_string(getPostParameter('end_time'));
			$retailer_end_date	= $end_date." ".$end_time;
			$featured			= (int)getPostParameter('featured');
			$deal_of_week		= (int)getPostParameter('deal_of_week');
			$status				= mysql_real_escape_string(getPostParameter('status'));

			if (!($rname && $url && $status))
			{
				$errors[] = "Please ensure that all fields marked with an asterisk are complete";
			}
			else
			{
				if ($img == "")
				{
					$img = "noimg.gif";
				}

				if (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://')
				{
					$errors[] = "Enter correct url format, enter the 'http://' or 'https://' statement before your link";
				}
				elseif ($url == 'http://' || $url == 'https://')
				{
					$errors[] = "Please enter correct URL";
				}

				/*
				if (isset($network_id) && $network_id != "")
				{
					if (!$program_id || $program_id == "")
						$errors[] = "Please enter Program ID (Merchant ID)";
				}
				*/

				if (isset($cashback) && $cashback != "" && !is_numeric($cashback))
				{
					$errors[] = "Please enter correct cashback value (digits only)";
				}
			}

			if (isset($cashback) && is_numeric($cashback))
			{
				switch ($old_cashback_sign)
				{
					case "currency":	$old_cashback_sign = ""; break;
					case "%":			$old_cashback_sign = "%"; break;
					case "points":		$old_cashback_sign = "points"; break;
				}

				switch ($cashback_sign)
				{
					case "currency":	$cashback_sign = ""; break;
					case "%":			$cashback_sign = "%"; break;
					case "points":		$cashback_sign = "points"; break;
				}
				
				if ($old_cashback != "") $retailer_old_cashback	= $old_cashback.$old_cashback_sign;
				$retailer_cashback = $cashback.$cashback_sign;
			}
			else
			{
				$old_cashback = "";
				$retailer_cashback = "";
			}


			if (count($errors) == 0)
			{
				// download store image
				if ($img_save == 1 && (strstr($img, 'http://') || strstr($img, 'https://')))
				{
					$current_image = mysql_fetch_array(smart_mysql_query("SELECT image FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' LIMIT 1"));
					if (file_exists(IMAGES_PATH.$current_image['image'])) @unlink(IMAGES_PATH.$current_image['image']);

					if (preg_match('/https?:\/\/.*\.png$/i', $img)) {
						$img_type = 'png';
					}
					else if (preg_match('/https?:\/\/.*\.(jpg|jpeg)$/i', $img)) {
						$img_type = 'jpg';
					}
					else if (preg_match('/https?:\/\/.*\.gif$/i', $img)) {
						$img_type = 'gif';
					}
					else
					{
						$img_type = 'jpg';
					}

					$new_img = time().rand(1,100).".".$img_type;
					@file_put_contents(IMAGES_PATH.$new_img, file_get_contents($img));
					$img = $new_img;
				}

				smart_mysql_query("UPDATE cashbackengine_retailers SET title='$rname', network_id='$network_id', program_id='$program_id', url='$url', image='$img', old_cashback='$retailer_old_cashback', cashback='$retailer_cashback', conditions='$conditions', description='$description', website='$website', tags='$tags', seo_title='$seo_title', meta_description='$meta_description', meta_keywords='$meta_keywords', end_date='$retailer_end_date', featured='$featured', deal_of_week='$deal_of_week', status='$status' WHERE retailer_id='$retailer_id' LIMIT 1");

				smart_mysql_query("DELETE FROM cashbackengine_retailer_to_category WHERE retailer_id='$retailer_id'");
				if (count($category) > 0)
				{
					foreach ($category as $cat_id)
					{
						$cats_insert_sql = "INSERT INTO cashbackengine_retailer_to_category SET retailer_id='$retailer_id', category_id='$cat_id'";
						smart_mysql_query($cats_insert_sql);
					}
				}

				smart_mysql_query("DELETE FROM cashbackengine_retailer_to_country WHERE retailer_id='$retailer_id'");
				if (count($country) > 0)
				{
					foreach ($country as $country_id)
					{
						$countries_insert_sql = "INSERT INTO cashbackengine_retailer_to_country SET retailer_id='$retailer_id', country_id='$country_id'";
						smart_mysql_query($countries_insert_sql);
					}
				}

				header("Location: retailers.php?msg=updated");
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
		$id	= (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$id' LIMIT 1";
		$rs	= smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	$title = "Edit Retailer";
	require_once ("inc/header.inc.php");

?>

    <h2>Edit Retailer</h2>

	<?php if ($total > 0) {
		
		$row = mysql_fetch_array($rs);

	?>

		<script type="text/javascript">
		<!--
			function hiddenDiv(id,showid){
				if(document.getElementById(id).value != ""){
					document.getElementById(showid).style.display = "";
				}else{
					document.getElementById(showid).style.display = "none";
				}
			}
		-->
		</script>

		<?php if (isset($errormsg) && $errormsg != "") { ?>
			<div class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

      <div style="position: absolute; right: 10px; margin-top: 5px; padding: 5px;">
		<div align="right"><font color="red">* denotes required field</font></div><br/>
		<img src="<?php if (!stristr($row['image'], 'http')) echo "/img/"; echo $row['image']; ?>" width="120" height="60" align="left" alt="" title="" border="0" />
      </div>

      <form action="" method="post" name="form1">
        <table bgcolor="#F9F9F9" width="100%" cellpadding="2" cellspacing="3"  border="0" align="center">
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td width="70%" valign="top"><input type="text" name="rname" id="rname" value="<?php echo $row['title']; ?>" size="62" class="textbox" /></td>
			</tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Affiliate Network:</td>
            <td valign="top">
			<select class="textbox2" id="network_id" name="network_id" onchange="javascript:hiddenDiv('network_id','program_id')" <?php if ($network_id) echo "style='display: block;'"; ?> style="width: 130px;">
				<option value="">-----------------------</option>
				<?php

					$sql_affs = smart_mysql_query("SELECT * FROM cashbackengine_affnetworks WHERE status='active' ORDER BY network_name ASC");
				
					while ($row_affs = mysql_fetch_array($sql_affs))
					{
						if ($row['network_id'] == $row_affs['network_id']) $selected = " selected=\"selected\""; else $selected = "";

						echo "<option value=\"".$row_affs['network_id']."\"".$selected.">".$row_affs['network_name']."</option>";
					}
				?>	
			</select>
			</td>
          </tr>
          <tr id="program_id" <?php if ($row['network_id'] == 0 && !$_POST['action']) { ?>style="display: none;" <?php } ?>>
            <td valign="middle" align="right" class="tb1">Program ID:</td>
            <td valign="top"><input type="text" name="program_id" value="<?php echo $row['program_id']; ?>" size="21" class="textbox" /><span class="note">Merchant ID from affiliate network</span></td>
          </tr>
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1">Category:</td>
            <td width="70%" valign="top">
				<div class="scrollbox">
				<?php

					unset($retailer_cats);
					$retailer_cats = array();

					$sql_retailer_cats = smart_mysql_query("SELECT category_id FROM cashbackengine_retailer_to_category WHERE retailer_id='$id'");

					if (mysql_num_rows($sql_retailer_cats) > 0)
					{
						while ($row_retailer_cats = mysql_fetch_array($sql_retailer_cats))
						{
							$retailer_cats[] = $row_retailer_cats['category_id'];
						}
					}

					$cc = 0;
					$allcategories = array();
					$allcategories = CategoriesList(0);
					foreach ($allcategories as $category_id => $category_name)
					{
						$cc++;
						if (is_array($retailer_cats) && in_array($category_id, $retailer_cats)) $checked = 'checked="checked"'; else $checked = '';

						if (($cc%2) == 0)
							echo "<div class=\"even\"><input name=\"category_id[]\" value=\"".(int)$category_id."\" ".$checked." type=\"checkbox\">".$category_name."</div>";
						else
							echo "<div class=\"odd\"><input name=\"category_id[]\" value=\"".(int)$category_id."\" ".$checked." type=\"checkbox\">".$category_name."</div>";
					}

				?>
				</div>
			</td>
			</tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Country:</td>
            <td valign="top">
				<div class="scrollbox">
				<?php

					unset($retailer_countries);
					$retailer_countries = array();

					$sql_retailer_countries = smart_mysql_query("SELECT country_id FROM cashbackengine_retailer_to_country WHERE retailer_id='$id'");		
					
					while ($row_retailer_countries = mysql_fetch_array($sql_retailer_countries))
					{
						$retailer_countries[] = $row_retailer_countries['country_id'];
					}

					$cc = 0;
					$sql_country = "SELECT * FROM cashbackengine_countries WHERE status='active' ORDER BY sort_order, name";
					$rs_country = smart_mysql_query($sql_country);
					$total_country = mysql_num_rows($rs_country);

					if ($total_country > 0)
					{
						while ($row_country = mysql_fetch_array($rs_country))
						{
							$cc++;
							if (is_array($retailer_countries) && in_array($row_country['country_id'], $retailer_countries)) $checked = 'checked="checked"'; else $checked = '';

							if (($cc%2) == 0)
								echo "<div class=\"even\"><input name=\"country_id[]\" value=\"".(int)$row_country['country_id']."\" ".$checked." type=\"checkbox\">".$row_country['name']."</div>";
							else
								echo "<div class=\"odd\"><input name=\"country_id[]\" value=\"".(int)$row_country['country_id']."\" ".$checked." type=\"checkbox\">".$row_country['name']."</div>";
						}
					}

				?>
				</div>
			</td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Image URL:</td>
				<td align="left" valign="top"><input type="text" name="image_url" class="textbox" value="<?php echo $row['image']; ?>" size="62" /> <input type="checkbox" class="checkbox" name="image_save" value="1" <?php if (@$image_url == 1) echo "checked=\"checked\""; ?> /> <img src="images/icons/download.png" /> download image <sup title="you must have allow_url_fopen set to on">?<sup></td>
			</tr>
            <tr>
				<td valign="middle" align="right" class="tb1"><span class="req">* </span>URL:</td>
				<td nowrap="nowrap" valign="top">
					<input type="text" name="url" id="url" value="<?php echo $row['url']; ?>" size="100" class="textbox" />
					<!--<br/><span style="color: #838383">You can add subID parameter to your affiliate link to track members.</span>-->
				</td>
			</tr>
			<?php
					if (strstr($row['old_cashback'], '%'))
					{
						$old_cashback = str_replace('%','',$row['old_cashback']);
						$old_selected1 = "";
						$old_selected2 = "selected";
					}
					elseif (strstr($row['old_cashback'], 'points'))
					{
						$old_cashback = str_replace('points','',$row['old_cashback']);
						$old_selected1 = $old_selected2 = "";
						$old_selected3 = "selected";
					}
					else
					{
						$old_cashback = $row['old_cashback'];
						$old_selected2 = $old_selected3 = "";
						$old_selected1 = "selected";
					}
			?>
            <tr>
				<td valign="middle" align="right" class="tb1">Old Cashback:</td>
				<td valign="top">
					<input type="text" name="old_cashback" id="old_cashback" value="<?php echo $old_cashback; ?>" size="4" class="textbox" />
					 <select name="old_cashback_sign" class="textbox2">
						<option value="%" <?php echo $old_selected2; ?>>%</option>
						<option value="currency" <?php echo $old_selected1; ?>><?php echo SITE_CURRENCY; ?></option>
						<option value="points" <?php echo $old_selected3; ?>>points</option>
					</select>
				</td>
			</tr>
			<?php
					if (strstr($row['cashback'], '%'))
					{
						$cashback = str_replace('%','',$row['cashback']);
						$selected1 = $selected3 = "";
						$selected2 = "selected";
					}
					elseif (strstr($row['cashback'], 'points'))
					{
						$cashback = str_replace('points','',$row['cashback']);
						$selected1 = $selected2 = "";
						$selected3 = "selected";
					}
					else
					{
						$cashback = $row['cashback'];
						$selected2 = $selected3 = "";
						$selected1 = "selected";
					}
			?>
            <tr>
				<td valign="middle" align="right" class="tb1">Cashback:</td>
				<td valign="top">
					<input type="text" name="cashback" id="cashback" value="<?php echo $cashback; ?>" size="4" class="textbox" />
					 <select name="cashback_sign" class="textbox2">
						<option value="%" <?php echo $selected2; ?>>%</option>
						<option value="currency" <?php echo $selected1; ?>><?php echo SITE_CURRENCY; ?></option>
						<option value="points" <?php echo $selected3; ?>>points</option>
					</select>
				</td>
			</tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Description:</td>
				<td valign="top"><textarea name="description" id="editor1" cols="75" rows="8" class="textbox2"><?php echo stripslashes($row['description']); ?></textarea></td>
            </tr>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor1' );
				</script>
            <tr>
				<td valign="middle" align="right" class="tb1">Conditions:</td>
				<td valign="top"><textarea name="conditions" cols="112" rows="4" style="width:590px;" class="textbox2"><?php echo strip_tags($row['conditions']); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Tags:</td>
				<td valign="top"><input type="text" name="tags" id="tags" value="<?php echo $row['tags']; ?>" size="115" style="width:590px;" class="textbox" /></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Website:</td>
				<td valign="top"><input type="text" name="website" id="website" value="<?php echo $row['website']; ?>" size="40" class="textbox" /></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">SEO Title:</td>
				<td valign="top"><input type="text" name="seo_title" id="seo_title" value="<?php echo $row['seo_title']; ?>" size="115" style="width:590px;" class="textbox" /></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="112" rows="2" style="width:590px;" class="textbox2"><?php echo strip_tags($row['meta_description']); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo $row['meta_keywords']; ?>" size="115" style="width:590px;" class="textbox" /></td>
            </tr>
			<script>
				$(function() {
			        $('#end_date').calendricalDate();
			        $('#end_time').calendricalTime({
						minTime: {hour: 0, minute: 0},
						maxTime: {hour: 23, minute: 59},
						timeInterval: 30
					})
				});
			</script>
            <tr>
				<td valign="middle" align="right" class="tb1">Expiry Date:</td>
				<td valign="middle"><input type="text" name="end_date" id="end_date" value="<?php echo ($row['end_date'] != "0000-00-00 00:00:00") ? substr($row['end_date'], 0, 10) : ""; ?>" size="10" maxlength="10" class="textbox" />&nbsp; <input type="text" name="end_time" id="end_time" value="<?php echo ($row['end_date'] != "0000-00-00 00:00:00") ? substr($row['end_date'], -8, 5) : ""; ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Featured?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="featured" value="1" <?php if ($row['featured'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Store of the Week?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="deal_of_week" value="1" <?php if ($row['deal_of_week'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status" class="textbox2">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
					<option value="expired" <?php if ($row['status'] == "expired") echo "selected"; ?>>expired</option>
				</select>
			</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="hidden" name="rid" id="rid" value="<?php echo (int)$row['retailer_id']; ?>" />
				<input type="hidden" name="action" id="action" value="edit">
				<input type="submit" class="submit" name="update" id="update" value="Update Retailer" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='retailers.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
              </td>
            </tr>
          </table>
      </form>

      <?php }else{ ?>
			<p align="center">Sorry, no retailer found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>