<?php
class SessionManager
{
    public static function start()
    {
        session_start();
        if (!isset($_SESSION['current_dir'])) {
            $_SESSION['current_dir'] = getcwd();
        }
    }

    public static function getCurrentDir(): string
    {
        return $_SESSION['current_dir'];
    }

    public static function changeDir(string $dir): string
    {
        $new_path = realpath($_SESSION['current_dir'] . '//' . $dir);
        if ($new_path && is_dir($new_path)) {
            $_SESSION['current_dir'] = $new_path;
            return "Directory changed to: " . $_SESSION['current_dir'];
        }
        return "Directory not found";
    }
}
