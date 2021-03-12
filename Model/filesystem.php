<?php
class FS
{
    public static function DeleteFullDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) 
            {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."\\".$object) == "dir") 
                        FS::DeleteFullDir($dir."\\".$object); 
                    else unlink($dir."\\".$object);
                }
            }
            //reset($objects);
            rmdir($dir);
        }
    }
    public static function Zip($folder_to_zip, $zip_path)
    {
        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zip_path . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder_to_zip),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folder_to_zip));

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
            else if($file->isDir()) {
                $folder = substr($file->getRealPath(), strlen($folder_to_zip));
                $zip->addEmptyDir($folder);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }
    /**
     * Unzip a zip file to a folder
     */
    public static function UnZip($zip_path, $to)
    {
        $zip = new ZipArchive; 
  
        // Zip File Name 
        if ($zip->open($zip_path) === TRUE) { 
          
            // Unzip Path 
            $zip->extractTo($to); 
            $zip->close(); 
            return true;
        }
        return false;
    }
}