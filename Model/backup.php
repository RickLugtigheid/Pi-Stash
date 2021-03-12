<?php
class Backups
{
    static $fallback;
    /**
     * Use a fallback point while loading a backup
     * @param $backup_path Name of the backup to load
     * @param string $fallback_path Name of the backup to fallback to
     */
    public static function LoadWithFallback($backup_path, $fallback_path)
    {
        // Create fallback
        $fallback = './fallback.zip';
        // Create a copy of the fallback
        copy($fallback_path, $fallback);

        // Run method
        try
        {
            // If method returns false load fallback point
            if(!Backups::Load($backup_path)) Backups::Load($fallback, false);
        }
        catch (Exception $e)
        {
            // On error load the fallback point
            Backups::Load($fallback, false);
        }

        // Remove fallback point
        unlink($fallback);
    }
    /**
     * Get all backups in backup folder
     */
    public static function Get()
    {
        if(!is_dir(CONFIG['backup_folder'])) mkdir(CONFIG['backup_folder']);
        return array_diff(scandir(CONFIG['backup_folder']),  array('.', '..'));
    }
    /**
     * Get last created backup
     */
    public static function GetLast()
    {
        if(!is_dir(CONFIG['backup_folder'])) mkdir(CONFIG['backup_folder']);

        $latest_ctime = 0;
        $latest_filename = '';

        foreach(array_diff(scandir(CONFIG['backup_folder']), array('.', '..')) as $file)
        {
            if (is_file(CONFIG['backup_folder'] . $file) && filectime(CONFIG['backup_folder'] . $file) >= $latest_ctime)
            {
                $latest_ctime = filectime(CONFIG['backup_folder'] . $file);
                $latest_filename = $file;
            }
        }
        return $latest_filename;
    }
    /**
     * Loads a backup
     * @param $backup_path Name of the backup to load
     */
    public static function Load($backup_path, $backup_from_backup_folder = true)
    {
        if(!is_file($backup_path)) return false;
        if($backup_from_backup_folder)
        {
            // Copy the backup
            if(!copy($backup_path, ".\\backup.zip")) return false;
            $backup_path = ".\\backup.zip";
        }
        
        // First remove the curent database
        SQL::Execute("DROP DATABASE `" . CONFIG['default_database'] . "`;", false);

        // Than the curent filesystem
        if(is_dir(CONFIG['filesystem'])) FS::DeleteFullDir(CONFIG['filesystem']);

        // Recreate filesystem dir
        if(!is_dir(CONFIG['filesystem'])) mkdir(CONFIG['filesystem']);

        // Unzip our filesystem
        FS::UnZip($backup_path, CONFIG['filesystem']);
        //if(!FS::UnZip($backup_path, CONFIG['filesystem'])) return false;

        // Now Create our database
        SQL::Execute("CREATE DATABASE " .CONFIG['default_database'] . ";", false);
        
        // And import our tables and rows
        if(!is_file(CONFIG['filesystem'] . 'export.sql')) return false;
        SQL::ExecuteFile(CONFIG['filesystem'] . 'export.sql');

        // The backup is loaded!
        return true;
        //return false; // TODO: Set back to fallback Point
    }
    /**
     * Creates a new backup
     */
    public static function Create()
    {
        $name = "backup_" . date('Y-m-d-H.i.s.ms');
        if(class_exists('FS'))
        {
            // Create a sql export file of the database
            $query = Backups::DumpDatabase();
            $exportFile = CONFIG['filesystem'] . 'export.sql';
            file_put_contents($exportFile, $query);

            // Check if backup folder exists
            if(!is_dir(CONFIG['backup_folder'])) mkdir(CONFIG['backup_folder']);

            // Create a zip of the filesystem
            FS::Zip(CONFIG['filesystem'], CONFIG['backup_folder'] . $name);
            // Remove temp backup
            unlink("./backup.zip");

            // Check if zip was successfull
            //if(!file_exists(CONFIG['backup_folder'] . $name)) return false;

            // After the zip is created we remove the export file from our filesystem
            unlink($exportFile);
            return true;
        } else return false;
    }
    static function DumpDatabase()
    {
        // Get the tables of our database
        $queryTables = SQL::Execute('SHOW TABLES');
        // Set database create query
        $content = "";
        
        // Loop throug all tables
        foreach($queryTables as $table)
        {
            // Check if we have a table
            if(empty($table)) continue;
            $table = $table[0];

            // Get create query
            $tableQuery = SQL::Execute("SHOW CREATE TABLE $table")->fetch()["Create Table"];
            // Add to content
            $content .= "$tableQuery;\n";
            
            // Get data from the table
            $data = SQL::Execute("SELECT * FROM $table");
            foreach($data as $row)
            {
                $values = "";
                // Foreach value
                $firstValue = true;
                foreach ($row as $value)
                {
                    // The values are double so every 1 we use the value 
                    if($firstValue) {
                        $values .= is_numeric($value) ? "$value, " : "\"$value\", "; // If string btwn ''
                        $firstValue = false;
                    }
                    else $firstValue = true;
                }
                // Remove the last to chars: ', '
                $values = substr($values, 0, -2);
                // Add Insert query to content
                $content .= "INSERT INTO $table VALUES($values);\n";
            }
        }
        return $content;
    }
}