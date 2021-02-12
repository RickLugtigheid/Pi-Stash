<?php
$_ENV["BASENAME"] = basename(dirname(__DIR__));

// Get the request URL
CORE::$request_url = isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : null;
if(isset(CORE::$request_url)) array_shift(CORE::$request_url);

// Get the controller name
$reqController = CORE::$request_url[0];

// Check if we need to load the default controller, app or controller(by name)
if($reqController == null) 
{
    CORE::$request_url[0] = CONFIG["default_controller"];
    CORE::$request_url[1] = "index";

    CORE::LoadController(ROOT . "/Controller/" . CONFIG["default_controller"] . "-ctrl.php");
}
else if (!file_exists(ROOT . "/Controller/$reqController-ctrl.php")) CORE::LoadController(CONFIG["filesystem"] . "SYSTEM\\APPS\\$reqController\\router.php");
else CORE::LoadController(ROOT . "/Controller/$reqController-ctrl.php");

class CORE
{
    /**
     * The request url
     * @var array
     */
    public static $request_url;

    /**
     * Loads a controller by path
     */
    static function LoadController($file)
    {
        // Check if controller erxists
        if(!file_exists($file)) CORE::ERROR("Not found", 404, "Could not find controller: $file");
        else
        {
            // Get the controller
            require_once $file;
            $_ENV["CURRENT"] = CORE::$request_url[0];

            if(!class_exists(CORE::$request_url[0]))
            {
                CORE::ERROR("Not found", 404, "Could not find controller class: " . CORE::$request_url[0]);
                return;
            }
            
            // Create an instance of the controller class
            $controller_instance = new CORE::$request_url[0]();

            // Get contoller method
            $controller_method = CORE::$request_url[1];

            if(!method_exists($controller_instance, $controller_method))
            {
                CORE::ERROR("Not found", 404, "Could not find controller method: " . $controller_method);
                return;
            }
            
            // Check if we have a controller method
            if($controller_method == '')  $controller_method = "index";

            // Now we call the controller method and give an array of args
            $controller_instance->$controller_method(array_slice(CORE::$request_url, 2, count(CORE::$request_url)-1, true));
        }
    }

    /**
     * Loads a html page
     * @param string $file Name of the page to view
     * @param string $title Page title
     * @param array $args Arguments to give to the page
     */
    public static function VIEW($file, $title, $args = null)
    {
        // Extract the variables to a local namespace
        if(!empty($args)) extract($args);

        // Set title
        ob_start();
        include(ROOT . "/View/header.php");
        $buffer=ob_get_contents();
        ob_end_clean();

        $buffer=str_replace("%TITLE%", $title, $buffer);
        echo $buffer;

        // Include the files
        include(ROOT . "/View/".$_ENV["CURRENT"]."/$file.php");
        include(ROOT . "/View/footer.php");
    }
    /**
     * Loads a html page
     * @param string $file Name of the page to view
     * @param string $title Page title
     * @param array $args Arguments to give to the page
     */
    public static function APP_VIEW($file, $title, $args = null)
    {
        // Extract the variables to a local namespace
        if(!empty($args)) extract($args);

        // Set title
        ob_start();
        include(ROOT . "/View/header.php");
        $buffer=ob_get_contents();
        ob_end_clean();

        $buffer=str_replace("%TITLE%", $title, $buffer);
        echo $buffer;

        // Include the files
        include(CONFIG["filesystem"] . "SYSTEM/APPS/" . $_ENV["CURRENT"] . "/View/$file.php");
        include(ROOT . "/View/footer.php");
    }

    /**
     * Loads an error page
     * @param string $type Type of error. Like 'Not found'
     * @param int $code Error code. Like 404
     * @param string $message Larger error message
     */
    public static function ERROR($type, $code, $message)
    {
        $_ENV["CURRENT"] = "";
        CORE::VIEW("error", $type, array(
            "type" => $type,
            "code" => $code,
            "message" => $message
        ));
    }
}