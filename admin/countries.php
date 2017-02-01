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


	if (isset($_GET['show']) && $_GET['show'] == "all")
		$results_per_page = 500;
	else
		$results_per_page = 15;

		// Delete countries //
		if (isset($_POST['action']) && $_POST['action'] == "delete")
		{
			$ids_arr	= array();
			$ids_arr	= $_POST['id_arr'];

			if (count($ids_arr) > 0)
			{
				foreach ($ids_arr as $v)
				{
					$countryid = (int)$v;
					DeleteCountry($countryid);
				}

				header("Location: countries.php?msg=deleted");
				exit();
			}	
		}


	if (isset($_POST['action']) && $_POST['action'] == "add")
	{
		$country_name	= mysql_real_escape_string(getPostParameter('country_name'));
		$signup			= (int)getPostParameter('signup');

		if (isset($country_name) && $country_name != "")
		{
			$check_query = smart_mysql_query("SELECT * FROM cashbackengine_countries WHERE name='$country_name'");
			
			if (mysql_num_rows($check_query) == 0)
			{
				$sql = "INSERT INTO cashbackengine_countries SET name='$country_name', signup='$signup', status='active'";

				if (smart_mysql_query($sql))
				{
					header("Location: countries.php?msg=added");
					exit();
				}
			}
			else
			{
				header("Location: countries.php?msg=exists");
				exit();
			}
		}
	}


	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$query = "SELECT * FROM cashbackengine_countries ORDER BY sort_order, name LIMIT $from, $results_per_page";
	$result = smart_mysql_query($query);

	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_countries ORDER BY sort_order, name");
	$total = mysql_num_rows($total_result);

	$cc = 0;


	$title = "Countries";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="javascript:void(0);" onclick="$('#add_country').toggle();">Add Country</a></div>

		<h2>Countries</h2>

        <?php if ($total > 0) { ?>

			<div id="add_country" style="display: none; width: 500px; background: #F7F7F7; padding: 10px 0; margin: 0 auto;">
			  <form action="" method="post">
			  <table align="center" width="400" border="0" cellpadding="3" cellspacing="0">
			  <tr>
				<td valign="middle" align="right" class="tb1">Name:</td>
				<td align="left">
					<input type="text" name="country_name" id="country_name" value="" size="27" class="textbox" />
				</td>
				<td nowrap="nowrap" valign="middle" align="left"><input type="checkbox" class="checkbox" name="signup" value="1" <?php if (getPostParameter('signup') == 1) echo "checked=\"checked\""; ?> /> Signup page</td>
				<td align="left">
					<input type="hidden" name="action" id="action" value="add" />
					<input type="submit" name="add" id="add" class="submit" value="Add Country" />
				</td>
			  </tr>
			  </table>
			  </form>
			 </div>

			<form id="form2" name="form2" method="post" action="">
			<table align="center" width="500" border="0" cellpadding="3" cellspacing="0">
			<tr align="center">
				<td colspan="6" align="right" valign="bottom">
					<a href="countries.php?show=all" style="color: #777">show all <b><?php echo $total; ?></b> countires &#155;</a>
					<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
					<div style="width:500px;" class="success_box">
						<?php

							switch ($_GET['msg'])
							{
								case "added":	echo "Country was successfully added"; break;
								case "exists":	echo "Sorry, country exists"; break;
								case "updated": echo "Country has been successfully edited"; break;
								case "deleted": echo "Country has been successfully deleted"; break;
							}

						?>
					</div>
					<?php } ?>
				</td>
			</tr>
			<tr bgcolor="#DCEAFB" align="center">
				<th width="3%"><input type="checkbox" name="selectAll" onclick="checkAll();" class="checkboxx" /></th>
				<th width="7%"></th>
				<th width="50%">Country Name</th>
				<th width="12%">Signup Page <sup><a href="#" title="These countries will be displayed on Signup page.">?</a></sup></th>
				<th width="15%">Status</th>
				<th width="25%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><input type="checkbox" class="checkboxx" name="id_arr[<?php echo $row['country_id']; ?>]" id="id_arr[<?php echo $row['country_id']; ?>]" value="<?php echo $row['country_id']; ?>" /></td>
					<td nowrap="nowrap" valign="middle" align="center"><img src="../images/flags/<?php echo strtolower($row['code']); ?>.png" align="absmiddle" /></td>
					<td nowrap="nowrap" align="left" valign="middle" class="row_title"><a href="country_edit.php?id=<?php echo $row['country_id']; ?>"><?php echo $row['name']; ?></a></td>
					<td nowrap="nowrap" valign="middle" align="center"><?php echo ($row['signup'] == 1) ? "<img src='./images/icons/yes.png' align='absmiddle'>" : "<img src='./images/icons/no.png' align='absmiddle'>"; ?></td>
					<td nowrap="nowrap" valign="middle" align="center">
						<?php
							switch ($row['status'])
							{
								case "active": echo "<span class='active_s'>&nbsp;</span>"; break;
								case "inactive": echo "<span class='inactive_s'>nbsp;</span>"; break;
								default: echo "<span class='default_status'>".$row['status']."</span>"; break;
							}
						?>					
					</td>
					<td nowrap="nowrap" align="center" valign="middle">
						<a href="country_edit.php?id=<?php echo $row['country_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a>
						<a href="#" onclick="if (confirm('Are you sure you really want to delete this country?') )location.href='country_delete.php?id=<?php echo $row['country_id']; ?>'" title="Delete"><img src="images/delete.png" border="0" alt="Delete" /></a>
					</td>
				  </tr>
			<?php } ?>
				<tr>
					<td style="border-top: 1px solid #F5F5F5" colspan="6" align="left">
						<input type="hidden" name="page" value="<?php echo $page; ?>" />
						<input type="hidden" name="action" value="delete" />
						<input type="submit" class="submit" name="GoDelete" id="GoDelete" value="Delete Selected" />
					</td>
				</tr>
            </table>
			</form>

				<?php echo ShowPagination("countries",$results_per_page,"?",""); ?>
          
		  <?php }else{ ?>
				<div class="info_box">There are no countries at this time.</div>
          <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>