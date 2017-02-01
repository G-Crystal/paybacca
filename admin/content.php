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


	$query = "SELECT * FROM cashbackengine_content ORDER BY content_id ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$cc = 0;

	$title = "Content";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="content_add.php">Create New Page</a></div>

		<h2>Content</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:70%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Content was successfully added"; break;
						case "updated": echo "Content has been successfully edited"; break;
						case "deleted": echo "Content has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>


			<table align="center" class="tbl" width="70%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th class="noborder" width="1%">&nbsp;</th>
				<th width="55%">Page Title</th>
				<th width="25%">Language</th>
				<th width="20%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center"><img src="images/icons/content.png" /></td>
					<td align="left" valign="middle" class="row_title"><a href="content_details.php?id=<?php echo $row['content_id']; ?>"><?php echo $row['title']; ?></a></td>
					<td align="center" valign="middle"><?php echo $row['language']; ?></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="content_details.php?id=<?php echo $row['content_id']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="content_edit.php?id=<?php echo $row['content_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<?php if ($row['content_id'] > 7) { ?>
							<a href="#" onclick="if (confirm('Are you sure you really want to delete this page?') )location.href='content_delete.php?id=<?php echo $row['content_id']; ?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
						<?php } ?>
					</td>
				  </tr>
			<?php } ?>
            </table>

          <?php }else{ ?>
				<div class="info_box">There are no pages at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>