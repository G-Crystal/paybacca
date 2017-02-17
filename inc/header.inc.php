<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<title><?php echo $PAGE_TITLE." | ".SITE_TITLE; ?></title>

	<?php if ($PAGE_DESCRIPTION != "") { ?><meta name="description" content="<?php echo $PAGE_DESCRIPTION; ?>" /><?php } ?>

	<?php if ($PAGE_KEYWORDS != "") { ?><meta name="keywords" content="<?php echo $PAGE_KEYWORDS; ?>" /><?php } ?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<meta name="author" content="CashbackEngine.net" />

	<meta name="robots" content="index, follow" />

	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" />

	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Comfortaa" />

	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>css/style.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>css/header.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>css/main.css" />

	<script
		src="https://code.jquery.com/jquery-3.1.1.min.js"
		integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
		crossorigin="anonymous">
	</script>
  
	<!--<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jquery.tools.tabs.min.js"></script>-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

	<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>

		<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#appId=<?php echo FACEBOOK_APPID; ?>&amp;xfbml=1"></script>

	<?php } ?>

	<?php if (isset($ADDTHIS_SHARE) && $ADDTHIS_SHARE == 1) { ?>

		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=<?php echo ADDTHIS_ID; ?>"></script>

	<?php } ?>

	<script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>

	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/autocomplete.js"></script>

	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/jsCarousel.js"></script>

	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/clipboard.js"></script>

	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/cashbackengine.js"></script>

	<script type="text/javascript" src="<?php echo SITE_URL; ?>js/easySlider1.7.js"></script>

	<link rel="shortcut icon" href="<?php echo SITE_URL; ?>favicon.ico" />

	<link rel="icon" type="image/ico" href="<?php echo SITE_URL; ?>favicon.ico" />

	<meta property="og:title" content="<?php echo $PAGE_TITLE; ?>" />

	<meta property="og:url" content="<?php echo SITE_URL; ?>" />

	<meta property="og:description" content="<?php echo $PAGE_DESCRIPTION; ?>" />

	<meta property="og:image" content="<?php echo SITE_URL; ?>images/logo.png" />

	<?php echo GOOGLE_ANALYTICS; ?>

</head>

<body>

