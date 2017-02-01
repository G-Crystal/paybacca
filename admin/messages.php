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


	function getRepliesNum($message_id)
	{
		global $userid;
		$message_id = (int)$message_id;
		$query = "SELECT COUNT(answer_id) as total_replies FROM cashbackengine_messages_answers WHERE message_id='$message_id' AND is_admin='1'";
		$result = smart_mysql_query($query);
		$row = mysql_fetch_array($result);
		$total_replies = $row['total_replies'];

		if ($total_replies > 0) 
			return "<span class=''><font color='#47C8F7'>".$total_replies."</font></span>";
		else
			return "<span class=''><font color='#CECECE'>".$total_replies."</font></span>";
	}



		// Delete messages //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$mid = (int)$v;
					DeleteMessage($mid);
				}

				header("Location: messages.php?msg=deleted");
				exit();
			}
		}


		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "subject": $rrorder = "subject"; break;
					case "sender": $rrorder = "u.lname"; break;
					case "ids": $rrorder = "message_id"; break;
					default: $rrorder = "message_id"; break;
				}
			}
			else
			{
				$rrorder = "message_id";
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
		///////////////////////////////////////////////////////

		if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
		$from = ($page-1)*$results_per_page;

		$query = "SELECT m.*, DATE_FORMAT(m.created, '%e %b %Y %h:%i %p') AS message_date, u.fname, u.lname FROM cashbackengine_messages m LEFT JOIN cashbackengine_users u ON m.user_id=u.user_id WHERE m.is_admin='0' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";

		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$query2 = "SELECT * FROM cashbackengine_messages WHERE is_admin='0'";
		$result2 = smart_mysql_query($query2);
        $total = mysql_num_rows($result2);

		$cc = 0;


	$title = "Messages from members";
	require_once ("inc/header.inc.php");

?>

       <h2>Messages from members</h2>

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "sent": echo "Message has been successfully sent"; break;
						case "deleted": echo "Message has been successfully deleted"; break;
					}

				?>
			</div>
			<?php } ?>

		<table bgcolor="#F9F9F9" align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
		<td valign="top" align="left" width="50%">
            <form id="form1" name="form1" method="get" action="">
           Sort by: 
          <select name="column" id="column" onChange="document.form1.submit()">
			<option value="ids" <?php if ($_GET['column'] == "ids") echo "selected"; ?>>Date</option>
			<option value="sender" <?php if ($_GET['column'] == "sender") echo "selected"; ?>>Sender</option>
			<option value="subject" <?php if ($_GET['column'] == "subject") echo "selected"; ?>>Subject</option>
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
            </form>
			</td>
			<td valign="top" width="45%" align="right">
			   Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>

			<form id="form2" name="form2" method="post" action="">
            <table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkbox" /></th>
				<th width="20%">From</th>
				<th width="40%">Subject</th>
				<th width="17%">Date</th>
				<th width="10%">Replies</th>
				<th width="10%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><input type="checkbox" class="checkbox" name="id_arr[<?php echo $row['message_id']; ?>]" id="id_arr[<?php echo $row['message_id']; ?>]" value="<?php echo $row['message_id']; ?>" /></td>
					<td nowrap="nowrap" align="left" valign="middle"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
					<td align="left" valign="middle">
						<a href="message_details.php?id=<?php echo $row['message_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View">
						<?php
							if (strlen($row["subject"]) > 100) $msubject = substr($row["subject"], 0, 100)."..."; else $msubject = $row["subject"];
							if ($row['viewed'] == 0) echo "<b>".$msubject."</b>"; else echo $msubject;
						?>
						</a>
					</td>
					<td align="center" valign="middle"><?php echo $row['message_date']; ?></td>
					<td align="center" valign="middle"><a href="message_details.php?id=<?php echo $row['message_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><?php echo getRepliesNum($row['message_id']); ?></a></td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="message_details.php?id=<?php echo $row['message_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="message_reply.php?id=<?php echo $row['message_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Reply"><img src="images/reply.gif" border="0" alt="Reply" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this message?') )location.href='message_delete.php?id=<?php echo $row['message_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
				<td style="border-top: 1px solid #F5F5F5" colspan="6" align="left">
					<input type="hidden" name="column" value="<?php echo $rrorder; ?>" />
					<input type="hidden" name="order" value="<?php echo $rorder; ?>" />
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="action" value="delete" />
					<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
				</td>
				</tr>
				  <tr>
				  <td colspan="6" align="center">
					<?php echo ShowPagination("messages",$results_per_page,"messages.php?column=$rrorder&order=$rorder&show=$results_per_page&","WHERE is_admin='0'"); ?>
				  </td>
				  </tr>
            </table>
			</form>		

		</table>

        <?php }else{ ?>
				<div class="info_box">There are no messages at this time.</div>
        <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>