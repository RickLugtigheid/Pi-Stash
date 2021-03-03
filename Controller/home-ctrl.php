<?php
// Load the needed model
CORE::LoadModel("users");

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
        CORE::VIEW("index", "Home", array("isAdmin" => User::HasPerms(ADMIN_PERM), "icons" => $icons, "headers" => array('<link rel="stylesheet" href="/' . $_ENV["BASENAME"] . '/public/assets/css/desktop.css">')));
    }

    public function login($args)
    {

        // Get the user by id
        $userID = htmlspecialchars($_POST["id"]);

        // Verify the password
        if(User::Login($userID, $_POST["password"]))
        {
            session_start();
            $_SESSION["userID"] = $userID;
        }
        else 
        {
            echo "<script> alert('Invalid password');</script>"; // Think about something different
        }

        // GO to the page we wanted to go to
        header("Location: /". $_ENV["BASENAME"] . "/" . $_GET["path"]);
    }
    public function logout()
    {
        session_start();

        // remove all session variables
        session_unset();
        // destroy the session
        session_destroy();
    
        // login
        header("Location: /". $_ENV["BASENAME"]);
    }
}