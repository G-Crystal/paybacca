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

		$query = "SELECT * FROM cashbackengine_countries WHERE country_id='$id'";
		$rs = smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	if (isset($_POST["action"]) && $_POST["action"] == "edit")
	{
		unset($errors);
		$errors = array();
 
		$country_id		= (int)getPostParameter('country_id');
		$country_name	= mysql_real_escape_string(getPostParameter('country_name'));
		$signup			= (int)getPostParameter('signup');
		$status			= mysql_real_escape_string(getPostParameter('status'));
		$sort_order		= (int)getPostParameter('sort_order');

		if (!($country_name && $country_name))
		{
			$errors[] = "Please fill in all required fields";
		}

		if (count($errors) == 0)
		{
			$sql = "UPDATE cashbackengine_countries SET name='$country_name', signup='$signup', sort_order='$sort_order', status='$status' WHERE country_id='$country_id' LIMIT 1";
			
			if (smart_mysql_query($sql))
			{
				header("Location: countries.php?msg=updated");
				exit();
			}
		}
		else
		{
			$errormsg = "";
			foreach ($errors as $errorname)
				$errormsg .= "&#155; ".$errorname."<br/>";
		}
	}

	$title = "Edit Country";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Country</h2>

	<?php if ($total > 0) {
	
		$row = mysql_fetch_array($rs);

	?>

		<?php if (isset($errormsg) && $errormsg != "") { ?>
			<div class="error_box"><?php echo $errormsg; ?></div>
		<?php } ?>

      <form action="" method="post">
        <table width="100%" bgcolor="#F9F9F9" cellpadding="2" cellspacing="3"  border="0" align="center">
		<?php if ($row['code'] != "" && file_exists('../images/flags/'.strtolower($row['code']).'.png')) { ?>
          <tr>
            <td width="45%" valign="middle" align="right" class="tb1">Flag:</td>
            <td width="55%" valign="middle"><img src="../images/flags/<?php echo strtolower($row['code']); ?>.png" align="absmiddle" /></td>
          </tr>
		<?php } ?>
          <tr>
            <td width="45%" valign="middle" align="right" class="tb1">Code:</td>
            <td width="55%" valign="middle"><?php echo $row['code']; ?></td>
          </tr>
          <tr>
            <td valign="middle" align="right" class="tb1">Country Name:</td>
            <td valign="middle"><input type="text" name="country_name" id="country_name" value="<?php echo $row['name']; ?>" size="32" class="textbox" /></td>
			</tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Signup Page</td>
				<td valign="middle"><input type="checkbox" class="checkbox" name="signup" value="1" <?php if ($row['signup'] == 1) echo "checked=\"checked\""; ?> /> <span class="note">show country on signup page</span></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sort Order:</td>
				<td valign="middle"><input type="text" class="textbox" name="sort_order" value="<?php echo $row['sort_order']; ?>" size="5" /></td>
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
              <td align="center" colspan="2" valign="bottom">
			  <input type="hidden" name="country_id" id="country_id" value="<?php echo (int)$row['country_id']; ?>" />
			  <input type="hidden" name="action" id="action" value="edit">
			  <input type="submit" class="submit" name="update" id="update" value="Update" />
              <input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='countries.php'" /></td>
            </tr>
          </table>
      </form>
      
	  <?php }else{ ?>
			<p align="center">Sorry, no country found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>