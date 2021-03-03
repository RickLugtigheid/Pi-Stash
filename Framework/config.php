<?php

/* Configuration for your site */

// ----------------------------------
// CORE 
// ----------------------------------
// Core configuration
$config["default_controller"] = "home";
$config["filesystem"] = "D:\\Pi-Stash\\";

// ----------------------------------
// RULES 
// ----------------------------------
$config["static_paths"] = ["SYSTEM", "SYSTEM/APPS", "FILES"];

// ----------------------------------
// SQL DATABASE
// ----------------------------------
// Configuration to login to the database
$config["default_database"] = "Pi-Stash";
$config["database_host"] = "127.0.0.1";
$config["database_user"] = "root";
$config["database_password"] = "mysql";


define("CONFIG", $config);