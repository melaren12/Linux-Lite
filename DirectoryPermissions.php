<?php

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
