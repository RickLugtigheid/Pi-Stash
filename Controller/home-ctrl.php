<?php
class home
{
    public function index()
    {
        // List of icons to show
        $icons = [];

        // Load all apps
        $apps = scandir(CONFIG["filesystem"] . "SYSTEM\\APPS");
        foreach(array_slice($apps, 2, count($apps)-1, true) as $appName)
        {
            $appDir = CONFIG["filesystem"] . "SYSTEM\\APPS\\$appName";
            if(!is_dir($appDir)) continue; // Get next
            
            // Check if we have an app.ini
            if(!file_exists("$appDir\\app.ini")) continue; // Get next
            
            // Load the app.ini
            $iconInfo = parse_ini_file("$appDir\\app.ini");

            // Add the icon
            if($iconInfo["icon_type"] != "hidden") $icons[] = $iconInfo;
        }

        // View the index page
        CORE::VIEW("index", "Home", array("icons" => $icons));
    }
}