<?php
class SQL{
    private static function CreateConnection($server_conn = false){
        try{
            // Create a PDO connection to the database
            $conn = null;
            if($server_conn) $conn = new PDO("mysql:host=". CONFIG["database_host"], CONFIG["database_user"], CONFIG["database_password"]);
            else $conn = new PDO("mysql:host=". CONFIG["database_host"] .";dbname=". CONFIG["default_database"], CONFIG["database_user"], CONFIG["database_password"]);
            
            //the first Attribute will report it to the webpage if something goes wrong
            //the second trows the error to the catch 
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);           
            return $conn;
        }catch(PDOException $e){
            //CORE::ERROR("Database Error", 500, $e);
            return array('error' => $e);
        }
    }
    /**
     * @param string $query
     * @return PDOStatement Query Result 
     */
    public static function Execute($query, $on_database=true){
        $conn = SQL::CreateConnection(!$on_database);
        if(is_array($conn)) return $conn;
        return $conn->query($query);
    }
    /**
     * @param string $query
     * @param array $prepare_args
     * @return PDOStatement Query Result 
     */
    public static function ExecutePrepare($query, $prepare_args){
        $conn = SQL::CreateConnection();
        $stmt = $conn->prepare($query);
        
        //bind params
        foreach($prepare_args as $key => &$value) 
        {
            echo "bindParam($key, $value)<br>";
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
    }
    /**
     * @param string $path
     * @return PDOStatement Query Result 
     */
    public static function ExecuteFile($path){
        $conn = SQL::CreateConnection();

        $query = file_get_contents($path);
        
        return $conn->exec($query);
    }
    /**
     * @param string $path
     * @param array $prepare_args
     * @return PDOStatement Query Result 
     */
    public static function ExecutePrepareFile($path, $prepare_args){
        return SQL::ExecutePrepare(file_get_contents($path), $prepare_args);
    }
}

function GetDockValues($doc_string)
{
    $results = array();
    // Get key value pairs from dockblock
    // Example:
    //  key => "@type", value => "string"
    $pattern = "/@(.*?)\s([a-zA-Z0-9]*)/m";
    preg_match_all($pattern, $doc_string, $matches, PREG_PATTERN_ORDER);
    
    // Matches[0] = full values
    // Matches[1] = group1 aka key
    // Matches[2] = group2 aka value
    foreach($matches[1] as $key)
    foreach($matches[2] as $value)
        $results[$key] = $value;

    //Return results
    return $results;
}