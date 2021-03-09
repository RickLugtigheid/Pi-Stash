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
        CORE::View("index", "admin");
    }
    public function cli()
    {
        CORE::View('cli', 'Command Prompt');
    }
    public function createUser()
    {
        // Get the values we want
        $username = $_POST['username'];
        $password = $_POST['password'];
        $perms = $_POST['perms'];
        // Create a new user
        User::Create($username, $password, $perms);
    }
}
$methods = array(
    "createUser" => array(
        'method' => 'POST'
    )
);