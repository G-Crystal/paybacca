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


	$query = "SELECT *, DATE_FORMAT(last_csv_upload, '%e %b %Y %h:%i %p') AS stats_update_date FROM cashbackengine_affnetworks ORDER BY status ASC";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$title = "Affiliate Networks";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew">
			<a style="color: #87CE04; padding-right: 20px;" href="http://www.cashbackengine.net/c/myproducts.php" target="_blank"><img src="images/icons/addons.png" align="absmiddle" /> <b>Buy Addons</b></a>
			<a class="addnew" href="affnetwork_add.php">Add Affiliate Network</a>
		</div>

		<h2>Affiliate Networks</h2>		

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:75%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Affiliate network was successfully added"; break;
						case "updated": echo "Affiliate network has been successfully edited"; break;
						case "deleted": echo "Affiliate network has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>

			<table align="center" class="tbl" width="75%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<th width="40%">Affiliate Network</td>
				<th width="15%">Retailers</td>
				<th width="15%">Cashback Update</td>
				<th width="15%">Status</td>
				<th width="15%">Actions</td>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { ?>
				 <tr>
					<td nowrap="nowrap" style="border-bottom: 1px #DCEAFB dotted;" valign="middle" class="row_title" align="center">
						<?php if ($row['image'] != "") { ?>
							<a href="affnetwork_edit.php?id=<?php echo $row['network_id']; ?>"><img src="images/networks/<?php echo $row['image']; ?>" alt="<?php echo $row['network_name']; ?>" title="<?php echo $row['network_name']; ?>" align="absmiddle" border="0" /></a>
						<?php }else{ ?>
							<a href="affnetwork_edit.php?id=<?php echo $row['network_id']; ?>"><?php echo $row['network_name']; ?></a>
						<?php } ?>
					</td>
					<td align="center" style="border-bottom: 1px #DCEAFB dotted;" valign="middle">
						<span style="color:#FFF; background:#B7B6B8; padding:3px 6px;"><?php echo NetworkTotalRetailers($row['network_id']); ?></span>
					</td>
					<td align="center" style="border-bottom: 1px #DCEAFB dotted;" valign="middle"><?php echo ($row['last_csv_upload'] != "0000-00-00 00:00:00") ? $row['stats_update_date'] : "---"; ?></td>
					<td align="center" style="border-bottom: 1px #DCEAFB dotted;" valign="middle">
						<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
					</td>
					<td nowrap="nowrap" style="border-bottom: 1px #DCEAFB dotted;" align="center" valign="middle">
						<a href="affnetwork_edit.php?id=<?php echo $row['network_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<?php if ($row['network_id'] > 14) { ?><a href="#" onclick="if (confirm('Are you sure you really want to delete this affiliate network?') )location.href='affnetwork_delete.php?id=<?php echo $row['network_id']; ?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a><?php } ?>
					</td>
				  </tr>
			<?php } ?>
            </table>
          
		  <?php }else{ ?>
				<div class="info_box">There are no affiliate networks at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>