<?php

// Load needed models
CORE::LoadModel("users");

// Check if we have admin perms
if(!User::HasPerms(ADMIN_PERM))
{
    CORE::Error("Permission Denied", 403, "Invalid permissions");
    return;
}
// After this we don't need to check perms in the rest of the controller code

class admin
{
    public function index()
    {
        // Get permisions
        $perms = array();
        foreach(User::GetPerms(true) as $perm)
            $perms[$perm['userID']] = $perm['permissions'];

        $backups = array();
        foreach(array_diff(scandir(CONFIG['backup_folder']), array('.', '..')) as $backup)
            $backups[] = array(
                "name" => $backup,
                "path" => CONFIG['backup_folder'] . $backup
            );
        
        CORE::View("index", "admin", array("users" => User::Read(), "backups" => $backups, "perms" => $perms));
    }
    public function create_backup()
    {
        // Create a new backup
        CORE::LoadModel('backup');
        CORE::LoadModel('filesystem');

        // View 
        if(!Backups::Create()) 
        {
            CORE::Error('Internal Server Error', 500, 'Error when creating database');
            return;
        }
        // Go back
        header("Location: /". ROOT_DIR . "/admin/index");
    }
    public function load_backup($args)
    {
        CORE::LoadModel('backup');
        CORE::LoadModel('filesystem');
        $backup = CONFIG['backup_folder'] . $args[2];

        // Set our last created backup as fallback point and load out backup
        Backups::LoadWithFallback(Backups::GetLast(), Backups::Load($backup));

        // Go back
        header("Location: /". ROOT_DIR . "/admin/index");
    }
    public function delete_backup($args)
    {
        $backup = CONFIG['backup_folder'] . $args[2];

        // If exists delete the backup
        if(file_exists($backup)) unlink($backup);

        // Go back
        header("Location: /". ROOT_DIR . "/admin/index");
    }
    public function download_backup($args)
    {
        $backup = CONFIG['backup_folder'] . $args[2];

        // If exists download it
        if(file_exists($backup))
        {
            // Load the file to page
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($backup));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup));
            ob_clean();
            flush();
            readfile($backup);
            exit;
        }

        // Go back
        header("Location: /". ROOT_DIR . "/admin/index");
    }
    /**
     * @method POST
     * @sanitize POST
     */
    public function create_user()
    {
        // Get the values we want
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        // Create a new user
        User::Create($username, $password);

        // Go back
        header("Location: /". ROOT_DIR . "/admin/index");
    }
    /**
     * @sanitize GET
     */
    public function delete_user()
    {
        // Delete the user with id
        User::Delete($_GET['id']);
        // Go back
        header("Location: /". ROOT_DIR . "/admin/index");
    }
    /**
     * @method POST
     * @sanitize POST
     */
    public function update_perms()
    {
        function CheckValueToInt($check_box_value)
        {
            return $check_box_value == 'on' ? 1 : 0;
        }

        // Get the id of the user to update perms for
        $id = $_POST['uid'];

        // Create the perms
        // 1000 = create; 0100 = read; 0010 = update; 0001 = delete;
        $perms = CheckValueToInt($_POST['create']) . CheckValueToInt($_POST['read']) . CheckValueToInt($_POST['update']) . CheckValueToInt($_POST['delete']);

        // Now update the perms
        User::UpdatePerms($id, $perms);

        // Go back
        header("Location: /". ROOT_DIR . "/admin/index");
    }
}