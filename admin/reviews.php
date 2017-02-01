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
	require_once("../inc/pagination.inc.php");
	require_once("./inc/admin_funcs.inc.php");


	// results per page
	if (isset($_GET['show']) && is_numeric($_GET['show']) && $_GET['show'] > 0)
		$results_per_page = (int)$_GET['show'];
	else
		$results_per_page = 10;


		// Delete reviews //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$reviewid = (int)$v;
					DeleteReview($reviewid);
				}

				header("Location: reviews.php?msg=deleted");
				exit();
			}
		}

		$where = "1=1";

		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "user_id": $rrorder = "user_id"; break;
					case "added": $rrorder = "added"; break;
					case "retailer_id": $rrorder = "retailer_id"; break;
					case "status": $rrorder = "status"; break;
					default: $rrorder = "added"; break;
				}
			}
			else
			{
				$rrorder = "added";
			}

			if (isset($_GET['order']) && $_GET['order'] != "")
			{
				switch ($_GET['order'])
				{
					case "asc": $rorder = "asc"; break;
					case "desc": $rorder = "desc"; break;
					default: $rorder = "asc"; break;
				}
			}
			else
			{
				$rorder = "asc";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		if (isset($_GET['store']) && is_numeric($_GET['store']))
		{
			$store = (int)$_GET['store'];
			$where .= " AND retailer_id='$store' ";
			$title2 = GetStoreName($store);
		}

		if (isset($_GET['user']) && is_numeric($_GET['user']))
		{
			$user = (int)$_GET['user'];
			$where .= " AND user_id='$user' ";
			$title2 = GetUsername($user)."'s";
		}

		$query = "SELECT *, DATE_FORMAT(added, '%e %b %Y %h:%i %p') AS date_added FROM cashbackengine_reviews WHERE $where ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM cashbackengine_reviews WHERE ".$where;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


	$title = $title2." Reviews";
	require_once ("inc/header.inc.php");

?>

		<h2><?php echo $title2; ?> Reviews</h2>		

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added": echo "Review has been successfully added"; break;
						case "updated": echo "Review has been successfully edited"; break;
						case "deleted": echo "Review has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>


		<form id="form1" name="form1" method="get" action="">
		<table bgcolor="#F9F9F9" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
		<td nowrap="nowrap" width="65%" valign="middle" align="left">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>>Newest</option>
			<option value="user_id" <?php if ($_GET['column'] == "user_id") echo "selected"; ?>>Username</option>
			<option value="retailer_id" <?php if ($_GET['column'] == "retailer_id") echo "selected"; ?>>Store</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
		  &nbsp;&nbsp;View: 
          <select name="show" id="show" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
			<option value="111111111" <?php if ($_GET['show'] == "111111111") echo "selected"; ?>>ALL</option>
          </select>
			<?php if ($user) { ?><input type="hidden" name="user" value="<?php echo $user; ?>" /><?php } ?>
			<?php if ($store) { ?><input type="hidden" name="store" value="<?php echo $store; ?>" /><?php } ?>
			</td>
			<td nowrap="nowrap" width="35%" valign="middle" align="right">
			   Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>
			</form>

			<form id="form2" name="form2" method="post" action="">
			<table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkbox" /></th>
				<th width="70%"><b>Review</b></th>
				<th width="17%"><b>Status</b></th>
				<th width="10%"><b>Actions</b></th>
			</tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>				  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td nowrap="nowrap" align="center" valign="middle"><input type="checkbox" class="checkbox" name="id_arr[<?php echo $row['review_id']; ?>]" id="id_arr[<?php echo $row['review_id']; ?>]" value="<?php echo $row['review_id']; ?>" /></td>
					<td align="left" valign="middle" class="row_title">
						<div style="margin: 5px 0;">
							by <a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a>
							<div style="float: right; font-size: 10px; color: #BABABA;"><?php echo $row['date_added']; ?></div>
						</div>
						<img src="images/icons/rating-<?php echo $row['rating']; ?>.gif" alt="<?php echo $row['rating']; ?> of 5" title="<?php echo $row['rating']; ?> of 5" align="left" />&nbsp;
						<b><?php echo $row['review_title']; ?></b>
						<br/>
						<?php 
							if (strlen($row['review']) > 350)
								echo substr($row['review'], 0, 350)."...";
							else
								echo $row['review'];
						?>
						<div style="width: 100%; border-top: 1px dotted #EEE; padding: 3px 0; margin: 3px 0;">
							<b>Store</b>: <a style="color: #777;" href="retailer_details.php?id=<?php echo $row['retailer_id']; ?>"><?php echo GetStoreName($row['retailer_id']); ?></a>
						</div>
					</td>
					<td align="center" valign="middle">
					<?php
						switch ($row['status'])
						{
							case "pending": echo "<span class='pending_status'>awaiting approval</span>"; break;
							case "active": echo "<span class='active_s'>".$row['status']."</span>"; break;
							case "inactive": echo "<span class='inactive_s'>".$row['status']."</span>"; break;
							default: echo "<span class='default_status'>".$row['status']."</span>"; break;
						}
					?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="review_details.php?id=<?php echo $row['review_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="review_edit.php?id=<?php echo $row['review_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this review?') )location.href='review_delete.php?id=<?php echo $row['review_id']; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>&pn=<?php echo $page?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
				<td style="border-top: 1px solid #F5F5F5" colspan="4" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
				</tr>
				<tr>
				  <td colspan="4" align="center">
					<?php
							$params = "";

							if ($user)		$params .= "user=$user&";
							if ($store)		$params .= "store=$store&";

							echo ShowPagination("reviews",$results_per_page,"reviews.php?".$params."column=$rrorder&order=$rorder&show=$results_per_page&", "WHERE ".$where);
					?>
				  </td>
				</tr>
            </table>
			</form>

          <?php }else{ ?>
					<div class="info_box">There are no reviews at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>