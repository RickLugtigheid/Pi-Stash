<?php

/* Configuration for your site */

// ----------------------------------
// SQL DATABASE
// ----------------------------------
// Configuration to login to the database
$config["default_database"] = "";
$config["database_host"] = "127.0.0.1";
$config["database_user"] = "root";
$config["database_password"] = "mysql";

// ----------------------------------
// CORE 
// ----------------------------------
// Core configuration
$config["default_controller"] = "home";

// ----------------------------------
// FILESYSTEM 
// ----------------------------------
$config["filesystem"] = "D:\\Pi-Stash\\";

define("CONFIG", $config);