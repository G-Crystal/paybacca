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


	$query = "SELECT * FROM cashbackengine_categories ORDER BY sort_order, name";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$cc = 0;

	$title = "Categories";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="category_add.php">Add Category</a></div>

		<h2>Categories</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:60%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Category was successfully added"; break;
						case "exists":	echo "Sorry, category with this name is exists"; break;
						case "updated": echo "Category has been successfully edited"; break;
						case "deleted": echo "Category has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>

			<table align="center" class="tbl" width="60%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th class="noborder" width="5%">&nbsp;</th>
				<th width="65%">Category Name</th>
				<th width="15%">Retailers</th>
				<th width="15%">Actions</th>
			</tr>
             <?php $allcategories = array(); $allcategories = CategoriesList(0); foreach ($allcategories as $category_id => $category_name) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center"><img src="images/icons/cat.png" /></td>
					<td align="left" valign="middle" class="row_title"><a href="category_edit.php?id=<?php echo $category_id; ?>"><?php echo $category_name; ?></a></td>
					<td align="center" valign="middle"><?php echo CategoryTotalItems($category_id); ?></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="category_edit.php?id=<?php echo $category_id; ?>" title="Edit"><img border="0" alt="Edit" src="images/edit.png" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this category?') )location.href='category_delete.php?id=<?php echo $category_id; ?>'" title="Delete"><img border="0" alt="Delete" src="images/delete.png" /></a>
					</td>
				  </tr>
			<?php } ?>
            </table>
          
		  <?php }else{ ?>
				<div class="info_box">There are no categories at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>