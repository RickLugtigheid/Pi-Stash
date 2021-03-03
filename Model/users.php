<?php

// Start session
session_start();

// Define all permissions
define("CREATE_PERM", 1000);
define("READ_PERM", 0100);
define("UPDATE_PERM", 0010);
define("DELETE_PERM", 0001);
define("ADMIN_PERM", 1111);

class User 
{
    public static function Create($name, $password)
    {
        // Check if we have permission to do this
        if(User::HasPerms(ADMIN_PERM))
        {
            SQL::ExecutePrepare("INSERT INTO Users (name, password) values (:name, :pass)", array(
                ":name" => $name,
                ":pass" => password_hash($password, PASSWORD_DEFAULT)
            ));
        }
        else CORE::ERROR("Permission Denied", 403, "We have no permission to create a new user");
    }

    /**
     * @param int $perm
     * @return bool If the user has these permissions
     */
    public static function HasPerms($perms)
    {
        // Check if a user is logedin
        if(!isset($_SESSION["userID"])) return false;

        // Get the permissions
        $userPerms = SQL::Execute("SELECT * FROM UsersPermissions WHERE userID=" . $_SESSION["userID"])->fetch();

        // Check if the user has these permissions with bitwise AND
        return bindec($perms) == (bindec($userPerms["permissions"]) & bindec($perms));

        // [Bitwise And]:
        // The AND opperator returns only a 1 if there is a 1 in both numbers on the same place
        //-------------------------
        //   1011   # UserPerms
        //   1000   # RequiredPerms
        // & ====
        //   1000   # Is the same as the RequiredPerms So permission granted!
    }

    public static function Login($id, $password)
    {
        // Get the password of the user with id '$id'
        $user = SQL::Execute("SELECT * FROM Users WHERE userID=$id")->fetch();
        var_dump($user);
        // Return if password is correct
        //return password_verify($password, $user["password"]);
        return $password == $user["password"]; // For testing
    }
}