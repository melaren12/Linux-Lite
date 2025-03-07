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

    public static function changePermissions($file_name, $code)
    {
        $perm_code = '0' . $code;
        $file_path = SessionManager::getCurrentDir() . '/' . $file_name;
        if (file_exists($file_path)) {
            return chmod($file_path, $perm_code);
        }

        return "File not found";
    }

    public static function changeOwner($username, $file_name)
    {
        $file_path = SessionManager::getCurrentDir() . '/' . $file_name;

        $command = 'sudo chown ' . escapeshellarg($username) . ' ' . escapeshellarg($file_path) . ' 2>&1';
        $output = shell_exec($command);

        if ($output === null) {
            return "Failed to execute command.";
        }

        return $output;
    }
}

class DirectoryContents
{
    public static function contentsPermissions($dir)
    {
        $files = scandir($dir);

        if ($files === false) {
            return false;
        }

        $result = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $dir . '/' . $file;
            $permissions = fileperms($filePath);

            if (is_dir($filePath)) {
                $is_directory = 'YES';
            } else {
                $is_directory = 'NO';
            }

            $result[] = [
                'name' => $file,
                'path' => $filePath,
                'permissions' => DirectoryContents::getPermissions($permissions),
                'Is Directory' => $is_directory,
                'File Owner' => DirectoryContents::getFileOwnerName($filePath),
            ];
        }

        return var_export($result, true);
    }
    public static function getPermissions($permissions)
    {
        if (($permissions & 0xC000) == 0xC000) {
            // Socket
            $info = 's';
        } elseif (($permissions & 0xA000) == 0xA000) {
            // Symbolic Link
            $info = 'l';
        } elseif (($permissions & 0x8000) == 0x8000) {
            // Regular
            $info = '-';
        } elseif (($permissions & 0x6000) == 0x6000) {
            // Block special
            $info = 'b';
        } elseif (($permissions & 0x4000) == 0x4000) {
            // Directory
            $info = 'd';
        } elseif (($permissions & 0x2000) == 0x2000) {
            // Character special
            $info = 'c';
        } elseif (($permissions & 0x1000) == 0x1000) {
            // FIFO pipe
            $info = 'p';
        } else {
            // Unknown
            $info = 'u';
        }

        // Owner
        $info .= (($permissions & 0x0100) ? 'r' : '-');
        $info .= (($permissions & 0x0080) ? 'w' : '-');
        $info .= (($permissions & 0x0040) ?
            (($permissions & 0x0800) ? 's' : 'x') : (($permissions & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($permissions & 0x0020) ? 'r' : '-');
        $info .= (($permissions & 0x0010) ? 'w' : '-');
        $info .= (($permissions & 0x0008) ?
            (($permissions & 0x0400) ? 's' : 'x') : (($permissions & 0x0400) ? 'S' : '-'));

        // World
        $info .= (($permissions & 0x0004) ? 'r' : '-');
        $info .= (($permissions & 0x0002) ? 'w' : '-');
        $info .= (($permissions & 0x0001) ?
            (($permissions & 0x0200) ? 't' : 'x') : (($permissions & 0x0200) ? 'T' : '-'));

        return $info;
    }

    public static function getFileOwnerName($filename)
    {
        $ownerId = fileowner($filename);

        if ($ownerId === false) {
            return false;
        }

        $ownerInfo = posix_getpwuid($ownerId);

        if ($ownerInfo === false) {
            return false;
        }

        return $ownerInfo['name'];
    }
}
