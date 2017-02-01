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


	if (!function_exists('str_split'))
	{
		function str_split($str)
		{
			$str_array=array();
			$len=strlen($str);
			for($i=0; $i<$len; $i++)
			{
				$str_array[]=$str{$i};
			}
			return $str_array;
		}
	}

	$tabid	= getGetParameter('tab');

	if (isset($_POST['action']) && $_POST['action'] == "savesettings")
	{
		$data	= array();
		$data	= $_POST['data'];

		$tabid	= getPostParameter('tabid');

		unset($errs);
		$errs = array();

		if ($tabid == "general")
		{
			if ($data['website_title'] == "")
				$errs[] = "Please enter website title";

			if ($data['website_home_title'] == "")
				$errs[] = "Please enter website homepage title";

			if ((substr($data['website_url'], -1) != '/') || ((substr($data['website_url'], 0, 7) != 'http://') && (substr($data['website_url'], 0, 8) != 'https://')))
				$errs[] = "Please enter correct site's url format, enter the 'http://' or 'https://' statement before your address, and a slash at the end ( e.g. http://www.yoursite.com/ )";

			if ((isset($data['website_email']) && $data['website_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['website_email'])))
				$errs[] = "Please enter a valid email address";

			if ((isset($data['alerts_email']) && $data['alerts_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['alerts_email'])))
				$errs[] = "Please enter a valid alerts email address";

			if ($data['website_date_format'] == "") $data['website_date_format'] = "%d %b %Y";

			if ($data['cashback_commission'] == "" || !is_numeric($data['cashback_commission']))
				$errs[] = "Please enter correct cashback commission";

			if ($data['signup_credit'] == "" || !is_numeric($data['signup_credit']))
				$errs[] = "Please enter correct value for sign up bonus";

			if ($data['refer_credit'] == "" || !is_numeric($data['refer_credit']))
				$errs[] = "Please enter correct refer a friend credit";

			if ($data['referral_commission'] == "" || !is_numeric($data['referral_commission']))
				$errs[] = "Please enter correct referral commission";

			if ($data['min_payout'] == "" || !is_numeric($data['min_payout']))
				$errs[] = "Please enter correct min payout";

			if ($data['min_transaction'] == "" || !is_numeric($data['min_transaction']))
				$errs[] = "Please enter correct min transaction";

			if ($data['news_per_page'] == "" || !is_numeric($data['news_per_page']))
				$errs[] = "Please enter correct number news per page";

			if ($data['multilingual'] != 1)
			{
				$default_language = mysql_real_escape_string($data['website_language']);
				smart_mysql_query("UPDATE cashbackengine_content SET language='$default_language' WHERE content_id<=7");
				smart_mysql_query("UPDATE cashbackengine_email_templates SET language='$default_language' WHERE template_id<=8");
			}
		}
		else if ($tabid == "retailers")
		{
			if ($data['stores_description_limit'] == "" || !is_numeric($data['stores_description_limit']))
				$errs[] = "Please enter correct stores description limit";

			if ($data['results_per_page'] == "" || !is_numeric($data['results_per_page']))
				$errs[] = "Please enter correct number retailers per page";

			if ($data['new_stores_limit'] == "" || !is_numeric($data['new_stores_limit']))
				$errs[] = "Please enter correct new stores limit number";

			if ($data['featured_stores_limit'] == "" || !is_numeric($data['featured_stores_limit']))
				$errs[] = "Please enter correct featured stores limit number";

			if ($data['popular_stores_limit'] == "" || !is_numeric($data['popular_stores_limit']))
				$errs[] = "Please enter correct most popular stores limit number";

			if ($data['image_width'] == "" || !is_numeric($data['image_width']))
				$errs[] = "Please enter correct retailers images width";

			if ($data['image_height'] == "" || !is_numeric($data['image_height']))
				$errs[] = "Please enter correct retailers images height";

			if ($data['homepage_reviews_limit'] == "" || !is_numeric($data['homepage_reviews_limit']))
				$errs[] = "Please enter correct homepage reviews limit number";

			if ($data['reviews_per_page'] == "" || !is_numeric($data['reviews_per_page']))
				$errs[] = "Please enter correct number reviews per page";

			if (!(isset($data['max_review_length']) && is_numeric($data['max_review_length']) && $data['max_review_length'] > 0))
				$errs[] = "Please enter correct max review length";
		}
		else if ($tabid == "coupons")
		{
			if ($data['coupons_description_limit'] == "" || !is_numeric($data['coupons_description_limit']))
				$errs[] = "Please enter correct coupons description limit";

			if ($data['todays_coupons_limit'] == "" || !is_numeric($data['todays_coupons_limit']))
				$errs[] = "Please enter correct today's top coupons limit number";

			if ($data['coupons_per_page'] == "" || !is_numeric($data['coupons_per_page']))
				$errs[] = "Please enter correct number coupons per page";
		}
		else if ($tabid == "facebook")
		{
		}
		else if ($tabid == "mail")
		{
			if ((isset($data['noreply_email']) && $data['noreply_email'] != "" && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['noreply_email'])))
				$errs[] = "Please enter a valid no-reply email address";
		}
		else if ($tabid == "notifications")
		{
		}
		else if ($tabid == "other")
		{
		}

		if (count($errs) == 0)
		{
			foreach ($data as $key=>$value)
			{
				$value	= mysql_real_escape_string(trim($value));
				$key	= mysql_real_escape_string(trim($key));
				if ($key == "website_currency" && $value == "") $key = "111111";
				smart_mysql_query("UPDATE cashbackengine_settings SET setting_value='$value' WHERE setting_key='$key'");
			}

			header("Location: settings.php?msg=updated&tab=$tabid#".$tabid);
			exit();
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}

	}


	if (isset($_POST['action']) && $_POST['action'] == "updatepassword")
	{
		$tabid	= getPostParameter('tabid');

		$cpwd		= mysql_real_escape_string(getPostParameter('cpassword'));
		$pwd		= mysql_real_escape_string(getPostParameter('npassword'));
		$pwd2		= mysql_real_escape_string(getPostParameter('npassword2'));
		$iword		= substr(GetSetting('iword'), 0, -3);

		unset($errs2);
		$errs2 = array();

		if (!($cpwd && $pwd && $pwd2))
		{
			$errs2[] = "Please fill in all fields";
		}
		else
		{
			if (GetSetting('word') !== PasswordEncryption($cpwd.$iword))
				$errs2[] = "Current password is wrong";

			if ($pwd !== $pwd2)
			{
				$errs2[] = "Password confirmation is wrong";
			}
			elseif ((strlen($pwd)) < 6 || (strlen($pwd2) < 6) || (strlen($pwd)) > 20 || (strlen($pwd2) > 20))
			{
				$errs2[] = "Password must be between 6-20 characters";
			}
		}

		if (count($errs2) == 0)
		{
				$query = "UPDATE cashbackengine_settings SET setting_value='".PasswordEncryption($pwd.$iword)."' WHERE setting_key='word'";

				if (smart_mysql_query($query))
				{
					header("Location: settings.php?msg=updated&tab=$tabid#".$tabid);
					exit();
				}
		}
		else
		{
			$allerrors2 = "";
			foreach ($errs2 as $errorname)
				$allerrors2 .= "&#155; ".$errorname."<br/>\n";
		}

	}

	$lik = str_replace("|","","l|i|c|e|n|s|e");
	$li = GetSetting($lik);
	if (!preg_match("/^[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}?$/", $li))
	{$license_status = "correct";$st = 1;}else{$license_status = "wrong";$key=explode("-",$li);$keey=$key[rand(0,2)];
	if($ikey[4][2]=7138%45){$step=1;$t=1;$license_status="wrong";}else{$license_status="correct";$step=2;}
	if($keey>0){$i=30+$step;if(rand(7,190)>=rand(0,1))$st=+$i;$u=0;}$status2=str_split($key[1],1);$status4=str_split($key[3],1);$status1=str_split($key[0],1);$status3=str_split($key[2],1);	if($step==1){$kky=str_split($key[$u+4],1);if((($key[$u]+$key[2])-($key[3]+$key[$t])==(((315*2+$u)+$t)*++$t))&&(($kky[3])==$status4[2])&&(($status3[1])==$kky[0])&&(($status2[3])==$kky[1])&&(($kky[2]==$status2[1]))){$kkkeey=1; $query = "SELECT * FROM cashbackengine_settings";}else{ $query = ""; if(!file_exists('./inc/fckeditor/ck.inc.php')) die("can't connect to database"); else require_once('./inc/rp.inc.php'); }}} if($lics!=7){$wrong=1;$license_status="wrong";}else{$wrong=0;$correct=1;}

	$result = smart_mysql_query($query);
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$settings[$row['setting_key']] = $row['setting_value'];
		}
	}


	$title = "Site Settings";
	require_once ("inc/header.inc.php");

?>

    <h2><img src="images/icons/settings.gif" align="absmiddle" /> Website Settings</h2>

	<div id="tabs_container">
		<ul id="tabs">
			<li class="active"><a href="#general"><span>General</span></a></li>
			<li><a href="#retailers"><span>Retailers</span></a></li>
			<li><a href="#coupons"><span>Coupons</span></a></li>
			<li><a href="#facebook"><span>Facebook</span></a></li>
			<li><a href="#mail"><span>Mail</span></a></li>
			<li><a href="#notifications"><span>Email Notifications</span></a></li>
			<li><a href="#other"><span>Other</span></a></li>
			<li><a href="#password"><span>Admin Password</span></a></li>
		</ul>
	</div>

	<div id="general" class="tab_content">
      <form action="#general" method="post">
		<?php if (isset($tabid) && $tabid == "general") { ?>
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="error_box"><?php echo $allerrors; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Settings have been successfully saved</div>
			<?php } ?>
		<?php } ?>
        <table width="100%" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="150" valign="middle" align="right" class="tb1">Site Name:</td>
            <td valign="middle"><input type="text" name="data[website_title]" value="<?php echo $settings['website_title']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Homepage Title:</td>
            <td valign="middle"><input type="text" name="data[website_home_title]" value="<?php echo $settings['website_home_title']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="top" align="right" class="tb1" style="padding-top:7px;">Site address (URL):</td>
            <td valign="top"><input type="text" name="data[website_url]" value="<?php echo $settings['website_url']; ?>" size="40" class="textbox" /><br/>
			<span style="color: #9B9B9B; font-size: 10px;">NOTE: enter the 'http://' statement before your address, and a slash at the end, e.g. http://www.yoursite.com/</small>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Site Mode:</td>
            <td valign="middle">
				<select name="data[website_mode]">
					<option value="live" <?php if ($settings['website_mode'] == "live") echo "selected"; ?>>live</option>
					<option value="maintenance" <?php if ($settings['website_mode'] == "maintenance") echo "selected"; ?>>maintenance</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Admin Email Address:</td>
            <td valign="middle"><input type="text" name="data[website_email]" value="<?php echo $settings['website_email']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Alerts Email Address:</td>
            <td nowrap="nowrap" valign="middle"><input type="text" name="data[alerts_email]" value="<?php echo $settings['alerts_email']; ?>" size="40" class="textbox" /><span class="note">email for notifications</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Site Language:</td>
            <td valign="top">
				<select name="data[website_language]">
				<?php
					$languages_dir = "../language/";
					$languages = scandir($languages_dir); 
					$array = array(); 
					foreach ($languages as $file)
					{
						if (is_file($languages_dir.$file) && strstr($file, ".inc.php")) { $language= str_replace(".inc.php","",$file);
				?>
					<option value="<?php echo $language; ?>" <?php if ($settings['website_language'] == $language) echo 'selected="selected"'; ?>><?php echo $language; ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Multilingual Site:</td>
            <td valign="middle">
				<select name="data[multilingual]">
					<option value="1" <?php if ($settings['multilingual'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['multilingual'] == "0") echo "selected"; ?>>off</option>
				</select>
				&nbsp;<a href="languages.php">manage languages &#155;</a>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Site Currency:</td>
            <td align="left" valign="middle">
				<span style="font-size:19px; color:#61DB06;"><b><?php echo $settings['website_currency']; ?></b></span>&nbsp;&nbsp; change currency: 
				<select name="data[website_currency]">
					<option value="">--------</option>
					<option value="$">Dollar</option>
					<option value="&euro;">Euro</option>
					<option value="&pound;">Pound</option>
					<option value="&yen;">Yen</option>
					<option value="$">Australian Dollar</option>
					<option value="$">Canadian Dollar</option>
					<option value="Kc">Czech Koruna</option>
					<option value="kr.">Danish Krone</option>
					<option value="Rs.">Indian Rupee</option>
					<option value="руб.">Russian Ruble</option>
					<option value="lei">Romanian Leu</option>
					<option value="kr.">Swedish Krona</option>
					<option value="fr.">Swiss Franc</option>
					<option value="tl.">Turkish Lira</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Currency Format:</td>
            <td valign="middle">
				<select name="data[website_currency_format]">
					<option value="1" <?php if ($settings['website_currency_format'] == "1") echo "selected"; ?>><?php echo SITE_CURRENCY; ?>5.00</option>
					<option value="2" <?php if ($settings['website_currency_format'] == "2") echo "selected"; ?>><?php echo SITE_CURRENCY; ?> 5.00</option>
					<option value="3" <?php if ($settings['website_currency_format'] == "3") echo "selected"; ?>><?php echo SITE_CURRENCY; ?>5,00</option>
					<option value="4" <?php if ($settings['website_currency_format'] == "4") echo "selected"; ?>>5.00 <?php echo SITE_CURRENCY; ?></option>
					<option value="5" <?php if ($settings['website_currency_format'] == "5") echo "selected"; ?>>5.00<?php echo SITE_CURRENCY; ?></option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Sign Up Captcha:</td>
            <td valign="middle">
				<select name="data[signup_captcha]">
					<option value="1" <?php if ($settings['signup_captcha'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['signup_captcha'] == "0") echo "selected"; ?>>off</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Sign Up Email Activation:</td>
            <td valign="middle">
				<select name="data[account_activation]">
					<option value="1" <?php if ($settings['account_activation'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['account_activation'] == "0") echo "selected"; ?>>off</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Login Attempts Limit:</td>
            <td valign="middle">
				<select name="data[login_attempts_limit]">
					<option value="1" <?php if ($settings['login_attempts_limit'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['login_attempts_limit'] == "0") echo "selected"; ?>>off</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Cashback Commission:</td>
            <td nowrap="nowrap" valign="middle"><input type="text" name="data[cashback_commission]" value="<?php echo $settings['cashback_commission']; ?>" size="2" class="textbox" />%<span class="note">the part of the commission you share with your members as cashback</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Sign Up Bonus:</td>
            <td valign="middle"><?php echo SITE_CURRENCY; ?><input type="text" name="data[signup_credit]" value="<?php echo $settings['signup_credit']; ?>" size="3" class="textbox" /><span class="note">sign up bonus for new members (0 = disabled)</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Refer a Friend Bonus:</td>
            <td valign="middle"><?php echo SITE_CURRENCY; ?><input type="text" name="data[refer_credit]" value="<?php echo $settings['refer_credit']; ?>" size="3" class="textbox" /><span class="note">amount which users earn when they refer a friend (0 = disabled)</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Referral Commission:</td>
            <td nowrap="nowrap" valign="top"><input type="text" name="data[referral_commission]" value="<?php echo $settings['referral_commission']; ?>" size="2" class="textbox" />%<span class="note">percentage which users earn from their referred friends</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Minimum Payout:</td>
            <td valign="middle"><?php echo SITE_CURRENCY; ?><input type="text" name="data[min_payout]" value="<?php echo $settings['min_payout']; ?>" size="3" class="textbox" /><span class="note">amount which users need to earn before they request payout</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Minimum Transaction:</td>
            <td valign="middle"><?php echo SITE_CURRENCY; ?><input type="text" name="data[min_transaction]" value="<?php echo $settings['min_transaction']; ?>" size="3" class="textbox" /><span class="note">minimum withdrawal amount</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Cancel Withdrawal:</td>
            <td valign="middle">
				<select name="data[cancel_withdrawal]">
					<option value="1" <?php if ($settings['cancel_withdrawal'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['cancel_withdrawal'] == "0") echo "selected"; ?>>no</option>
				</select><span class="note">allow members to cancel pending withdrawal</span>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">News per Page:</td>
            <td valign="middle">
				<select name="data[news_per_page]">
					<option value="5" <?php if ($settings['news_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['news_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="20" <?php if ($settings['news_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['news_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['news_per_page'] == "50") echo "selected"; ?>>50</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Site Statistics:</td>
            <td valign="middle">
				<select name="data[show_site_statistics]">
					<option value="1" <?php if ($settings['show_site_statistics'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_site_statistics'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Time Zone:</td>
            <td valign="middle">
				<select name="data[website_timezone]">
				<option value="">--- Use System Default ---</option>
				<?php if (count($timezone) > 0) { ?>
					<?php foreach ($timezone as $v) { ?>
						<option value="<?php echo $v; ?>" <?php if ($settings['website_timezone'] == $v) echo "selected"; ?>><?php echo $v; ?></option>
					<?php } ?>
				<?php } ?>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Date Format:</td>
            <td valign="middle"><input type="text" name="data[website_date_format]" value="<?php echo $settings['website_date_format']; ?>" size="10" class="textbox" /><span class="note">e.g., %d %b %Y</span></td>
          </tr>
          <tr>
            <td align="center" valign="bottom">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="tabid" id="tabid" value="general" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
            </td>
          </tr>
        </table>
      </form>
	 </div>


	<div id="coupons" class="tab_content">
		<form action="#coupons" method="post">
		<?php if (isset($tabid) && $tabid == "coupons") { ?>
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="error_box"><?php echo $allerrors; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Settings have been successfully saved</div>
			<?php } ?>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="150" valign="middle" align="right" class="tb1">Coupons Description Limit:</td>
            <td valign="middle"><input type="text" name="data[coupons_description_limit]" value="<?php echo $settings['coupons_description_limit']; ?>" size="3" class="textbox" /> characters</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Today's Top Coupons Limit:</td>
            <td valign="middle"><input type="text" name="data[todays_coupons_limit]" value="<?php echo $settings['todays_coupons_limit']; ?>" size="3" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Coupons per Page:</td>
            <td valign="middle">
				<select name="data[coupons_per_page]">
					<option value="5" <?php if ($settings['coupons_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['coupons_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="20" <?php if ($settings['coupons_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['coupons_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['coupons_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['coupons_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Allow users submit coupons:</td>
            <td valign="middle">
				<select name="data[submit_coupons]">
					<option value="1" <?php if ($settings['submit_coupons'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['submit_coupons'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Only members can submit coupons:</td>
            <td valign="middle">
				<select name="data[members_submit_coupons]">
					<option value="1" <?php if ($settings['members_submit_coupons'] == "1") echo "selected"; ?>>on</option>
					<option value="0" <?php if ($settings['members_submit_coupons'] == "0") echo "selected"; ?>>off</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Hide coupons from unregistered users:</td>
            <td valign="middle">
				<select name="data[hide_coupons]">
					<option value="1" <?php if ($settings['hide_coupons'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['hide_coupons'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td align="center" valign="bottom">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="tabid" id="tabid" value="coupons" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
            </td>
          </tr>
        </table>
		</form>
	</div>


	<div id="retailers" class="tab_content">
		<form action="#retailers" method="post">
		<?php if (isset($tabid) && $tabid == "retailers") { ?>
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="error_box"><?php echo $allerrors; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Settings have been successfully saved</div>
			<?php } ?>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="150" valign="middle" align="right" class="tb1">Retailers List Style:</td>
            <td valign="middle">
				<select name="data[stores_list_style]">
					<option value="1" <?php if ($settings['stores_list_style'] == "1") echo "selected"; ?>>Full</option>
					<option value="2" <?php if ($settings['stores_list_style'] == "2") echo "selected"; ?>>Short list</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Share Icons:</td>
            <td valign="middle">
				<select name="data[share_icons_style]">
					<option value="1" <?php if ($settings['share_icons_style'] == "1") echo "selected"; ?>>large</option>
					<option value="2" <?php if ($settings['share_icons_style'] == "2") echo "selected"; ?>>small</option>
				</select>
            </td>
          </tr>		  
          <tr>
            <td valign="middle" align="right" class="tb1">Retailers Description Limit:</td>
            <td valign="middle"><input type="text" name="data[stores_description_limit]" value="<?php echo $settings['stores_description_limit']; ?>" size="3" class="textbox" /> characters</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Retailers per Page:</td>
            <td valign="middle">
				<select name="data[results_per_page]">
					<option value="5" <?php if ($settings['results_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['results_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="25" <?php if ($settings['results_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['results_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['results_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Featured Stores Limit:</td>
            <td valign="middle"><input type="text" name="data[featured_stores_limit]" value="<?php echo $settings['featured_stores_limit']; ?>" size="3" class="textbox" />
			</td>
          </tr>		  
          <tr>
            <td valign="middle" align="right" class="tb1">Popular Stores Limit:</td>
            <td valign="middle"><input type="text" name="data[popular_stores_limit]" value="<?php echo $settings['popular_stores_limit']; ?>" size="3" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">New Stores Limit:</td>
            <td valign="middle"><input type="text" name="data[new_stores_limit]" value="<?php echo $settings['new_stores_limit']; ?>" size="3" class="textbox" />
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Retailer Image Size:</td>
            <td valign="middle">
				<input type="text" name="data[image_width]" value="<?php echo $settings['image_width']; ?>" size="3" class="textbox" /> x 
				<input type="text" name="data[image_height]" value="<?php echo $settings['image_height']; ?>" size="3" class="textbox" /> px
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Cashback Calculator:</td>
            <td valign="middle">
				<select name="data[show_cashback_calculator]">
					<option value="1" <?php if ($settings['show_cashback_calculator'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_cashback_calculator'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Retailer Statistics:</td>
            <td valign="middle">
				<select name="data[show_statistics]">
					<option value="1" <?php if ($settings['show_statistics'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_statistics'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Landing Page:</td>
            <td valign="middle">
				<select name="data[show_landing_page]">
					<option value="1" <?php if ($settings['show_landing_page'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_landing_page'] == "0") echo "selected"; ?>>no</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">One review for store:</td>
            <td valign="middle">
				<select name="data[one_review]">
					<option value="1" <?php if ($settings['one_review'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['one_review'] == "0") echo "selected"; ?>>no</option>
				</select>
				<span class="note">user can submit only one review for same store</span>
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Homepage Reviews Limit:</td>
            <td valign="middle"><input type="text" name="data[homepage_reviews_limit]" value="<?php echo $settings['homepage_reviews_limit']; ?>" size="3" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Reviews per Page:</td>
            <td valign="middle">
				<select name="data[reviews_per_page]">
					<option value="5" <?php if ($settings['reviews_per_page'] == "5") echo "selected"; ?>>5</option>
					<option value="10" <?php if ($settings['reviews_per_page'] == "10") echo "selected"; ?>>10</option>
					<option value="20" <?php if ($settings['reviews_per_page'] == "20") echo "selected"; ?>>20</option>
					<option value="25" <?php if ($settings['reviews_per_page'] == "25") echo "selected"; ?>>25</option>
					<option value="50" <?php if ($settings['reviews_per_page'] == "50") echo "selected"; ?>>50</option>
					<option value="100" <?php if ($settings['reviews_per_page'] == "100") echo "selected"; ?>>100</option>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Manually Approve Reviews:</td>
            <td valign="middle">
				<select name="data[reviews_approve]">
					<option value="1" <?php if ($settings['reviews_approve'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['reviews_approve'] == "0") echo "selected"; ?>>no</option>					
				</select>			
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Max Review Length:</td>
            <td valign="middle"><input type="text" name="data[max_review_length]" value="<?php echo $settings['max_review_length']; ?>" size="3" class="textbox" /> characters</td>
          </tr>
          <tr>
            <td align="center" valign="bottom">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="tabid" id="tabid" value="retailers" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
            </td>
          </tr>
        </table>
		</form>
	</div>


	<div id="facebook" class="tab_content">
		<form action="#facebook" method="post">
		<?php if (isset($tabid) && $tabid == "facebook") { ?>
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="error_box"><?php echo $allerrors; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Settings have been successfully saved</div>
			<?php } ?>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="3" border="0">
		<tr>
			<td width="50%" align="left" valign="top">
				<table cellpadding="2" cellspacing="3"  border="0">
				  <tr>
					<td valign="middle" align="right" class="tb1">Facebook Connect:</td>
					<td valign="middle">
						<select name="data[facebook_connect]">
							<option value="1" <?php if ($settings['facebook_connect'] == "1") echo "selected"; ?>>yes</option>
							<option value="0" <?php if ($settings['facebook_connect'] == "0") echo "selected"; ?>>no</option>
						</select>				
					</td>
				  </tr>
				  <tr>
					<td valign="middle" align="right" class="tb1">App ID:</td>
					<td valign="middle"><input type="text" name="data[facebook_appid]" value="<?php echo $settings['facebook_appid']; ?>" size="40" class="textbox" /></td>
				  </tr>
				  <tr>
					<td valign="middle" align="right" class="tb1">App Secret:</td>
					<td valign="middle"><input type="text" name="data[facebook_secret]" value="<?php echo $settings['facebook_secret']; ?>" size="40" class="textbox" /></td>
				  </tr>
				  <tr>
					<td align="center" valign="bottom">&nbsp;</td>
					<td align="left" valign="top">
						<input type="hidden" name="tabid" id="tabid" value="facebook" />
						<input type="hidden" name="action" id="action" value="savesettings" />
						<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
					</td>
				  </tr>
				</table>
			</td>
			<td width="50%" align="left" valign="top">
				<p style="text-align: justify">
					To enable this feature, a valid Facebook App ID/API key and App secret are required. This information is provided on the <a href="https://developers.facebook.com/apps" target="_blank">apps page</a> of Facebook's developer website. <br/><br/>If you don't already have an existing Facebook app, you will need to create one to get the App ID/API key and App secret to use with this feature.
				</p>
			</td>
		</tr>
		</table>
		</form>
	</div>


	<div id="mail" class="tab_content">

		<script type="text/javascript">
		$(function(){
			send_mail_method();
		});
		function send_mail_method(){
			emethod = $("#smtp_mail").val();
			if(emethod == 1){
				$("#smtp_details").show();
			}else{
				$("#smtp_details").hide();
			}
		}
		</script>

		<form action="#mail" method="post">
		<?php if (isset($tabid) && $tabid == "mail") { ?>
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="error_box"><?php echo $allerrors; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Settings have been successfully saved</div>
			<?php } ?>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="3" border="0">
		<tr>
			<td width="150" valign="middle" align="right" class="tb1">Site Emails From Name:</td>
			<td valign="middle" align="left"><input type="text" name="data[email_from_name]" value="<?php echo $settings['email_from_name']; ?>" size="30" class="textbox" /></td>
		</tr>
          <tr>
            <td valign="middle" align="right" class="tb1">No-reply Email Address:</td>
            <td valign="middle"><input type="text" name="data[noreply_email]" value="<?php echo $settings['noreply_email']; ?>" size="30" class="textbox" /></td>
          </tr>
		<tr>
			<td valign="middle" align="right" class="tb1">Mail Type:</td>
			<td valign="middle">
				<select name="data[smtp_mail]" id="smtp_mail" onchange="send_mail_method();">
					<option value="0" <?php echo ($settings['smtp_mail'] == "0") ? "selected" : ""; ?>>PHP mail()</option>
					<option value="1" <?php echo ($settings['smtp_mail'] == "1") ? "selected" : ""; ?>>SMTP</option>
				</select>				
			</td> 
		</tr>
		</table>		
		<table cellpadding="2" cellspacing="3"  border="0" id="smtp_details" <?php if ($settings['smtp_mail'] != 1 && @$data['smtp_mail'] != 1) { ?>style="display: none;"<?php } ?>>
		<tr>
			<td width="150" valign="middle" align="right" class="tb1">SMTP Port:</td>
			<td valign="middle"><input type="text" name="data[smtp_port]" value="<?php echo $settings['smtp_port']; ?>" size="30" class="textbox" /></td>
		</tr>
		<tr>
			<td valign="middle" align="right" class="tb1">SMTP Host:</td>
			<td valign="middle"><input type="text" name="data[smtp_host]" value="<?php echo $settings['smtp_host']; ?>" size="30" class="textbox" /></td>
		</tr>
		<tr>
			<td valign="middle" align="right" class="tb1">SMTP Username:</td>
			<td valign="middle"><input type="text" name="data[smtp_username]" value="<?php echo $settings['smtp_username']; ?>" size="30" class="textbox" /></td>
		</tr>
		<tr>
			<td valign="middle" align="right" class="tb1">SMTP Password:</td>
			<td valign="middle"><input type="password" name="data[smtp_password]" value="<?php echo $settings['smtp_password']; ?>" size="30" class="textbox" /></td>
		</tr>
		<tr>
			<td valign="middle" align="right" class="tb1">SMTP SSL Type:</td>
			<td valign="middle">
				<label><input type="radio" name="data[smtp_ssl]" value="" <?php echo ($settings['smtp_ssl'] == "") ? "checked" : ""; ?> /> None</label> 
				<label><input type="radio" name="data[smtp_ssl]" value="ssl" <?php echo ($settings['smtp_ssl'] == "ssl") ? "checked" : ""; ?> /> SSL</label>
				<label><input type="radio" name="data[smtp_ssl]" value="tls" <?php echo ($settings['smtp_ssl'] == "tls") ? "checked" : ""; ?> /> TLS</label>					
			</td>
		</tr>
		</table>
		<table cellpadding="2" cellspacing="3"  border="0">
		<tr>
			<td width="150" align="center" valign="bottom">&nbsp;</td>
			<td align="left" valign="top">
				<input type="hidden" name="tabid" id="tabid" value="mail" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
			</td>
		</tr>
		</table>
		</form>
	</div>


	<div id="notifications" class="tab_content">
		<form action="#notifications" method="post">
		<?php if (isset($tabid) && $tabid == "notifications") { ?>
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="error_box"><?php echo $allerrors; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Settings have been successfully saved</div>
			<?php } ?>
		<?php } ?>
		<p><b>Notify admin by email when:</b></p>
		<table width="100%" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="5" valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_coupon]" value="0" /><input type="checkbox" name="data[email_new_coupon]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_coupon'] == 1) ? "checked" : "" ?>/>&nbsp; new coupon added</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_review]" value="0" /><input type="checkbox" name="data[email_new_review]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_review'] == 1) ? "checked" : "" ?>/>&nbsp; new review added</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_ticket]" value="0" /><input type="checkbox" name="data[email_new_ticket]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_ticket'] == 1) ? "checked" : "" ?> />&nbsp; new support ticket sends</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_ticket_reply]" value="0" /><input type="checkbox" name="data[email_new_ticket_reply]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_ticket_reply'] == 1) ? "checked" : "" ?> />&nbsp; new support ticket reply sends</td>
          </tr>
          <tr>
            <td valign="middle" align="left" class="tb1">&nbsp;</td>
            <td valign="top"><input type="hidden" name="data[email_new_report]" value="0" /><input type="checkbox" name="data[email_new_report]" value="1" size="40" class="checkboxx" <?php echo ($settings['email_new_report'] == 1) ? "checked" : "" ?>/>&nbsp; new store report sends</td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="notifications" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
			</td>
          </tr>
		  </table>
		</form>
	</div>


	<div id="other" class="tab_content">
		<form action="#other" method="post">
		<?php if (isset($tabid) && $tabid == "other") { ?>
			<?php if (isset($allerrors) && $allerrors != "") { ?>
				<div class="error_box"><?php echo $allerrors; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Settings have been successfully saved</div>
			<?php } ?>
		<?php } ?>
		<table width="100%" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td width="150" valign="middle" align="right" class="tb1">Addthis.com Account ID:</td>
            <td valign="middle"><input type="text" name="data[addthis_id]" value="<?php echo $settings['addthis_id']; ?>" size="20" class="textbox" /><span class="note">for share buttons</span></td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">"How did you hear about us" field:</td>
            <td valign="middle"><input type="text" name="data[reg_sources]" value="<?php echo $settings['reg_sources']; ?>" size="40" class="textbox" /><span class="note">dropdown values, separated by comma</span></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Facebook Page URL:</td>
            <td valign="middle"><input type="text" name="data[facebook_page]" value="<?php echo $settings['facebook_page']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Show Facebook Like Box:</td>
            <td valign="middle">
				<select name="data[show_fb_likebox]">
					<option value="1" <?php if ($settings['show_fb_likebox'] == "1") echo "selected"; ?>>yes</option>
					<option value="0" <?php if ($settings['show_fb_likebox'] == "0") echo "selected"; ?>>no</option>
				</select>				
			</td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Twitter Page URL:</td>
            <td valign="middle"><input type="text" name="data[twitter_page]" value="<?php echo $settings['twitter_page']; ?>" size="40" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Google Analytics:</td>
            <td valign="top"><textarea name="data[google_analytics]" cols="55" rows="5" class="textbox2"><?php echo $settings['google_analytics']; ?></textarea></td>
          </tr>
          <tr>
			<td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="other" />
				<input type="hidden" name="action" id="action" value="savesettings" />
				<input type="submit" name="save" id="save" class="submit" value="Save Changes" />
			</td>
          </tr>
		</table>
		</form>
	</div>


	<div id="password" class="tab_content">
		<form action="#password" method="post">
		<?php if (isset($tabid) && $tabid == "password") { ?>
			<?php if (isset($allerrors2) && $allerrors2 != "") { ?>
				<div class="error_box"><?php echo $allerrors2; ?></div>
			<?php }elseif (isset($_GET['msg']) && $_GET['msg'] == "updated") { ?>
				<div class="success_box">Password has been changed successfully</div>
			<?php } ?>
		<?php } ?>
		<img src="images/icons/password.gif" style="position: absolute; right: 10px;" />
        <table width="100%" cellpadding="2" cellspacing="3" border="0">
          <tr>
            <td width="40%" valign="middle" align="right" class="tb1">Current Password:</td>
            <td valign="top"><input type="password" name="cpassword" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">New Admin Password:</td>
            <td valign="top"><input type="password" name="npassword" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Confirm New Password:</td>
            <td valign="top"><input type="password" name="npassword2" value="" size="30" class="textbox" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
			<td align="left" valign="middle">
				<input type="hidden" name="tabid" id="tabid" value="password" />
				<input type="hidden" name="action" id="action" value="updatepassword" />
				<input type="submit" name="psave" id="psave" class="submit" value="Change Password" />
			</td>
          </tr>
        </table>
		</form>
	</div>


<?php require_once ("inc/footer.inc.php"); ?>