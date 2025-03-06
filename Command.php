<?php

require_once "Session.php";
require_once "FileManager.php";

class CommandExecutor
{

    public static function execute($input)
    {
        $parts = explode(" ", $input);
        $command = strtolower(trim($parts[0]));
        $args = array_slice($parts, 1);

        switch ($command) {
            case 'pwd':
                return SessionManager::getCurrentDir();
            case 'ls':
                if (isset($parts[1]) && $parts[1] == '-l') {
                    return  DirectoryContents::contentsWithPermissions(SessionManager::getCurrentDir());
                }
                return FileManager::runLs($parts[0]);
            case 'cat':
                return isset($parts[1]) ? FileManager::runCat($parts[1]) : "Specify file!";
            case 'whoami':
                return FileManager::runWhoami();
            case 'date':
                return FileManager::runDate();
            case 'uname':
                return FileManager::runUname();
            case 'df':
                return FileManager::runDf();
            case 'free':
                return FileManager::runFree();
            case 'cd':
                return isset($parts[1]) ? FileManager::runCd($parts[1]) : "Specify file!";
            case 'mkdir':
                return isset($parts[1]) ? FileManager::runMkdir($parts[1]) : "Specify file!";
            case 'touch':
                return isset($parts[1]) ? FileManager::runTouch($parts[1]) : "Specify file!";
            case '~':
                return FileManager::runParent();
            case 'file':
                return isset($parts[1]) ? FileManager::runFile($parts[1]) : "Specify file!";
            case 'cp':
                if (isset($parts[1]) && isset($parts[2])) {
                    return FileManager::runCp($parts[1], $parts[2]);
                }
                return "Specify file!";
            case 'mv':
                return count($args) == 2 ? FileManager::runMv($args[0], $args[1]) : "Specify files!";
            case 'rm':
                if (isset($parts[1])) {
                    return FileManager::runRm(SessionManager::getCurrentDir() . '/' . $parts[1]);
                }
                return "Specify file!";
            case 'cat>':
                $newArray = array_slice($parts, 2);
                if (isset($parts[1]) && isset($parts[2]) && !empty($newArray)) {
                    $data = implode(" ", $newArray);
                    return FileManager::runWrite($parts[1], $data);
                }
                return "Specify file or text";
            case 'chmod':
                if (isset($parts[1]) && isset($parts[2])) {
                    return FileManager::changePermissions($parts[1], $parts[2]);
                }
            case 'chown':
                if (isset($parts[1]) && isset($parts[2])) {
                    return FileManager::changeOwner(SessionManager::getCurrentDir() . '/' . $parts[1], $parts[2]);
                }
            default:
                return "Unknown command!";
        }
    }
}
