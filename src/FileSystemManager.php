<?php

namespace App;

class FileSystemManager
{
    public static function listFiles() :string
    {
        $files = array_diff(scandir(SessionManager::getCurrentDir()), ['.', '..']);

        return empty($files) ? "Directory is empty" : implode("\n", $files);
    }

    public static function readFileContents($file_name)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;

        return file_exists($new_path) ? file_get_contents($new_path) : "File not found";
    }

    public static function createFile($file_name) :string
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (!file_exists($new_path)) {
            fopen($new_path, 'w');

            return "File created successfully!";
        }

        return "File already exists!";
    }

    public static function createDirectory($file_name) :string
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (!file_exists($new_path)) {
            mkdir($new_path, 0777, true);

            return "Directory created succesfuly" . "<br>" . $new_path;
        } else {
            return "The Given file path already exists";
        }
    }

    public static function copyFile($source, $destination) :string
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

    public static function renameFile($old_name, $new_name) :string
    {
        if (file_exists($old_name)) {
            rename($old_name, $new_name);

            return "File renamed succesfuly";
        }

        return "File not found";
    }

    public static function writeFileContents($file_name, $data) :string
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

    public static function setFilePermissions($file_name, $code)
    {
        $perm_code = '0' . $code;
        $file_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (file_exists($file_path)) {
            return chmod($file_path, $perm_code);
        }

        return "File not found";
    }

    public static function setFileOwner($username, $file_name)
    {
        $file_path = SessionManager::getCurrentDir() . '/' . $file_name;

        $command = 'sudo chown ' . escapeshellarg($username) . ' ' . escapeshellarg($file_path) . ' 2>&1';
        $output = shell_exec($command);

        if ($output === null) {
            return "Failed to execute command.";
        }

        return $output;
    }

    public static function getFileType($file_name)
    {
        $new_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (file_exists($new_path)) {
            return filetype($new_path);
        }

        return "File not found";
    }
}
