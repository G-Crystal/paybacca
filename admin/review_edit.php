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


	$pn = (int)$_GET['pn'];

	if (isset($_POST["action"]) && $_POST["action"] == "edit")
	{
			unset($errors);
			$errors = array();

			$review_id		= (int)getPostParameter('reviewid');
			$rating			= (int)getPostParameter('rating');
			$review_title	= mysql_real_escape_string(getPostParameter('review_title'));
			$retailer_id	= (int)getPostParameter('retailer_id');
			$review			= mysql_real_escape_string(getPostParameter('review'));
			$status			= mysql_real_escape_string(getPostParameter('status'));

			if (!($review_id && $review_title && $retailer_id && $rating && $status))
			{
				$errors[] = "Please fill in all fields";
			}

			if (count($errors) == 0)
			{
				smart_mysql_query("UPDATE cashbackengine_reviews SET review_title='$review_title', retailer_id='$retailer_id', rating='$rating', review='$review', status='$status' WHERE review_id='$review_id'");

				header("Location: reviews.php?msg=updated");
				exit();
			}
			else
			{
				$errormsg = "";
				foreach ($errors as $errorname)
					$errormsg .= "&#155; ".$errorname."<br/>";
			}
	}


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id	= (int)$_GET['id'];

		$query = "SELECT * FROM cashbackengine_reviews WHERE review_id='$id' LIMIT 1";
		$rs	= smart_mysql_query($query);
		$total = mysql_num_rows($rs);
	}


	$title = "Edit Review";
	require_once ("inc/header.inc.php");

?>


    <h2>Edit Review</h2>

	<?php if ($total > 0) {
		
		$row = mysql_fetch_array($rs);

	?>

	<?php if (isset($errormsg) && $errormsg != "") { ?>
		<div class="error_box"><?php echo $errormsg; ?></div>
	<?php } ?>

      <form action="" method="post" name="form1">
        <table bgcolor="#F9F9F9" width="100%" cellpadding="2" cellspacing="3"  border="0" align="center">
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1">By:</td>
            <td width="70%" valign="top"><a href="user_details.php?id=<?php echo $row['user_id']; ?>" class="user"><?php echo GetUsername($row['user_id']); ?></a></td>
          </tr>
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1">Store:</td>
            <td width="70%" valign="top">
			<select class="textbox2" id="retailer_id" name="retailer_id" style="width: 150px;">
				<option value="">--- Please select store ---</option>
				<?php

					$sql_retailers = smart_mysql_query("SELECT * FROM cashbackengine_retailers WHERE status='active' ORDER BY title ASC");
				
					while ($row_retailers = mysql_fetch_array($sql_retailers))
					{
						if ($row['retailer_id'] == $row_retailers['retailer_id']) $selected = " selected=\"selected\""; else $selected = "";

						echo "<option value=\"".$row_retailers['retailer_id']."\"".$selected.">".$row_retailers['title']."</option>";
					}
				?>	
			</select>
			</td>
			</tr>
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1">Rating:</td>
            <td width="70%" valign="top">
				<select class="textbox2" id="rating" name="rating" style="width: 150px;">
					<option value="">---------</option>
					<option value="5" <?php if ($row['rating'] == 5) echo "selected"; ?>>&#9733;&#9733;&#9733;&#9733;&#9733; - Excellent</option>
					<option value="4" <?php if ($row['rating'] == 4) echo "selected"; ?>>&#9733;&#9733;&#9733;&#9733; - Very Good</option>
					<option value="3" <?php if ($row['rating'] == 3) echo "selected"; ?>>&#9733;&#9733;&#9733; - Good</option>
					<option value="2" <?php if ($row['rating'] == 2) echo "selected"; ?>>&#9733;&#9733; - Fair</option>
					<option value="1" <?php if ($row['rating'] == 1) echo "selected"; ?>>&#9733; - Poor</option>
				</select>			
			</td>
          </tr>
          <tr>
            <td width="30%" valign="middle" align="right" class="tb1">Review Title:</td>
            <td width="70%" valign="top"><input type="text" name="review_title" id="review_title" value="<?php echo $row['review_title']; ?>" size="73" class="textbox" /></td>
          </tr>
            <tr>
				<td valign="middle" align="right" class="tb1">Review:</td>
				<td valign="top"><textarea name="review" cols="70" rows="10" class="textbox2"><?php echo strip_tags($row['review']); ?></textarea></td>
            </tr>
            <tr>
            <td valign="middle" align="right" class="tb1">Status:</td>
            <td valign="top">
				<select name="status">
					<option value="active" <?php if ($row['status'] == "active") echo "selected"; ?>>active</option>
					<option value="inactive" <?php if ($row['status'] == "inactive") echo "selected"; ?>>inactive</option>
					<option value="pending" <?php if ($row['status'] == "pending") echo "selected"; ?>>pending</option>
				</select>
			</td>
            </tr>
            <tr>
              <td align="center" colspan="2" valign="bottom">
				<input type="hidden" name="reviewid" id="reviewid" value="<?php echo (int)$row['review_id']; ?>" />
				<input type="hidden" name="action" id="action" value="edit">
				<input type="submit" class="submit" name="update" id="update" value="Update Review" />
				<input type="button" class="cancel" name="cancel" value="Cancel" onClick="javascript:document.location.href='reviews.php?page=<?php echo $pn; ?>&column=<?php echo $_GET['column']; ?>&order=<?php echo $_GET['order']; ?>'" />
              </td>
            </tr>
          </table>
      </form>

      <?php }else{ ?>
			<p align="center">Sorry, no review found.<br/><br/><a class="goback" href="#" onclick="history.go(-1);return false;">Go Back</a></p>
      <?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>