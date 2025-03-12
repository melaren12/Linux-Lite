<?php

namespace App;

class SystemInfoManager
{
    public static function getCurrentUser() :string
    {
        return get_current_user();
    }
    public static function getCurrentDateTime()
    {
        return date("Y-m-d H:i:s");
    }
    public static function getSystemInformation() :string
    {
        return php_uname();
    }
    public static function getDiskSpace() :string
    {
        $disk = disk_free_space("/");
        $total = disk_total_space("/");

        return "Free: " . round($disk / 1024 / 1024 / 1024, 2) . " GB / " . round($total / 1024 / 1024 / 1024, 2) . " GB";
    }
    public static function getMemoryUsage() :string
    {
        $memory = memory_get_usage(true);

        return "Memory used: " . round($memory / 1024 / 1024, 2) . " MB";
    }
}
