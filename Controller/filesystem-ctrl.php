<?php
// Load the models we need
CORE::LoadModel("filesystem");
CORE::LoadModel("users");

class filesystem
{
    public function browse($args)
    {
        // Get the curent path
        $path = implode("\\", $args);

        // Get files/folders in the curent directory and remove the . and .. because we don't need them
        $files = array_diff($path == "" ? scandir(CONFIG["filesystem"]) : scandir(CONFIG["filesystem"] . $path), array('.', '..'));

        $contents = array();
        
        // Get an array of info for all files
        foreach($files as $file) array_push($contents,  array(
            "name" => $file,
            "ext" => pathinfo(CONFIG["filesystem"] . $path . "\\$file", PATHINFO_EXTENSION)
        ));

        // View the filesystem/index.php file
        CORE::View("index", "FS - " . $path, array("contents" => $contents, "curent" => $path, "isAdmin" => User::HasPerms(ADMIN_PERM)));
    }
    public function download($args)
    {
        // Check if we have read permission
        if(!User::HasPerms(READ_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get the requested file
        $file = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        // Check if the file exists
        if (file_exists($file)) 
        {
            // Load the file to page
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
        else CORE::Error("Not Found", 404, "Could not find file '$file' to download");
    }
    /**
     * @sanitize GET
     */
    public function createfile($args)
    {
        // Check if we have create permission
        if(!User::HasPerms(CREATE_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get the file name
        $filename = array_pop($args);
        // Get the path of the file
        $filepath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args) . "\\$filename");
        
        // Check if it doesn't already exists
        if(!is_file($filepath)){
            // Check if there is an extension
            $explode = explode('.', $filename);
            if($explode[0] != "null"){
                // If no extension was found we use .txt
                if($explode[1] == null) $filepath .= '.txt';
                
                // Get file contents
                $content = isset($_GET["content"]) ? $_GET["content"] : '';

                // Create the file
                file_put_contents($filepath, $content);

                // Log this
                Logger::LogSuccess("User '" . $_SESSION['name'] . "' Created file '" . str_replace('%20', ' ', implode('\\', $args) . "\\$filename") . "'", "system");
            }
        }

        // Go back to the correct page
        $location = $_GET["loc"];
        var_dump($location);
        if($location == '') header("Location: /". ROOT_DIR ."/filesystem/browse/" . implode("\\", $args));
        else header("Location: /". ROOT_DIR . $location . implode("\\", $args) . "\\$filename");
    }
    /**
     * @sanitize GET
     */
    public function createdir($args)
    {
        // Check if we have create permission
        if(!User::HasPerms(CREATE_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get the file path
        $filepath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        // Check if there is not already directory with that name
        if(!is_dir($filepath) && $filepath != null) {
            // Create the directory
            mkdir($filepath);

            Logger::LogSuccess("User '" . $_SESSION['name'] . "' Created folder '" . str_replace('%20', ' ', implode('\\', $args)) . "'", "system");
        }

        // Go back
        header("Location: /". ROOT_DIR ."/filesystem/browse/" . implode("\\", $args));
    }
    public function rename($args)
    {
        // Check if we have update permission
        if(!User::HasPerms(UPDATE_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get new and old name
        $newName = array_pop($args);
        $oldName = array_pop($args);

        // Get the path of the file/folder we want to rename
        $path = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        // Check if we aren't trying to rename a static folder
        if(filesystem::IsStatic($oldName) || filesystem::IsStatic($newName)) CORE::Error("Could not rename file", 409, "Could not rename static folder/file");
        else
        {
            // Get the extension of the old file/folder
            //$newName .= '.' . explode('.', $oldName)[1];
            $oldExten = pathinfo("$path\\$oldName", PATHINFO_EXTENSION);
            if($oldExten != null) $newName .= '.' . $oldExten;

            // Than rename the file/folder
            rename(
                "$path\\$oldName",
                "$path\\$newName"
            );

            Logger::LogInfo("User '" . $_SESSION['name'] . "' renamed file/folder '$oldName' to '$newName'", "system");
        }
        // Go back
        header("Location: /". ROOT_DIR ."/filesystem/browse/" . implode("\\", $args));
    }
    public function delete($args)
    {
        // Check if we have delete permission
        if(!User::HasPerms(DELETE_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get the file we want to delete
        $target = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        array_pop($args); // Remove this file from the header path
    
        // Make sure we can't delete a static folder/file
        if(filesystem::IsStatic($_GET["path"]))
        {
            CORE::Error("Could not delete file/folder", 409, "Could not delete static folder/file");
            return;
        }

        // When the target is a directory we check if we can remove it
        if(is_dir($target)){
            rmdir($target);
        }
        // If the target is a file we unlike it
        else if(is_file($target)) {
            unlink($target);
        }

        Logger::LogWarning("User '" . $_SESSION['name'] . "' Deleted file/folder '$target'", "system");

        // Go back
        header("Location: /". ROOT_DIR ."/filesystem/browse/" . implode("\\", $args));
    }
    public function showfile($args)
    {
        // Check if we have read permission
        if(!User::HasPerms(READ_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get the path of the file to show
        $filepath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        // If the file exist we show the file
        if(file_exists($filepath)){
            header('Content-Type:'.mime_content_type($filepath));
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit();
        }
    }
    /**
     * @method POST
     * @sanitize POST
     */
    public function upload($args)
    {
        // Check if we have create permission
        if(!User::HasPerms(CREATE_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // (A) FUNCTION TO FORMULATE SERVER RESPONSE
        function verbose($ok=1,$info=""){
            // THROW A 400 ERROR ON FAILURE
            if ($ok==0) { http_response_code(400); }
                die(json_encode(["ok"=>$ok, "info"=>$info]));
        }
        // (B) INVALID UPLOAD
        if (empty($_FILES) || $_FILES['file']['error']) {
                verbose(0, "Failed to move uploaded file.");
        }
        
        // (C) UPLOAD DESTINATION
        // ! CHANGE FOLDER IF REQUIRED !
        $filepath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));
        
        if (!file_exists($filepath)) { 
            if (!mkdir($filepath, 0777, true)) {
                verbose(0, "Failed to create $filepath");
            }
        }
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
        $filepath = $filepath . "\\" . $fileName;
            
        // (D) DEAL WITH CHUNKS
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $out = @fopen("{$filepath}.part", $chunk == 0 ? "wb" : "ab");
        if ($out) {
            $in = @fopen($_FILES['file']['tmp_name'], "rb");
            if ($in) {
                while ($buff = fread($in, 4096)) { fwrite($out, $buff); }
            } else {
                verbose(0, "Failed to open input stream");
            }
            @fclose($in);
            @fclose($out);
            @unlink($_FILES['file']['tmp_name']);
        } else {
            verbose(0, "Failed to open output stream");
        }
        
        // (E) CHECK IF FILE HAS BEEN UPLOADED
        if (!$chunks || $chunk == $chunks - 1) {
            rename("{$filepath}.part", $filepath);
        }

        verbose(1, "Upload OK");
    }


    // [Text Editor]
    public function edit($args)
    {
        var_dump(User::HasPerms(UPDATE_PERM));
        // Check if we have update permission
        if(!User::HasPerms(READ_PERM + UPDATE_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get the file path
        $filepath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));
        
        // Check if the file exists
        if(!file_exists($filepath))
        {
            CORE::Error("Not found", 404, "Could not find file: " . $filepath);
            return;
        }

        $type = mime_content_type($filepath);
        if($type == 'inode/x-empty') $type = pathinfo($filepath, PATHINFO_EXTENSION);

        CORE::View("textEditor", "Text Editor", array("curent" => str_replace('%20', ' ', implode('\\', array_slice($args, 0, -1, true))), "contents" => htmlspecialchars(file_get_contents($filepath)), "type" => $type, "name" => array_pop($args)));
    }
    public function save($args)
    {
        // Check if we have update permission
        if(!User::HasPerms(UPDATE_PERM))
        {
            CORE::Error("Permission Denied", 403, "Invalid permissions");
            return;
        }

        // Get the file path
        $filepath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        // Get the new file contents
        $newContents = $_POST["newContents"];

        // Put the new contents in the file
        file_put_contents($filepath, $newContents);

        // Go back
        header("Location: /". ROOT_DIR . $_GET["loc"] . implode("\\", $args));
    }

    // Misc
    public static function IsStatic($path)
    {
        foreach(CONFIG["static_paths"] as $static)
        {
            if($path == $static) return true;
            else if($path == "/$static") return true;
        }
        return false;
    }
}