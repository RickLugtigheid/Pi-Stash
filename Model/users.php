<?php

// Start session
if(!isset($_SESSION)) session_start();

// Define all permissions
define("CREATE_PERM", 1000);
define("READ_PERM", 0100);
define("UPDATE_PERM", 0010);
define("DELETE_PERM", 0001);
define("ADMIN_PERM", 1111);

class User 
{
    /**
     * Creates a new user
     * @param string $name Name of the user
     * @param string $password Password for the user
     * @requred Admin Permisions
     */
    public static function Create($name, $password)
    {
        // Check if we have permission to do this
        if(User::HasPerms(ADMIN_PERM))
        {
            // Create a new user in the database with the given info
            $user = SQL::ExecutePrepare("INSERT INTO Users (name, password) values (:name, :pass)", array(
                ":name" => $name,
                ":pass" => password_hash($password, PASSWORD_DEFAULT)
            ));

            Logger::LogSuccess("User '" . $_SESSION['name'] . "' created user '$name'", "users");
        }
        else CORE::ERROR("Permission Denied", 403, "We have no permission to create a new user");
    }

    /**
     * Reads all users or gets one by id
     * @param int $id Id of the user to get; If NULL get all users\
     * @requred Admin Permisions
     */
    public static function Read($id=null)
    {
        // Check if we have the permisions to do this
        if(User::HasPerms(ADMIN_PERM))
        {
            // Get the query to execute
            $query = "SELECT * FROM Users";
            if(isset($id)) $query . " WHERE id=$id";

            // Execute the query
            return SQL::Execute($query);
        }
        else CORE::ERROR("Permission Denied", 403, "We have no permission to look at a/all users");
    }
    public static function UpdatePass($password)
    {
        // Update the password
        SQL::ExecutePrepare("UPDATE users SET password=:pass WHERE userID=:uid", array(
            ":uid" => $_SESSION['userID'],
            ":pass" => password_hash($password, PASSWORD_DEFAULT)
        ));
    }

    public static function UpdatePerms($userID, $new_perms)
    {
        // Admin and others can't update there own account
        if($userID === $_SESSION['userID']) CORE::Error('Not Acceptable', 406, 'You can\'t change your own permisions');
        // Only admin can change its perms
        else if(User::HasPerms(ADMIN_PERM))
        {
            // Create a row for the user if not exists
            // For this we use IGNORE to ignore duplication attempts (to not throw error when already exists)
            SQL::ExecutePrepare("INSERT IGNORE INTO userspermissions (userID) VALUES (:uid)", array(
                ":uid" => $userID
            ));

            // Update the user permisions
            SQL::Execute("UPDATE userspermissions SET permissions=$new_perms WHERE userID=$userID");

            Logger::LogInfo("User '" . $_SESSION['name'] . "' updated permisions for user with id '$userID'", "users");
        }
        else CORE::ERROR("Permission Denied", 403, "We have no permission to update a users permisions");
    }
    public static function Delete($userID)
    {
        // Admin and others can't delete there own account
        if($userID === $_SESSION['userID']) CORE::Error('Not Acceptable', 406, 'You can\'t delete your own account');
        // Only admin can change its perms
        else if(User::HasPerms(ADMIN_PERM))
        {
            // Update the user permisions
            SQL::Execute("DELETE FROM users WHERE userID=$userID");

            Logger::LogWarning("User '" . $_SESSION['name'] . "' deleted user with id '$userID'", "users");
        }
        else CORE::ERROR("Permission Denied", 403, "We have no permission to delete this user");
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
        $userPerms = User::GetPerms();

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

    /**
     * Gets the permisions of the curent user
     */
    public static function GetPerms($all = false)
    {
        if($all) return SQL::Execute("SELECT * FROM UsersPermissions");
        return SQL::Execute("SELECT * FROM UsersPermissions WHERE userID=" . $_SESSION["userID"])->fetch();
    }

    public static function Login($id, $password)
    {
        // Get the password of the user with id '$id'
        $user = SQL::Execute("SELECT * FROM Users WHERE userID=$id")->fetch();
        // Return if password is correct
        return password_verify($password, $user["password"]);
        //return $password == $user["password"]; // For testing
    }
}