<?php
// You can not reach this file by url.
// To execute this file use: php /path/to/this/install.php

echo "Getting config...<br>";

require './Framework/config.php';
echo "Creating FS Folders...\n";
mkdir(CONFIG['filesystem']);
echo "[1/4]: Main folder... CREATED\n";
mkdir(CONFIG['filesystem'] . 'FILES');
echo "[2/4]: /FILES... CREATED\n";
mkdir(CONFIG['filesystem'] . 'SYSTEM');
echo "[3/4]: /SYSTEM... CREATED\n";
mkdir(CONFIG['filesystem'] . 'SYSTEM\\APPS');
echo "[4/4]: /SYSTEM/APPS... CREATED\n";

echo "Getting Plugins...\n";
require './Framework/plugins.php';

echo "Creating Database...\n";
$db = CONFIG['default_database'];
$err = SQL::Execute("CREATE DATABASE $db;", false);
if(is_array($err) && array_key_exists('error', $err)) 
{
    var_dump($err);
    return;
}
echo "[1/2]: Database... CREATED\n";

$err = SQL::ExecuteFile("./_install/import.sql");
if(is_array($err) && array_key_exists('error', $err)) 
{
    var_dump($err);
    return;
}
echo "[2/2]: Tables... IMPORTED\n";

echo "Installation... DONE";