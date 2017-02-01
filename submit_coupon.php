<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2015 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");

	if (MEMBERS_SUBMIT_COUPONS == 1 && !isLoggedIn())
	{
		header ("Location: login.php?login");
		exit();
	}

	if (SUBMIT_COUPONS != 1)
	{
		header ("Location: index.php");
		exit();
	}


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		unset($errs);
		$errs = array();

		$coupon_type	= mysql_real_escape_string(getPostParameter('coupon_type'));
		$coupon_title	= mysql_real_escape_string(getPostParameter('coupon_title'));
		$retailer_id	= (int)getPostParameter('store');
		$code			= mysql_real_escape_string(getPostParameter('code'));
		$link			= mysql_real_escape_string(getPostParameter('link'));
		$date_mm		= mysql_real_escape_string(getPostParameter('date_mm'));
		$date_dd		= mysql_real_escape_string(getPostParameter('date_dd'));
		$date_yy		= mysql_real_escape_string(getPostParameter('date_yy'));
		$description	= mysql_real_escape_string(nl2br(getPostParameter('description')));
		$captcha		= mysql_real_escape_string(getPostParameter('captcha'));
		$ip				= mysql_real_escape_string(getenv("REMOTE_ADDR"));
		if (isLoggedIn()) $author_id = (int)$userid; else $author_id = "11111111";


		if (!($coupon_type && $coupon_title && $retailer_id))
		{
			$errs[] = CBE1_SCOUPON_ERR1;
		}
		else
		{
			if ($date_mm && $date_dd && $date_yy)
			{
				$end_date = $date_yy."-".$date_mm."-".$date_dd;
	
				if (strtotime($end_date) < strtotime("now"))
				{
					$errs[] = CBE1_SCOUPON_ERR3;
				}
				else
				{
					$end_date .= " 00:00:00";
				}
			}

			if ($coupon_title == "coupon" && $code == "")
			{
				$errs[] = CBE1_SIGNUP_ERR4;
			}
			elseif ($coupon_title == "printable" && $link == "")
			{
				$errs[] = CBE1_SIGNUP_ERR5;
			}

			if (!isLoggedIn())
			{
				if (!$captcha || empty($_SESSION['captcha']) || strcasecmp($_SESSION['captcha'], $captcha) != 0)
				{
					$errs[] = CBE1_SIGNUP_ERR3;
				}
			}

			if ($code != "") $where = "AND code='$code'"; elseif($link != "") $where = "AND link='$link'";
			$check_query = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE retailer_id='$retailer_id' $where LIMIT 1");
			if (mysql_num_rows($check_query) != 0)
			{
				$errs[] = CBE1_SCOUPON_ERR2;
			}
		}

		if (count($errs) == 0)
		{
			$query = "INSERT INTO cashbackengine_coupons SET coupon_type='$coupon_type', title='$coupon_title', retailer_id='$retailer_id', user_id='$author_id', code='$code', link='$link', start_date='', end_date='$end_date', description='$description', viewed='0', status='inactive', added=NOW()";
			$result = smart_mysql_query($query);

			// send email notification //
			if (NEW_COUPON_ALERT == 1)
			{
				SendEmail(SITE_ALERTS_MAIL, CBE1_EMAIL_ALERT1, CBE1_EMAIL_ALERT1_MSG);
			}
			/////////////////////////////
		
			header("Location: submit_coupon.php?msg=1");
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}


	if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
	{
		$retailer_id = (int)$_REQUEST['id'];

		$query = "SELECT * FROM cashbackengine_retailers WHERE retailer_id='$retailer_id' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 1"; 
		$result = smart_mysql_query($query);
		$total = mysql_num_rows($result);
		if ($total > 0)
		{
			$row = mysql_fetch_array($result);
		}
	}

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_SCOUPON_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_SCOUPON_TITLE; ?></h1>

	<?php if (isset($_GET['msg']) && $_GET['msg'] == 1) { ?>
		<div class="success_msg"><?php echo CBE1_SCOUPON_SENT; ?></div>
	<?php } ?>

	<?php if (!(isset($_GET['msg']) && $_GET['msg'] == 1)) { ?>		

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_msg"><?php echo $allerrors; ?></div>
		<?php } ?>

		

		<form action="" method="post">
		<table bgcolor="#F9F9F9" width="100%" align="center" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td colspan="2" style="position: relative">
				<img src="<?php echo SITE_URL; ?>images/coupon.png" style="position:absolute; right:0px;" />
				<p align="center"><?php echo CBE1_SCOUPON_TEXT; ?></p>
			</td>
		</tr>
		<tr>
		   <td align="right" valign="middle"><?php echo CBE1_SCOUPON_STORE; ?>:</td>
		   <td valign="top">
		   <?php if ($total > 0) { ?>
				<b><?php echo $row['title']; ?></b>
		   <?php }else{ ?>
				<select name="store" id="store" style="width: 192px;">
				<option value=""><?php echo CBE1_SCOUPON_STORE_SELECT; ?></option>
				<?php
					$select_allstores = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title ASC");
					while ($srow_allstores = mysql_fetch_array($select_allstores))
					{
						if ($retailer_id == $srow_allstores['retailer_id']) $dsel = "selected='selected'"; else $dsel = "";
						echo "<option value=\"".$srow_allstores['retailer_id']."\" $dsel>".$srow_allstores['title']."</option>";
					}
				?>
				</select>
			<?php } ?>
		   </td>
		</tr>
		<tr>
		   <td align="right" valign="middle"><?php echo CBE1_SCOUPON_TYPE; ?>:</td>
		   <td valign="top">
				<select name="coupon_type" id="coupon_type" onchange="hiddenDiv('coupon_type')" style="width: 192px;">
					<option value="coupon" <?php if ($coupon_type == "coupon") echo "selected='selected'"; ?>><?php echo CBE1_SCOUPON_TYPE1; ?></option>
					<option value="printable" <?php if ($coupon_type == "printable") echo "selected='selected'"; ?>><?php echo CBE1_SCOUPON_TYPE2; ?></option>
					<option value="discount" <?php if ($coupon_type == "discount") echo "selected='selected'"; ?>><?php echo CBE1_SCOUPON_TYPE3; ?></option>
				</select>
		   </td>
		</tr>
		<tr>
		   <td align="right" valign="middle"><?php echo CBE1_SCOUPON_NAME; ?>:</td>
		   <td valign="top"><input type="text" name="coupon_title" id="coupon_title" value="<?php echo getPostParameter('coupon_title'); ?>" class="textbox" required="required" size="29" /></td>
		</tr>
		<tr id="coupon_code" <?php if ($coupon_type != "coupon") { ?>style="display: none;"<?php } ?>>
		   <td align="right" valign="middle"><?php echo CBE1_SCOUPON_CODE; ?>:</td>
		   <td valign="top"><input type="text" name="code" id="code" value="<?php echo getPostParameter('code'); ?>" class="textbox" size="29" /></td>
		</tr>
		<tr id="coupon_link" <?php if ($coupon_type != "printable") { ?>style="display: none;"<?php } ?>>
		   <td align="right" valign="middle"><?php echo CBE1_SCOUPON_LINK; ?>:</td>
		   <td valign="top"><input type="text" name="link" id="link" placeholder="http://mycouponlink.com" value="<?php echo getPostParameter('link'); ?>" class="textbox" size="29" /></td>
		</tr>
		<tr>
		   <td align="right" valign="middle"><?php echo CBE1_SCOUPON_EXPIRY; ?>:</td>
		   <td valign="top">
				<input type="text" name="date_mm" id="date_mm" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_MM; ?>" class="textbox" value="<?php echo getPostParameter('date_mm'); ?>" maxlength="2" size="2" />
				<input type="text" name="date_dd" id="date_dd" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_DD; ?>" class="textbox" value="<?php echo getPostParameter('date_dd'); ?>" maxlength="2" size="2" />
				<input type="text" name="date_yy" id="date_yy" autocomplete="off" placeholder="<?php echo CBE1_SCOUPON_EXPIRY_YYYY; ?>" class="textbox" value="<?php echo getPostParameter('date_yy'); ?>" maxlength="4" size="4" />
				(<?php echo CBE1_FORMS_OPTIONAL; ?>)
			</td>
		</tr>
		<tr>
			<td align="right" valign="middle">&nbsp;</td>
			<td align="left" valign="middle">
				<textarea name="description" cols="55" rows="5" class="textbox2" placeholder="<?php echo CBE1_SCOUPON_DESCRIPTION; ?>"><?php echo getPostParameter('description'); ?></textarea>
			</td>
		</tr>
		<?php if (!isLoggedIn()) { ?>
		<tr>
			<td align="right" valign="middle"><?php echo CBE1_SIGNUP_SCODE; ?>:</td>
			<td align="left" valign="middle">
				<input type="text" id="captcha" class="textbox" name="captcha" value="" size="8" required="required" />
				<img src="<?php echo SITE_URL; ?>captcha.php?rand=<?php echo rand(); ?>" id="captchaimg" align="absmiddle" /> <small><a href="javascript: refreshCaptcha();" style="color: #777" title="<?php echo CBE1_SIGNUP_RIMG; ?>"><img src="<?php echo SITE_URL; ?>images/icon_refresh.png" align="absmiddle" alt="<?php echo CBE1_SIGNUP_RIMG; ?>" /></a></small>
			</td>
		 </tr>
		 <?php } ?>
		 <tr>
			<td align="left" valign="top">&nbsp;</td>
			<td align="left" valign="top">
				<?php if ($row['retailer_id'] > 0) { ?><input type="hidden" name="store" value="<?php echo (int)$row['retailer_id']; ?>" /><?php } ?>
				<?php if ($row['retailer_id'] > 0) { ?><input type="hidden" name="id" value="<?php echo (int)$row['retailer_id']; ?>" /><?php } ?>
				<input type="hidden" name="action" value="add" />
				<input type="submit" class="submit" value="<?php echo CBE1_SUBMIT_BUTTON; ?>" />
				<input type="button" class="cancel" name="cancel" value="<?php echo CBE1_CANCEL_BUTTON; ?>" onclick="history.go(-1);return false;" />
			</td>
		 </tr>
		 </table>
		 </form>

			<script language="javascript" type="text/javascript">
				function hiddenDiv(id){
					if (document.getElementById(id).value == "printable"){
						document.getElementById("coupon_link").style.display = "";
						document.getElementById("coupon_code").style.display = "none";
					}else if(document.getElementById(id).value == "discount"){
						document.getElementById("coupon_code").style.display = "none";
						document.getElementById("coupon_link").style.display = "none";
					}else{
						document.getElementById("coupon_code").style.display = "";
						document.getElementById("coupon_link").style.display = "none";
					}
				}
			</script>

			<script language="javascript" type="text/javascript">
				function refreshCaptcha()
				{
					var img = document.images['captchaimg'];
					img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
				}
			</script>

	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>