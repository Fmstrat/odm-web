<div class="dev_overlay dev_mainoverlay">
	<div class="w-form">
	  <form name="login" data-name="login" data-redirect="odm.php?p=main">
		<h3><?php echo _("login_login"); ?></h3>
		<?php if ($ALLOW_REGISTRATIONS) { ?>
			<div class="note"><?php echo _("login_no_account"); ?> <a href="odm.php?p=register"><?php echo _("index_register_now");?></a></div>
		<?php } ?>
		<input class="w-input" type="text" placeholder="<?php echo _("username");?>" name="username" data-name="username" required="required" autofocus="autofocus"></input>
		<input class="w-input" type="password" placeholder="<?php echo _("password");?>" name="password" data-name="password" required="required"></input>
		<input class="w-button button" type="submit" value="<?php echo _("btn_login"); ?>" data-wait="<?php echo _("please_wait");?>"></input>
	  </form>
	  <div class="w-form-done">
		<p><?php echo _("login_success");?></p>
	  </div>
	  <div class="w-form-fail">
		<p><?php echo _("login_failure");?></p>
	  </div>
	</div>
</div>