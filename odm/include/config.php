<?php
/**
 * Database config variables
 */
global $DB_HOST; $DB_HOST = "localhost";
global $DB_USER; $DB_USER = "username";
global $DB_PASSWORD; $DB_PASSWORD = "SecretPW";
global $DB_DATABASE; $DB_DATABASE = "mydatabase";

/*
 * Allow new user registration?
 */
global $ALLOW_REGISTRATIONS; $ALLOW_REGISTRATIONS = true;

/*
 * Absolute directory on the server without trailing slash
 */
global $APP_BASE_PATH; $APP_BASE_PATH = "/var/www/vhosts/example.com/httpdocs/odm";

/*
 * Web subdirectory without trailing slash
 */
global $WEB_BASE_PATH; $WEB_BASE_PATH = "/odm";

/*
 * Google API Key
 * (No need to modify this)
 */
global $GOOGLE_API_KEY; $GOOGLE_API_KEY = "AIzaSyA-3S_tjBnTDuplbqFPulEtVtoaPJGJmVc";

/*
 * Path to PlayStore App
 */
global $GOOGLE_PLAY_STORE; $GOOGLE_PLAY_STORE = "https://github.com/Fmstrat/odm";
?>