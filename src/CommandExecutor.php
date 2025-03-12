<?php

namespace App;

class CommandExecutor
{
    public static function execute($input)
    {
        $parts = explode(" ", $input);
        $command = strtolower(trim($parts[0]));
        $args = array_slice($parts, 1);

        function executeCommand($command, $parts, $args)
        {
            $commands = [
                'pwd' => function () {
                    return SessionManager::getCurrentDir();
                },
                'ls' => function () use ($parts) {
                    if (isset($parts[1]) && $parts[1] == '-l') {
                        return DirectoryContents::contentsPermissions(SessionManager::getCurrentDir());
                    }
                    return FileSystemManager::listFiles($parts[0]);
                },
                'cat' => function () use ($parts) {
                    return isset($parts[1]) ? FileSystemManager::readFileContents($parts[1]) : "Specify file!";
                },
                'whoami' => function () {
                    return SystemInfoManager::getCurrentUser();
                },
                'date' => function () {
                    return SystemInfoManager::getCurrentDateTime();
                },
                'uname' => function () {
                    return SystemInfoManager::getSystemInformation();
                },
                'df' => function () {
                    return SystemInfoManager::getDiskSpace();
                },
                'free' => function () {
                    return SystemInfoManager::getMemoryUsage();
                },
                'cd' => function () use ($parts) {
                    return isset($parts[1]) ? DirectoryManager::changeDirectory($parts[1]) : "Specify file!";
                },
                'mkdir' => function () use ($parts) {
                    return isset($parts[1]) ? FileSystemManager::createDirectory($parts[1]) : "Specify file!";
                },
                'touch' => function () use ($parts) {
                    return isset($parts[1]) ? FileSystemManager::createFile($parts[1]) : "Specify file!";
                },
                '~' => function () {
                    return DirectoryManager::navigateToParentDirectory();
                },
                'file' => function () use ($parts) {
                    return isset($parts[1]) ? FileSystemManager::getFileType($parts[1]) : "Specify file!";
                },
                'cp' => function () use ($parts) {
                    if (isset($parts[1]) && isset($parts[2])) {
                        return FileSystemManager::copyFile($parts[1], $parts[2]);
                    }
                    return "Specify file!";
                },
                'mv' => function () use ($args) {
                    return count($args) == 2 ? FileSystemManager::renameFile($args[0], $args[1]) : "Specify files!";
                },
                'rm' => function () use ($parts) {
                    if (isset($parts[1])) {
                        return DirectoryManager::removeDirectory(SessionManager::getCurrentDir() . '/' . $parts[1]);
                    }
                    return "Specify file!";
                },
                'cat>' => function () use ($parts) {
                    $newArray = array_slice($parts, 2);
                    if (isset($parts[1]) && isset($parts[2]) && !empty($newArray)) {
                        $data = implode(" ", $newArray);
                        return FileSystemManager::writeFileContents($parts[1], $data);
                    }
                    return "Specify file or text";
                },
                'chmod' => function () use ($parts) {
                    if (isset($parts[1]) && isset($parts[2])) {
                        return FileSystemManager::setFilePermissions($parts[1], $parts[2]);
                    }
                    return "Specify file and mode";
                },
                'chown' => function () use ($parts) {
                    if (isset($parts[1]) && isset($parts[2])) {
                        return FileSystemManager::setFileOwner($parts[1], $parts[2]);
                    }
                    return "Specify file and owner";
                },
            ];

            if (isset($commands[$command])) {
                return $commands[$command]();
            } else {
                return "Unknown command!";
            }
        }
        return executeCommand($command, $parts, $args);
    }
}
