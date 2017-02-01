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


	$query = "SELECT * FROM cashbackengine_email_templates ORDER BY template_id ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$cc = 0;

	$title = "Email Templates";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="etemplate_add.php">Create New Template</a></div>

		<h2>Email Templates</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:60%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Email template was successfully added"; break;
						case "updated": echo "Email template has been successfully edited"; break;
						case "deleted": echo "Email template has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>

			<table align="center" class="tbl" width="60%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th class="noborder" width="1%">&nbsp;</th>
				<th width="55%">Template Name</th>
				<th width="25%">Language</th>
				<th width="20%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center"><img src="images/icons/etemplate.png" /></td>
					<td align="left" valign="middle" class="row_title"><a href="etemplate_details.php?id=<?php echo $row['template_id']; ?>"><?php echo GetETemplateTitle($row['email_name']); ?></a></td>
					<td align="center" valign="middle"><?php echo $row['language']; ?></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="etemplate_details.php?id=<?php echo $row['template_id']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="etemplate_edit.php?id=<?php echo $row['template_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<?php if ($row['template_id'] > 8) { ?>
							<a href="#" onclick="if (confirm('Are you sure you really want to delete this email template?') )location.href='etemplate_delete.php?id=<?php echo $row['template_id']; ?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
						<?php } ?>
					</td>
				  </tr>
			<?php } ?>
            </table>

          <?php }else{ ?>
				<p align="center">Sorry, no email templates found.</p>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>