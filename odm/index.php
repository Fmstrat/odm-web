<?php
include("include/core.php");
?>
<!DOCTYPE html>
<html data-wf-site="sdm_index">
<head>
  <meta charset="utf-8">
  <title><?php echo _("title");?></title>
  <meta name="description" content="<?php echo _("index_meta_description");?>">
  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="css/normalize.css">
  <link rel="stylesheet" type="text/css" href="css/webflow.css">
  <link rel="stylesheet" type="text/css" href="css/sprinternet-device-manager.webflow.css">
  <script src="js/webfont.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Bitter:400,700","Montserrat:400,700"]
      }
    });
  </script>
  <script>
    if (/mobile/i.test(navigator.userAgent)) document.documentElement.className += ' w-mobile';
  </script>
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
  <!--[if lt IE 9]><script src="js/html5shiv.min.js"></script><![endif]-->
</head>
<body>
  <div class="w-container header">
    <div class="w-nav sdm_navbar" data-collapse="medium" data-animation="default" data-duration="400" data-contain="1">
      <div class="w-container">
        <a class="w-nav-brand sdm_brand" href="#"></a>
        <nav class="w-nav-menu" role="navigation">
			<a class="w-nav-link sdm_navlink" href="index.php"><?php echo _("btn_home"); ?></a>
			<a class="w-nav-link sdm_navlink" href="about.php"><?php echo _("btn_about"); ?></a>
			<?php if (isset($_SESSION['user_id'])) { ?>
				<a class="w-nav-link sdm_navlink" href="odm.php?p=main"><?php echo _("btn_devman"); ?></a>
				<a class="w-nav-link sdm_navlink" href="odm.php?p=changepassword"><?php echo _("btn_changepassword"); ?></a>
				<a class="w-nav-link sdm_navlink" href="ajax/connector.php?cmd=logout"><?php echo _("btn_logout"); ?></a>
			<?php } else { ?>	
				<a class="w-nav-link sdm_navlink" href="odm.php?p=main"><?php echo _("btn_login"); ?></a>
			<?php } ?>	
        </nav>
        <div class="w-nav-button">
          <div class="w-icon-nav-menu"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="jumbo">
    <div class="w-container">
      <div class="w-row">
        <div class="w-col w-col-6"></div>
        <div class="w-col w-col-6">
          <h1><br><?php echo _("index_headline"); ?></h1>
          <div class="subtitle"><?php echo _("index_description");?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="testimonials">
    <div class="w-container">
      <div class="quote"><?php echo _("based_on_opensource"); ?></div>
    </div>
  </div>
  <div class="w-container content">
    <div class="w-row">
      <div class="w-col w-col-3">
        <h2><?php echo _("index_app"); ?></h2>
        <a target="_blank" href="<?php echo $GOOGLE_PLAY_STORE;?>"><img class="sdm_appstore_icon" src="images/google_play_icon.png" alt="SDM in google play"></a>
		<img class="sdm_appstore_icon" src="ajax/connector.php?cmd=qrcode" style="margin-left:-13px;" alt="QR Code for SDM App">
      </div>
      <div class="w-col w-col-3"></div>
      <div class="w-col w-col-6">
		<?php if (!isset($_COOKIE['user_id'])) { ?>
        <div class="w-form wrapper">
          <form name="register" data-name="register">
            <h3 class="call-to-action"><?php echo _("index_register_now");?></h3>
            <div class="note"><?php echo _("index_free_registration");?></div>
            <input class="w-input" type="text" placeholder="<?php echo _("username");?>" name="username" data-name="username" required="required" autofocus="autofocus"></input>
            <input class="w-input" type="email" placeholder="<?php echo _("email");?>" name="email" data-name="email" required="required"></input>
            <input class="w-input" type="password" placeholder="<?php echo _("password");?>" name="password" data-name="password" required="required"></input>
            <input class="w-input" type="password" placeholder="<?php echo _("password2");?>" name="password2" required="required" data-name="password2"></input>
            <input class="w-button button" type="submit" value="<?php echo _("register");?>" data-wait="<?php echo _("please_wait");?>"></input>
            <div class="note share"><?php echo _("index_privacy_note");?></div>
          </form>
          <div class="w-form-done">
            <p><?php echo _("index_reg_success");?></p>
          </div>
          <div class="w-form-fail">
            <p><?php echo _("index_reg_failure");?></p>
          </div>
        </div>
		<?php } ?>
      </div>
    </div>
  </div>
  <div class="footer">
    <div class="w-container">
      <div class="w-row">
        <div class="w-col w-col-6">
          <div class="copyright">© 2014 Sprinternet.at</div>
        </div>
        <div class="w-col w-col-6">
          <div class="copyright info"></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/webflow.js"></script>
</body>
</html>