<div id="container" class="container">

	<div id="content">

	<nav class="navbar navbar-default trans-bgcolor">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo SITE_URL; ?>"><img src="<?php echo SITE_URL; ?>images/logo.png" alt="<?php echo SITE_TITLE; ?>" title="<?php echo SITE_TITLE; ?>" border="0" /></a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="<?php echo SITE_URL; ?>retailers.php">Stores</a></li>
					<li><a href="<?php echo SITE_URL; ?>coupons.php">Coupons</a></li>
				</ul>
				
				<form name="searchfrm" id="searchfrm" class="navbar-form navbar-left" action="<?php echo SITE_URL; ?>search.php" method="get" autocomplete="off">
					<div class="form-group">
						<input type="text" id="searchtext" name="searchtext" class="form-control" onKeyPress="ajaxsearch(this.value)" value="<?php echo @$stext; ?>" placeholder="<?php echo CBE_SEARCH_MSG; ?>">
						<input type="hidden" name="action" value="search" />
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
				</form>

				<ul class="nav navbar-nav navbar-right">
				<?php if (isLoggedIn()) { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Account <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo SITE_URL; ?>myaccount.php"><span class="member"><?php echo $_SESSION['FirstName']; ?></span></a></li>
							<li><a class="logout" href="<?php echo SITE_URL; ?>logout.php"><?php echo CBE_LOGOUT; ?></a></li>
						</ul>
					</li>
				<?php }else{ ?>
					<li><a class="menu" data-toggle="modal" href="#loginModal"><?php echo CBE_LOGIN; ?></a></li>
					<li><a class="menu" data-toggle="modal" href="#signupModal"><?php echo CBE_SIGNUP; ?></a></li>
				<?php } ?>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<div class="modal fade" id="loginModal" role="dialog">
		<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo CBE_LOGIN; ?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 error_msg" id="login_error_msg" style="display:none;">
					</div>
				</div>

				<div class="login_box">
				<form action="<?php echo SITE_URL; ?>login.php" method="post">
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><?php echo CBE1_LOGIN_EMAIL2; ?>:</div>
						<div class="col-xs-8"><input type="text" class="textbox form-full-width" id="username" name="username" value="<?php echo getPostParameter('username'); ?>" size="25" required="required" /></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><?php echo CBE1_LOGIN_PASSWORD; ?>:</div>
						<div class="col-xs-8"><input type="password" class="textbox form-full-width" id="password" name="password" value="" size="25" required="required" /></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-8 col-xs-offset-4 form-row-label">
							<input type="checkbox" class="checkboxx remember-id-checkbox" name="rememberme" id="rememberme" value="1" checked="checked" /> <?php echo CBE1_LOGIN_REMEMBER; ?>
						</div>
					</div>
					<div class="row form-row-control justify-content-center">
						<div class="col-xs-12 text-center">
							<input type="hidden" name="action" value="login" />
							<a id="login_btn" class="common-btn margin-top-10"><?php echo CBE1_LOGIN_BUTTON; ?></a>
							<a class="common-btn close-login" href="#signupModal" data-toggle="modal"><?php echo CBE_SIGNUP; ?></a>
						</div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-8 col-xs-offset-4 form-row-label">
							<a class="close-login" href="#forgotModal" data-toggle="modal"><?php echo CBE1_LOGIN_FORGOT; ?></a>
							<?php if (ACCOUNT_ACTIVATION == 1) { ?>
								<p><a href="<?php echo SITE_URL; ?>activation_email.php"><?php echo CBE1_LOGIN_AEMAIL; ?></a></p>
							<?php } ?>
						</div>
					</div>
					<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
					<div class="row form-row-control">
						<div style="border-bottom: 1px solid #ECF0F1; margin-bottom: 15px;">
							<div style="font-weight: bold; background: #FFF; color: #CECECE; margin: 0 auto; top: 5px; text-align: center; width: 50px; position: relative;">or</div>
						</div>
						<p align="center"><a href="javascript: void(0);" onClick="facebook_login();" class="connect-f"><img src="<?php echo SITE_URL; ?>images/facebook_connect.png" /></a></p>
					</div>
					<?php } ?>
				</form>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

		</div>
	</div>

	<div class="modal fade" id="signupModal" role="dialog">
		<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo CBE_SIGNUP; ?></h4>
			</div>
			<div class="modal-body">
				<div class="col-xs-12 error_msg" id="signup_error_msg" style="display:none;">
				</div>

				<?php if (FACEBOOK_CONNECT == 1 && FACEBOOK_APPID != "" && FACEBOOK_SECRET != "") { ?>
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript: void(0);" onClick="facebook_login();" class="connect-f"><img src="<?php echo SITE_URL; ?>images/facebook_connect.png" /></a>
					</div>
				</div>
				<?php } ?>

				<div class="login_box">
				<form action="<?php echo SITE_URL; ?>signup.php" method="post">
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><span class="req">* </span><?php echo CBE1_LABEL_FNAME; ?>:</div>
						<div class="col-xs-8"><input type="text" id="fname" class="textbox" name="fname" value="<?php echo getPostParameter('fname'); ?>" size="27" /></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><span class="req">* </span><?php echo CBE1_LABEL_LNAME; ?>:</div>
						<div class="col-xs-8"><input type="text" id="lname" class="textbox" name="lname" value="<?php echo getPostParameter('lname'); ?>" size="27" /></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><span class="req">* </span><?php echo CBE1_LABEL_EMAIL2; ?>:</div>
						<div class="col-xs-8"><input type="text" id="email" class="textbox" name="email" value="<?php echo getPostParameter('email'); ?>" size="27" /></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><span class="req">* </span><?php echo CBE1_LABEL_PWD; ?>:</div>
						<div class="col-xs-8"><input type="password" id="pwd" class="textbox" name="pwd" value="" size="27" /> <span class="note"><?php echo CBE1_SIGNUP_PTEXT; ?></span></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><span class="req">* </span><?php echo CBE1_LABEL_CPWD; ?>:</div>
						<div class="col-xs-8"><input type="password" id="pwd_cfm" class="textbox" name="pwd_cfm" value="" size="27" /></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-8 col-xs-offset-4 form-row-label">
						<?php if (isset($_COOKIE['referer_id']) && is_numeric($_COOKIE['referer_id'])) { ?>
							<input type="hidden" name="referer_id" id="referer_id" value="<?php echo (int)$_COOKIE['referer_id']; ?>" />
						<?php } ?>
						</div>
					</div>
					<div class="row form-row-control justify-content-center">
						<div class="col-xs-12 text-center">
							<?php if (isset($_COOKIE['referer_id']) && is_numeric($_COOKIE['referer_id'])) { ?>
								<input type="hidden" name="referer_id" id="referer_id" value="<?php echo (int)$_COOKIE['referer_id']; ?>" />
							<?php } ?>
							<input type="hidden" name="action" id="action" value="signup" />
							<a id="signup_btn" class="common-btn margin-top-10"><?php echo CBE1_SIGNUP_BUTTON; ?></a>
							<a class="common-btn close-signup" href="#loginModal" data-toggle="modal"><?php echo CBE1_LOGIN_BUTTON; ?></a>
						</div>
					</div>
				</form>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

		</div>
	</div>

	<div class="modal fade" id="forgotModal" role="dialog">
		<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo CBE1_FORGOT_TITLE; ?></h4>
			</div>
			<div class="modal-body">
				<div class="col-xs-12 error_msg" id="forgot_error_msg" style="display:none;"></div>
				<div class="col-xs-12 success_msg" id="forgot_success_msg" style="display:none;"></div>
				<div class="col-xs-12" id="forgot_normal_msg" style="display:none;"><?php echo CBE1_FORGOT_TEXT; ?></div>

				<div class="login_box">
				<form action="<?php echo SITE_URL; ?>forgot.php" method="post">
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><span class="req">* </span><?php echo CBE1_FORGOT_EMAIL; ?>:</div>
						<div class="col-xs-8"><input type="text" class="textbox" id="femail" name="femail" size="30" required="required" value="<?php echo getPostParameter('femail'); ?>" /></div>
					</div>
					<div class="row form-row-control">
						<div class="col-xs-4 form-row-label"><span class="req">* </span><?php echo CBE1_SIGNUP_SCODE; ?>:</div>
						<div class="col-xs-8">
							<input type="text" id="captcha" class="textbox" name="captcha" required="required" value="" size="8" />
							<img src="<?php echo SITE_URL; ?>captcha.php?rand=<?php echo rand(); ?>&bg=grey" id="captchaimg" align="absmiddle" /> <small><a href="javascript: refreshCaptcha();" title="<?php echo CBE1_SIGNUP_RIMG; ?>"><img src="<?php echo SITE_URL; ?>images/icon_refresh.png" align="absmiddle" alt="<?php echo CBE1_SIGNUP_RIMG; ?>" /></a></small>
						</div>
					</div>
					<div class="row form-row-control justify-content-center">
						<div class="col-xs-12 text-center">
							<input type="hidden" name="action" id="action" value="forgot" />
							<a id="forgot_btn" class="common-btn margin-top-10"><?php echo CBE1_FORGOT_BUTTON; ?></a>
							<a class="common-btn close-forgot" href="#loginModal" data-toggle="modal"><?php echo CBE1_LOGIN_BUTTON; ?></a>
						</div>
					</div>
				</form>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>

		</div>
	</div>


