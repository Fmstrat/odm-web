<div id="new-device" class="dev_overlay dev_newdevice">
	<h2><?php echo _("newdev_add_new");?></h2>
	<h4><?php echo _("newdev_scan_qr");?></h4>
	<img src="ajax/connector.php?cmd=qrcode" alt="QR Code for SDM App">
	<h4><?php echo _("newdev_download_app");?></h4><a class="dev_link" target="_blank" href="<?php echo $GOOGLE_PLAY_STORE;?>"><?php echo $GOOGLE_PLAY_STORE;?></a>
	<div class="button" onclick="toggleNewDevice()"><?php echo _("newdev_done");?></div>
</div>