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

			$coupon_id		= (int)getPostParameter('couponid');
			$coupon_type	= mysql_real_escape_string(getPostParameter('coupon_type'));
			$coupon_title	= mysql_real_escape_string(getPostParameter('coupon_title'));
			$retailer_id	= (int)getPostParameter('retailer_id');
			$code			= mysql_real_escape_string(getPostParameter('code'));
			$link			= mysql_real_escape_string(getPostParameter('link'));
			$start_date		= mysql_real_escape_string(getPostParameter('start_date'));
			$start_time		= mysql_real_escape_string(getPostParameter('start_time'));
			$end_date		= mysql_real_escape_string(getPostParameter('end_date'));
			$end_time		= mysql_real_escape_string(getPostParameter('end_time'));
			$coupon_start_date	= $start_date." ".$start_time;
			$coupon_end_date	= $end_date." ".$end_time;
			$description	= mysql_real_escape_string(nl2br(getPostParameter('description')));
			$exclusive		= (int)getPostParameter('exclusive');
			$sort_order		= (int)getPostParameter('sort_order');
			$status			= mysql_real_escape_string(getPostParameter('status'));

			if (!($coupon_id && $coupon_type && $coupon_title && $retailer_id && $status))
			{
				$errors[] = "Please ensure that all fields marked with an asterisk are complete";
			}
			else
			{
				if (isset($link) && $link != "")
				{
					if (substr($link, 0, 7) != 'http://' && substr($link, 0, 8) != 'https://')
					{
						$errors[] = "Enter correct url format, enter the 'http://' or 'https://' statement before your link";
					}
					elseif ($link == 'http://' || $link == 'https://')
					{
						$errors[] = "Please enter correct URL";
					}
				}
			}

			if (count($errors) == 0)
			{
				smart_mysql_query("UPDATE cashbackengine_coupons SET coupon_type='$coupon_type', title='$coupon_title', retailer_id='$retailer_id', user_id='$user_id', code='$code', link='$link', start_date='$coupon_start_date', end_date='$coupon_end_date', description='$description', exclusive='$exclusive', sort_order='$sort_order', status='$status' WHERE coupon_id='$coupon_id'");

				header("Location: coupons.php?msg=updated");
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

		$query = "SELECT * FROM cashbackengine_coupons WHERE coupon_id='$id' LIMIT 1";
		$rs	= smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	$title = "Edit Coupon";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Coupon</h2>

	<?php if ($total > 0) {
		
		$row = mysql_fetch_array($rs);

	?>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div class="error_box"><?php echo $errormsg; ?></div>
	<?php } ?>

      <form action="" method="post" name="form1">
        <table bgcolor="#F9F9F9" width="100%" cellpadding="2" cellspacing="3"  border="0" align="center">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
		   <td valign="middle" align="right" class="tb1">Offer Type:</td>
		   <td valign="top">
				<select name="coupon_type" id="coupon_type" class="textbox2" style="width: 190px;">
					<option value="coupon" <?php if ($row['coupon_type'] == "coupon") echo "selected='selected'"; ?>>Coupon Code</option>
					<option value="printable" <?php if ($row['coupon_type'] == "printable") echo "selected='selected'"; ?>>Printable Coupon</option>
					<option value="discount" <?php if ($row['coupon_type'] == "discount") echo "selected='selected'"; ?>>Online Discount/Sale</option>
				</select>
		   </td>
          </tr>
          <tr>
            <td width="40%" valign="middle" align="right" class="tb1"><span class="req">* </span>Title:</td>
            <td valign="top"><input type="text" name="coupon_title" id="coupon_title" value="<?php echo $row['title']; ?>" size="33" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1"><span class="req">* </span>Store:</td>
            <td valign="top">
			<select class="textbox2" id="retailer_id" name="retailer_id" style="width: 190px;">
				<option value="">--- Please select store ---</option>
				<?php

					$sql_retailers = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE status='active' ORDER BY title ASC");
				
					while ($row_retailers = mysql_fetch_array($sql_retailers))
					{
						if ($row['retailer_id'] == $row_retailers['retailer_id']) $selected = " selected=\"selected\""; else $selected = "";

						echo "<option value=\"".$row_retailers['retailer_id']."\"".$selected.">".$row_retailers['title']."</option>";
					}
				?>	
			</select>
			</td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Coupon Code:</td>
				<td valign="top"><input type="text" name="code" id="code" value="<?php echo $row['code']; ?>" size="33" class="textbox" /></td>
			</tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Link:</td>
				<td valign="top"><input type="text" name="link" id="link" value="<?php echo $row['link']; ?>" size="53" class="textbox" /></td>
			</tr>
			<script>
				$(function() {
					$('#start_date').calendricalDate();
			        $('#end_date').calendricalDate();
			        $('#start_time').calendricalTime({
						minTime: {hour: 0, minute: 0},
						maxTime: {hour: 23, minute: 59},
						timeInterval: 30
					})
			        $('#end_time').calendricalTime({
						minTime: {hour: 0, minute: 0},
						maxTime: {hour: 23, minute: 59},
						timeInterval: 30
					})
				});
			</script>
            <tr>
				<td valign="middle" align="right" class="tb1">Start Date:</td>
				<td valign="middle"><input type="text" name="start_date" id="start_date" value="<?php echo ($row['start_date'] != "0000-00-00 00:00:00") ? substr($row['start_date'], 0, 10) : ""; ?>" size="10" maxlength="10" class="textbox" /> <input type="text" name="start_time" id="start_time" value="<?php echo ($row['start_date'] != "0000-00-00 00:00:00") ? substr($row['start_date'], -8, 5) : ""; ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Expiry Date:</td>
				<td valign="middle"><input type="text" name="end_date" id="end_date" value="<?php echo ($row['end_date'] != "0000-00-00 00:00:00") ? substr($row['end_date'], 0, 10) : ""; ?>" size="10" maxlength="10" class="textbox" /> <input type="text" name="end_time" id="end_time" value="<?php echo ($row['end_date'] != "0000-00-00 00:00:00") ? substr($row['end_date'], -8, 5) : ""; ?>" size="6" maxlength="8" class="textbox" /><span class="note">YYYY-MM-DD &nbsp; HH:MM</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Description:</td>
				<td valign="top"><textarea name="description" cols="55" rows="6" class="textbox2"><?php echo strip_tags($row['description']); ?></textarea></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sort Order:</td>
				<td valign="middle"><input type="text" class="textbox" name="sort_order" value="<?php echo $row['sort_order']; ?>" size="5" /></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Exclusive?</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="exclusive" value="1" <?php if ($row['exclusive'] == 1) echo "checked=\"checked\""; ?> />&nbsp;Yes!</td>
            </tr>
            <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
					<option value="expired" <?php if ($row['status'] == "expired") echo "selected"; ?>>expired</option>
				</select>
			</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="hidden" name="couponid" id="couponid" value="<?php echo (int)$row['coupon_id']; ?>" />
				<input type="hidden" name="action" id="action" value="edit">
				<input type="submit" class="submit" name="update" id="update" value="Update Coupon" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='coupons.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
              </td>
            </tr>
          </table>
      </form>

      <?php }else{ ?>
			<p align="center">Sorry, no coupon found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>