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


	$query = "SELECT * FROM cashbackengine_pmethods ORDER BY status";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$cc = 0;


	$title = "Payment Methods";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="pmethod_add.php">Add Payment Method</a></div>

		<h2>Payment Methods</h2>		

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:50%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Payment method was successfully added"; break;
						case "updated": echo "Payment method has been successfully edited"; break;
						case "deleted": echo "Payment method has been successfully deleted"; break;
					}
				?>
			</div>
			<?php } ?>


			<table align="center" width="50%" class="tbl" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="55%">Payment Method</th>
				<th width="20%">Status</th>
				<th width="25%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>		  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle" class="row_title">
						<a href="pmethod_edit.php?id=<?php echo $row['pmethod_id']; ?>"><?php echo $row['pmethod_title']; ?></a>
					</td>
					<td align="center" valign="middle">
						<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="pmethod_edit.php?id=<?php echo $row['pmethod_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this payment method?') )location.href='pmethod_delete.php?id=<?php echo $row['pmethod_id']; ?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
            </table>

          <?php }else{ ?>
				<div class="info_box">There are no payment methods at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>