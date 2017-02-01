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


	$results_per_page = 20;


		// Delete News //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$nid = (int)$v;
					DeleteNews($nid);
				}

				header("Location: news.php?msg=deleted");
				exit();
			}
		}


		////////////////// filter  //////////////////////
			if (isset($_GET['column']) && $_GET['column'] != "")
			{
				switch ($_GET['column'])
				{
					case "news_id": $rrorder = "news_id"; break;
					case "added": $rrorder = "added"; break;
					case "status": $rrorder = "status"; break;
					default: $rrorder = "news_id"; break;
				}
			}
			else
			{
				$rrorder = "news_id";
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

		$query = "SELECT *, DATE_FORMAT(added, '%e %M %Y %h:%i %p') AS news_date FROM cashbackengine_news ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
	
		$total_result = smart_mysql_query("SELECT * FROM cashbackengine_news ORDER BY news_title ASC");
		$total = mysql_num_rows($total_result);

		$result = smart_mysql_query($query);
		$total_on_page = mysql_num_rows($result);

		$cc = 0;


	$title = "News";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="news_add.php">Add News</a></div>

		<h2>News</h2>

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added": echo "News has been successfully added"; break;
						case "updated": echo "News has been successfully edited"; break;
						case "deleted": echo "News has been successfully deleted"; break;
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
			<option value="added" <?php if ($_GET['column'] == "ids") echo "selected"; ?>>Date</option>
			<option value="status" <?php if ($_GET['column'] == "status") echo "selected"; ?>>Status</option>
          </select>
          <select name="order" id="order" onChange="document.form1.submit()">
			<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>>Descending</option>
			<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>>Ascending</option>
          </select>
            </form>
			</td>
			<td nowrap="nowrap" valign="top" width="45%" align="right">
			   Showing <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> of <?php echo $total; ?>
			</td>
			</tr>
			</table>

			<form id="form2" name="form2" method="post" action="">
            <table align="center" width="100%" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkbox" /></th>
				<th width="55%">News</th>
				<th width="20%">Date</th>
				<th width="12%">Status</th>
				<th width="12%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><input type="checkbox" class="checkbox" name="id_arr[<?php echo $row['news_id']; ?>]" id="id_arr[<?php echo $row['news_id']; ?>]" value="<?php echo $row['news_id']; ?>" /></td>
					<td align="left" valign="middle"><a href="news_details.php?id=<?php echo $row['news_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>"><?php echo $row["news_title"]; ?></a></td>
					<td align="center" valign="middle"><?php echo $row['news_date']; ?></td>
					<td align="center" valign="middle">
						<?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?>
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="news_details.php?id=<?php echo $row['news_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="View"><img src="images/view.png" border="0" alt="View" /></a>
						<a href="news_edit.php?id=<?php echo $row['news_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this news?') )location.href='news_delete.php?id=<?php echo $row['news_id']; ?>&pn=<?php echo $page; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>';" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
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
				  <td align="center" colspan="5">
					<?php echo ShowPagination("news",$results_per_page,"news.php?&author=$author_id&column=$rrorder&order=$rorder&"); ?>
				  </td>
				  </tr>
            </table>
			</form>		

		</table>

        <?php }else{ ?>
					<div class="info_box">There are no news at this time.</div>
        <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>