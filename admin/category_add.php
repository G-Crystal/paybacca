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


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		$category_name			= mysql_real_escape_string(getPostParameter('catname'));
		$category_description	= mysql_real_escape_string(nl2br(getPostParameter('description')));
		$parent_category		= (int)getPostParameter('parent_id');
		$meta_description		= mysql_real_escape_string(nl2br(getPostParameter('meta_description')));
		$meta_keywords			= mysql_real_escape_string(getPostParameter('meta_keywords'));
		$sort_order				= (int)getPostParameter('sort_order');
 
		if (isset($category_name) && $category_name != "")
		{
			$check_query = smart_mysql_query("SELECT * FROM cashbackengine_categories WHERE parent_id='$parent_category' AND name='$category_name' AND category_url='$category_url'");
			if (mysql_num_rows($check_query) == 0)
			{
				$sql = "INSERT INTO cashbackengine_categories SET parent_id='$parent_category', name='$category_name', description='$category_description', category_url='', meta_description='$meta_description', meta_keywords='$meta_keywords', sort_order='$sort_order'";

				if (smart_mysql_query($sql))
				{
					header("Location: categories.php?msg=added");
					exit();
				}
			}
			else
			{
				header("Location: categories.php?msg=exists");
				exit();
			}
		}
	}

	$title = "Add Category";
	require_once ("inc/header.inc.php");

?>

		  <h2>Add Category</h2>

		  <form action="" method="post">
		  <table bgcolor="#F9F9F9" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
          <tr>
            <td colspan="2" align="right" valign="top"><font color="red">* denotes required field</font></td>
          </tr>
          <tr>
            <td width="30%" nowrap="nowrap" valign="middle" align="right" class="tb1"><span class="req">* </span>Category Name:</td>
			<td align="left">
				<input type="text" name="catname" id="catname" value="<?php echo getPostParameter('catname'); ?>" size="40" class="textbox" />
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Parent Category:</td>
			<td align="left">
				<select name="parent_id">
					<option value=""> ---------- None ---------- </option>
					<?php CategoriesDropDown (0); ?>
				</select>
			</td>
          </tr>
          <tr>
            <td nowrap="nowrap" valign="middle" align="right" class="tb1">Description:</td>
			<td align="left" valign="top"><textarea name="description" cols="75" rows="5" class="textbox2"><?php echo getPostParameter('description'); ?></textarea></select>
			</td>
          </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Description:</td>
				<td valign="top"><textarea name="meta_description" cols="75" rows="2" class="textbox2"><?php echo getPostParameter('meta_description'); ?></textarea></td>
            </tr>
			<tr>
				<td valign="middle" align="right" class="tb1">Meta Keywords:</td>
				<td valign="top"><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo getPostParameter('meta_keywords'); ?>" size="78" style="width: 390px" class="textbox" /></td>
            </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Sort Order:</td>
				<td valign="middle"><input type="text" class="textbox" name="sort_order" value="<?php echo getPostParameter('sort_order'); ?>" size="5" /></td>
            </tr>
          <tr>
			<td>&nbsp;</td>
			<td valign="middle" align="left">
				<input type="hidden" name="action" id="action" value="add" />
				<input type="submit" name="add" id="add" class="submit" value="Add Category" />
		    </td>
          </tr>
		  </table>
		  </form>


<?php require_once ("inc/footer.inc.php"); ?>