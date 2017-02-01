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


	if (isset($_POST['action']) && $_POST['action'] == "editcontent")
	{
		$language			= mysql_real_escape_string($_POST['language']);
		$content_id			= (int)getPostParameter('cid');
		$link_title			= mysql_real_escape_string(getPostParameter('link_title'));
		$page_title			= mysql_real_escape_string($_POST['page_title']);
		$page_text			= mysql_real_escape_string($_POST['page_text']);
		$meta_description	= mysql_real_escape_string(getPostParameter('meta_description'));
		$meta_keywords		= mysql_real_escape_string(getPostParameter('meta_keywords'));
		$page_location		= mysql_real_escape_string(getPostParameter('page_location'));
		$status				= mysql_real_escape_string(getPostParameter('status'));

		unset($errs);
		$errs = array();

		if (!($page_title && $page_text))
		{
			$errs[] = "Please fill in all required fields";
		}

		if (count($errs) == 0)
		{
			$sql = "UPDATE cashbackengine_content SET language='$language', link_title='$link_title', title='$page_title', description='$page_text', page_location='$page_location', page_url='', meta_description='$meta_description', meta_keywords='$meta_keywords', status='$status', modified=NOW() WHERE content_id='$content_id' LIMIT 1";

			if (smart_mysql_query($sql))
			{
				header("Location: content.php?msg=updated");
				exit();
			}
		}
		else
		{
			$allerrors = "";
			foreach ($errs as $errorname)
				$allerrors .= "&#155; ".$errorname."<br/>\n";
		}
	}


	if (isset($_GET['id']) && is_numeric($_GET['id'])) { $cid = (int)$_GET['id']; } else { $cid = (int)$_POST['cid']; }
	
	$query = "SELECT * FROM cashbackengine_content WHERE content_id='$cid' LIMIT 1";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);


	$title = "Edit Content";
	require_once ("inc/header.inc.php");

?>
 
      <?php
		
		if ($total > 0)
		{
			$row = mysql_fetch_array($result);
      ?>

        <h2>Edit Content</h2>

		<?php if (isset($allerrors) && $allerrors != "") { ?>
			<div class="error_box"><?php echo $allerrors; ?></div>
		<?php } ?>

        <form action="" method="post">
          <table bgcolor="#F9F9F9" width="100%" align="center" cellpadding="2" cellspacing="3"  border="0">
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1"><span class="req">* </span>Page Title:</td>
            <td valign="top"><input type="text" name="page_title" id="page_title" value="<?php echo $row['title']; ?>" size="80" class="textbox" /></td>
          </tr>
		  <?php if ($row['content_id'] > 7) { ?>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1"><span class="req">* </span>Link Title:</td>
            <td valign="top"><input type="text" name="link_title" id="link_title" value="<?php echo $row['link_title']; ?>" size="40" class="textbox" /></td>
          </tr>
		  <?php } ?>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">&nbsp;</td>
            <td valign="top">
				<textarea cols="80" id="editor" name="page_text" rows="10"><?php echo stripslashes($row['description']); ?></textarea>
				<script type="text/javascript" src="./js/ckeditor/ckeditor.js"></script>
				<script>
					CKEDITOR.replace( 'editor' );
				</script>		
			</td>
          </tr>
          <tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="112" rows="2" class="textbox2" style="width: 99%"><?php echo strip_tags($row['meta_description']); ?></textarea></td>
          </tr>
          <tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo $row['meta_keywords']; ?>" size="115" class="textbox" style="width: 99%" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1">Page Name:</td>
            <td valign="top"><?php echo $row['name']; ?></td>
          </tr>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1">Language:</td>
            <td valign="top">
				<select name="language" onChange="document.form1.submit()">
				<option value="">-- any --</option>
				<?php

					$lang_sql = "SELECT * FROM cashbackengine_languages WHERE status='active' ORDER BY sort_order, language";
					$lang_result = smart_mysql_query($lang_sql);

					if (mysql_num_rows($lang_result) > 0) {
						while ($lang_row = mysql_fetch_array($lang_result)) {
				?>
					<option value="<?php echo $lang_row['language']; ?>" <?php if ($row['language'] == $lang_row['language']) echo 'selected="selected"'; ?>><?php echo $lang_row['language']; ?></option>

				<?php 
					}
						}
				?>
				</select>			
			</td>
          </tr>
		  <?php if ($row['content_id'] > 7) { ?>
          <tr>
            <td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1">Add link to:</td>
            <td valign="top">
				<select name="page_location">
					<option value="">----------</option>
					<option value="top" <?php if ($row['page_location'] == 'top') echo "selected='selected'"; ?>>Top menu</option>
					<option value="footer" <?php if ($row['page_location'] == 'footer') echo "selected='selected'"; ?>>Footer menu</option>
					<option value="topfooter" <?php if ($row['page_location'] == 'topfooter') echo "selected='selected'"; ?>>Top &amp; footer</option>
				</select>
			</td>
          </tr>
		  <tr>
			<td nowrap="nowrap" width="35" valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
				</select>
			</td>
          </tr>
		  <?php } ?>
          <tr>
            <td colspan="2" align="center" valign="bottom">
				<input type="hidden" name="cid" id="cid" value="<?php echo (int)$row['content_id']; ?>" />
				<input type="hidden" name="action" id="action" value="editcontent" />
				<input type="submit" name="update" id="update" class="submit" value="Update" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='content.php'" />
		  </td>
          </tr>
        </table>
      </form>

      <?php }else{ ?>
				<p align="center">Sorry, no record found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>