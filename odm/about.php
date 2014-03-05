<?php
include("include/core.php");
?>
<!DOCTYPE html>
<!-- This site was created in Webflow. http://www.webflow.com-->
<!-- Last Published: Mon Feb 24 2014 00:22:49 GMT+0000 (UTC) -->
<html data-wf-site="530a6c9a7b5bc4ca190008af">
<head>
  <meta charset="utf-8">
  <title><?php echo _("title");?></title>
  <meta name="description" content="<?php echo _("index_meta_description");?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="css/normalize.css">
  <link rel="stylesheet" type="text/css" href="css/webflow.css">
  <link rel="stylesheet" type="text/css" href="css/odm.webflow.css">
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
  <link rel="shortcut icon" type="image/x-icon" href="https://y7v4p6k4.ssl.hwcdn.net/placeholder/favicon.ico">
  <!--[if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js"></script><![endif]-->
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
  <div class="testimonials">
    <div class="w-container">
      <div class="quote"><?php echo _("based_on_opensource"); ?></div>
    </div>
  </div>
  <div class="w-container content">
    <h1 class="blue"><?php echo _("about_about"); ?></h1>
    <h2><?php echo _("about_contact"); ?></h2>
    <p>Sprinternet.at
      <br>
      <br>
      <br>Email: info@sprinternet.at
      <br>
      <br>Website: www.sprinternet.at</p>
    <h2><?php echo _("about_software"); ?></h2>
    <p><?php echo _("about_software_description"); ?></p>
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