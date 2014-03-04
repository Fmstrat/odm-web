<?php
if (!$ALLOW_REGISTRATIONS) {
?>
	<div class="dev_overlay dev_mainoverlay">
		<h3><?php echo _("form_no_registration");?></h3>
	</div>
<?php
} else {
?>
	<div class="dev_overlay dev_mainoverlay">
		<div class="w-form">
          <form name="register" data-name="register" data-redirect="odm.php?p=main">
            <h3><?php echo _("index_register_now");?></h3>
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
	</div>
<?php 
} 
?>