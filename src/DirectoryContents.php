<?php

namespace App;

class DirectoryContents
{
    const TYPE_SOCKET = 0xC000;
    const TYPE_LINK = 0xA000;
    const TYPE_REGULAR = 0x8000;
    const TYPE_BLOCK = 0x6000;
    const TYPE_DIRECTORY = 0x4000;
    const TYPE_CHAR = 0x2000;
    const TYPE_FIFO = 0x1000;

    const PERM_OWNER_READ = 0x0100;
    const PERM_OWNER_WRITE = 0x0080;
    const PERM_OWNER_EXEC = 0x0040;
    const PERM_SETUID = 0x0800;

    const PERM_GROUP_READ = 0x0020;
    const PERM_GROUP_WRITE = 0x0010;
    const PERM_GROUP_EXEC = 0x0008;
    const PERM_SETGID = 0x0400;

    const PERM_WORLD_READ = 0x0004;
    const PERM_WORLD_WRITE = 0x0002;
    const PERM_WORLD_EXEC = 0x0001;
    const PERM_STICKY = 0x0200;

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
    public static function getPermissions($permissions): string
    {
        // Тип файла
        if (($permissions & self::TYPE_SOCKET) == self::TYPE_SOCKET) {
            $info = 's';
        } elseif (($permissions & self::TYPE_LINK) == self::TYPE_LINK) {
            $info = 'l';
        } elseif (($permissions & self::TYPE_REGULAR) == self::TYPE_REGULAR) {
            $info = '-';
        } elseif (($permissions & self::TYPE_BLOCK) == self::TYPE_BLOCK) {
            $info = 'b';
        } elseif (($permissions & self::TYPE_DIRECTORY) == self::TYPE_DIRECTORY) {
            $info = 'd';
        } elseif (($permissions & self::TYPE_CHAR) == self::TYPE_CHAR) {
            $info = 'c';
        } elseif (($permissions & self::TYPE_FIFO) == self::TYPE_FIFO) {
            $info = 'p';
        } else {
            $info = 'u';
        }

        // Владелец (Owner)
        $info .= (($permissions & self::PERM_OWNER_READ) ? 'r' : '-');
        $info .= (($permissions & self::PERM_OWNER_WRITE) ? 'w' : '-');
        $info .= (($permissions & self::PERM_OWNER_EXEC) ?
            (($permissions & self::PERM_SETUID) ? 's' : 'x') :
            (($permissions & self::PERM_SETUID) ? 'S' : '-'));

        // Группа (Group)
        $info .= (($permissions & self::PERM_GROUP_READ) ? 'r' : '-');
        $info .= (($permissions & self::PERM_GROUP_WRITE) ? 'w' : '-');
        $info .= (($permissions & self::PERM_GROUP_EXEC) ?
            (($permissions & self::PERM_SETGID) ? 's' : 'x') :
            (($permissions & self::PERM_SETGID) ? 'S' : '-'));

        // Остальные (World)
        $info .= (($permissions & self::PERM_WORLD_READ) ? 'r' : '-');
        $info .= (($permissions & self::PERM_WORLD_WRITE) ? 'w' : '-');
        $info .= (($permissions & self::PERM_WORLD_EXEC) ?
            (($permissions & self::PERM_STICKY) ? 't' : 'x') :
            (($permissions & self::PERM_STICKY) ? 'T' : '-'));

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
