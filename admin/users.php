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


		// Delete users //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$userid = (int)$v;
					DeleteUser($userid);
				}

				header("Location: users.php?msg=deleted");
				exit();
			}	
		}

		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "fname": $rrorder = "fname"; break;
					case "lname": $rrorder = "lname"; break;
					case "email": $rrorder = "email"; break;
					case "country": $rrorder = "country"; break;
					case "reg_source": $rrorder = "reg_source"; break;
					case "ids": $rrorder = "user_id"; break;
					case "status": $rrorder = "status"; break;
					default: $rrorder = "user_id"; break;
				}
			}
			else
			{
				$rrorder = "user_id";
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
				$rorder = "desc";
			}
			if (isset($_GET['filter']) && $_GET['filter'] != "")
			{
				$filter	= mysql_real_escape_string(trim(getGetParameter('filter')));
				$filter_by = " WHERE (username LIKE '%".$filter."%' OR email LIKE '%".$filter."%' OR fname LIKE '%".$filter."%' OR lname LIKE '%".$filter."%' OR reg_source='".$filter."' OR ip='".$filter."')";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }

		$from = ($page-1)*$results_per_page;

		$query = "SELECT *, DATE_FORMAT(created, '%e %b %Y') AS signup_date FROM cashbackengine_users $filter_by ORDER BY ".$rrorder." ".$rorder." LIMIT ".$from.",".$results_per_page;
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM cashbackengine_users".$filter_by;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


		$title = "Members";

		require_once ("inc/header.inc.php");

?>

       <div id="addnew"><a class="addnew" href="user_add.php">Add Member</a></div>

       <h2>Members</h2>

        <?php if ($total > 0) { ?>

			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added": echo "User has been successfully added"; break;
						case "updated": echo "User information has been successfully edited"; break;
						case "deleted": echo "User has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>


		<form id="form1" name="form1" method="get" action="">
		<table bgcolor="#F9F9F9" width="100%" border="0" cellpadding="3" cellspacing="0" align="center">
		<tr>
		<td nowrap="nowrap" width="47%" valign="middle" align="left">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="ids" <?php if ($_GET['column'] == "ids") echo "selected"; ?>>Signup Date</option>
			<option value="fname" <?php if ($_GET['column'] == "fname") echo "selected"; ?>>First Name</option>
			<option value="lname" <?php if ($_GET['column'] == "lname") echo "selected"; ?>>Last Name</option>
			<option value="email" <?php if ($_GET['column'] == "email") echo "selected"; ?>>Email</option>
			<option value="country" <?php if ($_GET['column'] == "country") echo "selected"; ?>>Country</option>
			<option value="reg_source" <?php if ($_GET['column'] == "reg_source") echo "selected"; ?>>Signup Source</option>
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
			</td>
			<td nowrap="nowrap" width="30%" valign="middle" align="left">
				<div class="admin_filter">
					<input type="text" name="filter" value="<?php echo $filter; ?>" class="textbox" size="30" /> <input type="submit" class="submit" value="Search" />
					<?php if (isset($filter) && $filter != "") { ?><a title="Cancel Search" href="users.php"><img align="absmiddle" src="images/icons/delete_filter.png" border="0" alt="Cancel Search" /></a><?php } ?> 
				</div>
			</td>
			<td nowrap="nowrap" width="35%" valign="middle" align="right">
			   Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>
			</form>

			<form id="form2" name="form2" method="post" action="">
            <table align="center" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr bgcolor="#DCEAFB" align="center">
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkbox" /></th>
				<th width="10%">ID</th>
				<th width="25%">Name</th>
				<th width="25%">Email</th>
				<th width="10%">Country</th>
				<th width="15%">Balance</th>
				<th width="10%">Clicks</th>
				<th width="10%">Status</th>
				<th width="14%">Signup Date</th>
				<th width="12%">Actions</th>
			</tr>
			 <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
			 <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><input type="checkbox" class="checkbox" name="id_arr[<?php echo $row['user_id']; ?>]" id="id_arr[<?php echo $row['user_id']; ?>]" value="<?php echo $row['user_id']; ?>" /></td>
					<td nowrap="nowrap" align="center" valign="middle"><?php echo $row['user_id']; ?></td>
					<td align="left" valign="middle"><a href="user_details.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $page; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
					<td align="left" valign="middle"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
					<td align="center" valign="middle"><?php echo GetCountry($row['country'], $show_only_icon = 1); ?></td>
					<td align="center" valign="middle"><a style="color: #000" href="user_payments.php?id=<?php echo $row['user_id']; ?>"><?php echo GetUserBalance($row['user_id']); ?></a></td>
					<td align="center" valign="middle"><a href="clicks.php?user=<?php echo $row['user_id']; ?>"><?php echo GetUserClicksTotal($row['user_id']); ?></a></td>
					<td align="center" valign="middle"><?php echo ($row['status'] == "inactive") ? "<span class='inactive_s' title='inactive'>&nbsp;</span>" : "<span class='active_s' title='active'>&nbsp;</span>"; ?></td>
					<td align="center" valign="middle"><?php echo $row['signup_date']; ?></td>
					<td nowrap="nowrap" align="center" valign="middle" >
						<a href="money2user.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Transfer Money"><img src="images/addmoney.gif" border="0" alt="Transfer Money" /></a>
						<a href="user_details.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="user_edit.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this user?') )location.href='user_delete.php?id=<?php echo $row['user_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
			</tr>
			<?php } ?>
			<tr>
				<td style="border-top: 1px solid #F5F5F5" colspan="10" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
			</tr>
			<tr>
				<td colspan="10" align="center">
					<?php echo ShowPagination("users",$results_per_page,"users.php?column=".$_GET['column']."&order=".$_GET['order']."&show=$results_per_page&",$filter_by); ?>
				</td>
			</tr>
            </table>
			</form>

		</table>

        <?php }else{ ?>
				<?php if (isset($filter)) { ?>
					<div class="info_box">No member found. <a href="users.php">Search again &#155;</a></div>
				<?php }else{ ?>
					<div class="info_box">There are no members at this time.</div>
				<?php } ?>
        <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>