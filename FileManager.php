<?php

require_once "Session.php";

class FileManager
{
    public static function runLs()
    {
        $files = array_diff(scandir(SessionManager::getCurrentDir()), ['.', '..']);
        return empty($files) ? "Directory is empty" : implode("\n", $files);
    }

    public static function runCat($file_name)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        return file_exists($new_path) ? file_get_contents($new_path) : "File not found";
    }

    public static function runWhoami()
    {
        return get_current_user();
    }

    public static function runDate()
    {
        return date("Y-m-d H:i:s");
    }

    public static function runUname()
    {
        return php_uname();
    }

    public static function runDf()
    {
        $disk = disk_free_space("/");
        $total = disk_total_space("/");
        return "Free: " . round($disk / 1024 / 1024 / 1024, 2) . " GB / " . round($total / 1024 / 1024 / 1024, 2) . " GB";
    }

    public static function runFree()
    {
        $memory = memory_get_usage(true);
        return "Memory used: " . round($memory / 1024 / 1024, 2) . " MB";
    }

    public static function runCd($file_name)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if ($new_path && is_dir($new_path)) {
            $_SESSION['current_dir'] = $new_path;
            return "Directory changed to" . $_SESSION['current_dir'];
        }
        return "Directory not found";
    }

    public static function runMkdir($file_name)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (!file_exists($new_path)) {
            mkdir($new_path, 0777, true);
            return "Directory created succesfuly" . "<br>" . $new_path;
        } else {
            return "The Given file path already exists";
        }
    }

    public static function runTouch($file_name)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (!file_exists($new_path)) {
            fopen($new_path, 'w');
            return "File created successfully!";
        }
        return "File already exists!";
    }

    public static function runParent()
    {
        return $_SESSION['current_dir'] = getcwd();
    }

    public static function runFile($file_name)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (file_exists($new_path)) {
            return filetype($new_path);
        }
        return "File not found";
    }

    public static function runCp($source, $destination)
    {
        if (!file_exists($destination)) {
            $new_path = SessionManager::getCurrentDir() . '/' . $destination;
            fopen($new_path, 'w');
        }
        if (is_file($source) && is_file($destination)) {
            copy($source, $destination);
            return "File Copied!";
        }
        return "It is not file!";
    }

    public static function runMv($old_name, $new_name)
    {
        if (file_exists($old_name)) {
            rename($old_name, $new_name);
            return "File renamed succesfuly";
        }
        return "File not found";
    }

    public static function runRm($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $file_path = $dir . '/' . $file;
            if (is_dir($file_path)) {
                FileManager::runRm($file_path);
            } else {
                unlink($file_path);
            }
        }

        rmdir($dir);
        return "Directory deleted!";
    }

    public static function runWrite($file_name, $data)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;

        if (file_exists($new_path)) {
            $file = fopen($new_path, "w");
            fwrite($file, $data);
            fclose($file);
            return "The text is written to the file.";
        }
        return "File not found";
    }

    public static function changePermissions() {}
    public static function changeOwner() {}
}

class DirectoryContents
{
    public static function contentsWithPermissions() {}
}
