<?php
/**
 * this file needs to be included in each page!
 * 
 * it will provide db and other stuff =)
 */
session_start();

include(__DIR__ .'/config.php');
include(__DIR__ .'/version.php');
include(__DIR__ .'/password_compat.php');
include(__DIR__ .'/language.php');
include(__DIR__ .'/gcm.php');
include(__DIR__ .'/mcrypt.php');
include(__DIR__ .'/phpqrcode/qrlib.php');
include(__DIR__ .'/db/db.php');
include(__DIR__ .'/db/device_manager.class.php');
?>