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
        $perms = array();
        foreach(User::GetPerms(true) as $perm)
            $perms[$perm['userID']] = $perm['permissions'];
        CORE::View("index", "admin", array("users" => User::Read(), "perms" => $perms));
    }
    public function cli()
    {
        CORE::View('cli', 'Command Prompt');
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
        //header("Location: /". ROOT_DIR . "/admin/index");
    }
}