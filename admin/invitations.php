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


		// Delete invitations //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$invitationid = (int)$v;
					DeleteInvitation($invitationid);
				}

				header("Location: invitations.php?msg=deleted");
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
					case "sent_date": $rrorder = "sent_date"; break;
					default: $rrorder = "sent_date"; break;
				}
			}
			else
			{
				$rrorder = "sent_date";
			}

			if (isset($_GET['order']) && $_GET['order'] != "")
			{
				switch ($_GET['order'])
				{
					case "asc": $rorder = "asc"; break;
					case "desc": $rorder = "desc"; break;
					default: $rorder = "desc"; break;
				}
			}
			else
			{
				$rorder = "desc";
			}
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		if (isset($_GET['user']) && is_numeric($_GET['user']))
		{
			$user = (int)$_GET['user'];
			$where .= " AND user_id='$user' ";
			$title2 = GetUsername($user)."'s";
		}

		$query = "SELECT *, DATE_FORMAT(sent_date, '%e %b %Y %h:%i %p') AS date_sent FROM cashbackengine_invitations WHERE $where ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
		
		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM cashbackengine_invitations WHERE ".$where;
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


	$title = $title2." Invitations";
	require_once ("inc/header.inc.php");

?>

		<h2><?php echo $title2; ?> Invitations</h2>		

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "deleted": echo "Invitation has been successfully deleted"; break;
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
			<option value="sent_date" <?php if ($_GET['column'] == "sent_date") echo "selected"; ?>>Newest</option>
			<option value="user_id" <?php if ($_GET['column'] == "user_id") echo "selected"; ?>>Username</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
		  &nbsp;&nbsp;View: 
          <select name="show" id="order" onChange="document.form1.submit()">
			<option value="10" <?php if ($_GET['show'] == "10") echo "selected"; ?>>10</option>
			<option value="50" <?php if ($_GET['show'] == "50") echo "selected"; ?>>50</option>
			<option value="100" <?php if ($_GET['show'] == "100") echo "selected"; ?>>100</option>
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
				<th width="40%"><b>Recipients</b></th>
				<th width="20%"><b>User</b></th>
				<th width="20%"><b>Date Sent</b></th>
				<th width="10%"><b>Actions</b></th>
			</tr>
			<?php while ($row = mysql_fetch_array($result)) { $cc++; ?>				  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td nowrap="nowrap" align="center" valign="middle"><input type="checkbox" class="checkbox" name="id_arr[<?php echo $row['invitation_id']; ?>]" id="id_arr[<?php echo $row['invitation_id']; ?>]" value="<?php echo $row['invitation_id']; ?>" /></td>
					<td align="left" valign="middle">
						<?php 
								$recipients = explode("||", $row['recipients']);

								foreach ($recipients as $v)
								{
									if ($v != "")
									{
										$recipient = explode("|", $v);
										echo $recipient[0]." / ".$recipient[1]."<br/>";
									}
								}
						?>
					</td>
					<td align="left" valign="middle"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a></td>
					<td align="center" valign="middle"><div style="float: right; color: #8E8E8E;"><?php echo $row['date_sent']; ?></div></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="invitation_details.php?id=<?php echo $row['invitation_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this invitation?') )location.href='invitation_delete.php?id=<?php echo $row['invitation_id']; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>&pn=<?php echo $page?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
				<td style="border-top: 1px solid #F5F5F5" colspan="5" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
				</tr>
				<tr>
				  <td colspan="5" align="center">
					<?php
							$params = "";

							if ($user)	$params .= "user=$user&";

							echo ShowPagination("invitations",$results_per_page,"invitations.php?".$params."column=$rrorder&order=$rorder&show=$results_per_page&", "WHERE ".$where);
					?>
				  </td>
				</tr>
            </table>
			</form>

          <?php }else{ ?>
					<div class="info_box">There are no invitations at this time.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>