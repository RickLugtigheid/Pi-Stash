<?php
// Load the plugin file
require_once ROOT . "/Framework/plugins.php";

// Get the folder name
$_ENV["BASENAME"] = basename(dirname(__DIR__));

// Get the request URL
CORE::$request_url = isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : null;
if(isset(CORE::$request_url)) array_shift(CORE::$request_url);

// Check if we are loged in
session_start();
// Check if a user is logedin
if((CORE::$request_url[1] != "login") && !isset($_SESSION["userID"]))
{
    // If not we send them to the login page
    $_ENV["CURRENT"] = "home";
    CORE::VIEW("login", "Login", array("users" => SQL::Execute("SELECT * FROM USERS"), "path" => $_SERVER['PATH_INFO'],
    "headers" => array('<link rel="stylesheet" href="/' . $_ENV["BASENAME"] . '/public/assets/css/desktop.css">')));
    return;
}

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

            // Check if we have a class with the same name as the controller
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

            // Get the doc block of our function
            $method_doc = (new ReflectionClass($controller_instance))->getMethod($controller_method)->getDocComment();
            // Use the docblock to find out things abbout our function
            if($method_doc)
            {
                // Use regex to get dockblock values; Get all key/values so key='@type' value='string'
                $results = GetDockValues($method_doc);

                // Check all keys and values
                foreach($results as $key=>&$value)
                {
                    switch($key)
                    {
                        case "method":
                            // Check if the function is called with correct method
                            if($value !== $_SERVER['REQUEST_METHOD']) 
                            {
                                CORE::Error('Invalid method', 405, "Invalid request method. This function requires '$value' method");
                                exit();
                            }
                            break;
                        case "sanitize":
                            // Sanitize the values we want to sanitize
                            if(strpos($value, 'POST') !== false) foreach($_POST as $post) CORE::Sanitize($post);
                            else if(strpos($value, 'GET') !== false) foreach($_GET as $get) CORE::Sanitize($get);
                            break;
                    }
                }
            }

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
    public static function View($file, $title, $args = null)
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
    public static function AppView($file, $title, $args = null)
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
     * Loads an model in ROOT/Model
     * @param string $name The model to load
     */
    public static function LoadModel($name)
    {
        // Get the path
        $modelPath = ROOT . "/Model/$name.php";

        // Check if the model exists
        if(!file_exists($modelPath)) return false;

        // include the file
        require_once $modelPath;
        return true;
    }

    /**
     * Loads an error page
     * @param string $type Type of error. Like 'Not found'
     * @param int $code Error code. Like 404
     * @param string $message Larger error message
     */
    public static function Error($type, $code, $message)
    {
        $_ENV["CURRENT"] = "";
        CORE::VIEW("error", $type, array(
            "type" => $type,
            "code" => $code,
            "message" => $message
        ));
    }
    
    /**
     * Sanitize the input data
     * @param any $data Data to validate
     */
    public static function Sanitize($data) 
    {
        $data = trim($data);
        $data = htmlspecialchars($data);
        $data = stripcslashes($data);
        return $data;
    }
}

/**
 * @static
 */
class Logger
{
    private static $log_folder;

    public static function LogInfo($message, $logfile) { Logger::WriteLine($message, "Inf", $logfile); }
    public static function LogSuccess($message, $logfile) { Logger::WriteLine($message, "Scs", $logfile); }
    public static function LogWarning($message, $logfile) { Logger::WriteLine($message, "Wrn", $logfile); }
    public static function LogError($message, $logfile) { Logger::WriteLine($message, "Err", $logfile); }

    /**
     * Write a line to a log file
     */
    private static function WriteLine($message, $type, $logfile)
    {
        // Check if we have a log folder
        if(!isset(Logger::$log_folder)) 
        {
            Logger::$log_folder = CONFIG['filesystem'] . "SYSTEM\\LOGS\\";
            // Create the folder if it doesn't exist
            if(!is_dir(Logger::$log_folder)) mkdir(Logger::$log_folder);
        }
        // Get path to the log
        $path = Logger::$log_folder . $logfile . '.log';
        // If there is no file with this name we create one
        if(!is_file($path)) file_put_contents($path, "");
        // Create a stream to our file
        $stream = fopen($path, 'a');
        // Write to the stream
        fwrite($stream, date("Y-m-d H:i:s.ms") . "  $type   $message\n");
        // Close our file stream
        fclose($stream);
    }
}