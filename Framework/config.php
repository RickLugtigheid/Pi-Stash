<?php
/* Configuration for Pi-Stash */

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
$config["guest_account"] = true; // A account anyone can use
$config["max_log_size"] = 32; // Max logfile size in kb

// ----------------------------------
// SQL DATABASE
// ----------------------------------
// Configuration to login to the database
$config["default_database"] = "pi_stash";
$config["database_host"] = "127.0.0.1";
$config["database_user"] = "root";
$config["database_password"] = "mysql";


define("CONFIG", $config);