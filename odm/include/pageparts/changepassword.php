<div class="dev_overlay dev_mainoverlay">
	<div class="w-form">
	  <form name="changepassword" data-name="changepassword">
		<h3><?php echo _("changepassword_changepassword"); ?></h3>
		<input class="w-input" type="password" placeholder="<?php echo _("oldpassword");?>" name="oldpassword" data-name="oldpassword" required="required"></input>
		<input class="w-input" type="password" placeholder="<?php echo _("password");?>" name="password" data-name="password" required="required"></input>
		<input class="w-input" type="password" placeholder="<?php echo _("password2");?>" name="password2" required="required" data-name="password2"></input>
		<input class="w-button button" type="submit" value="<?php echo _("changepassword_changepassword"); ?>" data-wait="<?php echo _("please_wait");?>"></input>
		<div class="note share"><?php echo _("changepassword_update_notice"); ?></div>
	  </form>
	  <div class="w-form-done">
		<p><?php echo _("changepassword_success"); ?></p>
	  </div>
	  <div class="w-form-fail">
		<p><?php echo _("changepassword_failure"); ?></p>
	  </div>
	</div>
</div>