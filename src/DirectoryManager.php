<?php

namespace App;

require_once "vendor/autoload.php";

use App\SessionManager;

class DirectoryManager
{
    public static function changeDirectory($file_name) :string
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if ($new_path && is_dir($new_path)) {
            $_SESSION['current_dir'] = $new_path;

            return "Directory changed to" . $_SESSION['current_dir'];
        }

        return "Directory not found";
    }

    public static function navigateToParentDirectory()
    {
        return $_SESSION['current_dir'] = getcwd();
    }

    public static function removeDirectory($dir) :string
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $file_path = $dir . '/' . $file;

            if (is_dir($file_path)) {
                DirectoryManager::removeDirectory($file_path);
            } else {
                unlink($file_path);
            }
        }

        rmdir($dir);
        return "Directory deleted!";
    }
}
