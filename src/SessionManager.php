<?php

namespace App;

class SessionManager
{
    public static function start()
    {
        session_start();
        if (!isset($_SESSION['current_dir'])) $_SESSION['current_dir'] = getcwd();
    }
    public static function getCurrentDir(): string
    {
        return $_SESSION['current_dir'];
    }

}
