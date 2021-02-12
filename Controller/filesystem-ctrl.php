<?php

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
        CORE::VIEW("index", "FS - " . $path, array("contents" => $contents, "curent" => $path));
    }
    public function download($args)
    {
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
        else ERROR("Not Found", 404, "Could not find file '$file' to download");
    }
    public function createfile($args)
    {
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
            }
        }

        // Go back
        header("Location: /". $_ENV["BASENAME"] ."/filesystem/browse/" . implode("\\", $args));
    }
    public function createdir($args)
    {
        // Get the file path
        $filepath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        // Check if there is not already directory with that name
        if(!is_dir($filepath) && $filepath != null) {
            // Create the directory
            mkdir($filepath);
        }

        // Go back
        header("Location: /". $_ENV["BASENAME"] ."/filesystem/browse/" . implode("\\", $args));
    }
    public function rename($args)
    {
        // Get new and old name
        $newName = array_pop($args);
        $oldName = array_pop($args);

        // Get the path of the file/folder we want to rename
        $path = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));

        // Check if we aren't trying to rename folders that we need
        if(($oldName == "FILES" || $oldName == "SYSTEM") || ($newName == "FILES" || $newName == "SYSTEM")) CORE::ERROR("Could not rename file", 409, "Rule 'Dont rename to or form SYSTEM or FILES' was triggerd");
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
        }
        // Go back
        header("Location: /". $_ENV["BASENAME"] ."/filesystem/browse/" . implode("\\", $args));
    }
    public function delete($args)
    {
        // Get the file we want to delete
        $target = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));
        //$target = "..\_DIR" . $_POST["path"];
        array_pop($args); // Remove this file from the header path
    
        // When the target is a directory we check if we can remove it
        if(is_dir($target) && $_POST["path"] != "FILES" && $_POST["path"] != "SYSTEM"){
            rmdir($target);
        }
        // If the target is a file we unlike it
        else if(is_file($target)) {
            unlink($target);
        }
        // Go back
        header("Location: /". $_ENV["BASENAME"] ."/filesystem/browse/" . implode("\\", $args));
    }
    public function showfile($args)
    {
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
    public function upload($args)
    {
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
        $filePath = CONFIG["filesystem"] . str_replace('%20', ' ', implode('\\', $args));
        
        if (!file_exists($filePath)) { 
            if (!mkdir($filePath, 0777, true)) {
                verbose(0, "Failed to create $filePath");
            }
        }
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
        $filePath = $filePath . "\\" . $fileName;
            
        // (D) DEAL WITH CHUNKS
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
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
            rename("{$filePath}.part", $filePath);
        }
        verbose(1, "Upload OK");
    }
}