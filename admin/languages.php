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


	$languages_dir = "../language/";
	$languages = scandir($languages_dir); 
	$array = array(); 
	foreach ($languages as $file)
	{
		if (is_file($languages_dir.$file) && strstr($file, ".inc.php"))
		{
			$language = mysql_real_escape_string(str_replace(".inc.php","",$file));
			$check_query = smart_mysql_query("SELECT * FROM cashbackengine_languages WHERE language='$language'");
			if (mysql_num_rows($check_query) == 0)
			{
				smart_mysql_query("INSERT INTO cashbackengine_languages SET language='$language', status='inactive'");
			}
		}
	}

	$query = "SELECT * FROM cashbackengine_languages ORDER BY status, language_id";
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	$cc = 0;


	$title = "Site Languages";
	require_once ("inc/header.inc.php");

?>

		<div id="addnew"><a class="addnew" href="language_add.php">Add Language</a></div>

		<h2>Site Languages</h2>		

        <?php if ($total > 0) { ?>


			<?php if (isset($_GET['msg']) && $_GET['msg'] != "") { ?>
			<div style="width:44%;" class="success_box">
				<?php

					switch ($_GET['msg'])
					{
						case "added":	echo "Language was successfully added"; break;
						case "updated": echo "Language has been successfully updated"; break;
					}
				?>
			</div>
			<?php } ?>


			<table align="center" width="44%" class="tbl" border="0" cellpadding="3" cellspacing="0">
			<tr>
				<th width="15%">&nbsp;</th>
				<th width="45%">Language</th>
				<th width="20%">Status</th>
				<th width="25%">Actions</th>
			</tr>
             <?php while ($row = mysql_fetch_array($result)) { $cc++; ?>		  
				  <tr class="<?php if (($cc%2) == 0) echo "even"; else echo "odd"; ?>">
					<td align="center" valign="middle"><img src="<?php echo SITE_URL; ?>images/flags/<?php echo $row['language_code']; ?>.png" align="absmiddle" /></td>
					<td align="left" valign="middle" class="row_title"><?php echo $row['language']; ?></td>
					<td align="left" valign="middle"><?php if ($row['status'] == "inactive") echo "<span class='inactive_s'>".$row['status']."</span>"; else echo "<span class='active_s'>".$row['status']."</span>"; ?></td>
					<td nowrap="nowrap" align="center" valign="middle"><a href="language_edit.php?id=<?php echo $row['language_id']; ?>" title="Edit"><img src="images/edit.png" border="0" alt="Edit" /></a></td>
				  </tr>
			<?php } ?>
            </table>

          <?php }else{ ?>
				<div class="info_box">There are no languages found.</div>
          <?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>