<script language="javascript">
$(document).ready(function(){
	refreshCaptcha = function () {
		var img = document.images['captchaimg'];
		img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000+"&bg=grey";
	}

	// Login ajax process module
	login_ajax = function () {
		item = {
			"action" : "login",
			"username" : $("#username").val(),
			"password" : $("#password").val(),
			"rememberme": $("#rememberme").val()
		}

		$.ajax({
			type : 'GET',
			url  : 'server/login.php',
			data : {params:JSON.stringify(item)}
		})
		.done(function(data) {
			var data = jQuery.parseJSON(data);
			if (typeof(data['error_msg']) != "undefined")
			{
				$("#login_error_msg").html(data['error_msg']);
				$("#login_error_msg").css('display', 'inline');
			}
			else if (typeof(data['url']) != "undefined")
			{
				window.location = data['url'];
			}
		});
	}

	$("#login_btn").click(function(){
		login_ajax();
	});

	$("#password").keyup(function(e){ 
		var code = e.which;
		if(code==13) e.preventDefault();
		if(code==32 || code==13 || code==188 || code==186) {
			login_ajax();
		}
	});
	
	// Signup ajax process module
	signup_ajax = function () {
		item = {
			"action" : "signup",
			"fname" : $("#fname").val(),
			"lname" : $("#lname").val(),
			"email" : $("#email").val(),
			"pwd" : $("#pwd").val(),
			"pwd_cfm" : $("#pwd_cfm").val(),
			"referer_id" : $("#referer_id").val()
		}

		$.ajax({
			type : 'GET',
			url  : 'server/signup.php',
			data : {params:JSON.stringify(item)}
		})
		.done(function(data) {
			var data = jQuery.parseJSON(data);
			if (typeof(data['error_msg']) != "undefined")
			{
				$("#signup_error_msg").html(data['error_msg']);
				$("#signup_error_msg").css('display', 'inline');
			}
			else if (typeof(data['url']) != "undefined")
			{
				window.location = data['url'];
			}
		});
	}

	$("#signup_btn").click(function(){
		signup_ajax();
	});

	$("#pwd_cfm").keyup(function(e){ 
		var code = e.which;
		if(code==13) e.preventDefault();
		if(code==32 || code==13 || code==188 || code==186) {
			signup_ajax();
		}
	});
	
	// Forgot ajax process module
	forgot_ajax = function () {
		item = {
			"action" : "signup",
			"femail" : $("#femail").val(),
			"captcha" : $("#captcha").val()
		}

		$.ajax({
			type : 'GET',
			url  : 'server/forgot.php',
			data : {params:JSON.stringify(item)}
		})
		.done(function(data) {
			var data = jQuery.parseJSON(data);
			if (typeof(data['error_msg']) != "undefined")
			{
				$("#forgot_error_msg").html(data['error_msg']);
				$("#forgot_error_msg").css('display', 'inline');
			}
			else if (typeof(data['success_msg']) != "undefined")
			{
				$("#forgot_success_msg").html(data['error_msg']);
				$("#forgot_success_msg").css('display', 'inline');
			}
			else if (typeof(data['normal_msg']) != "undefined")
			{
				$("#forgot_normal_msg").css('display', 'inline');
			}
			else if (typeof(data['url']) != "undefined")
			{
				window.location = data['url'];
			}
		});
	}

	$("#forgot_btn").click(function(){
		forgot_ajax();
	});

	$("#captcha").keyup(function(e){ 
		var code = e.which;
		if(code==13) e.preventDefault();
		if(code==32 || code==13 || code==188 || code==186) {
			forgot_ajax();
		}
	});

	$('#loginModal').on('hidden.bs.modal', function () {
		$("#login_error_msg").html("");
		$("#login_error_msg").css('display', 'none');
	});

	$('#signupModal').on('hidden.bs.modal', function () {
		$("#signup_error_msg").html("");
		$("#signup_error_msg").css('display', 'none');
	});

	$('#forgotModal').on('hidden.bs.modal', function () {
		$("#forgot_error_msg").html("");
		$("#forgot_error_msg").css('display', 'none');
		$("#forgot_success_msg").html("");
		$("#forgot_success_msg").css('display', 'none');
		$("#forgot_normal_msg").css('display', 'none');
	});
});
</script>

<div id="column_center">

