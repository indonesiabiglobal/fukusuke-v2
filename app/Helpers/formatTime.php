<?php

namespace App\Helpers;

class formatTime
{
    // Helper untuk ubah waktu dari "HH:MM" jadi total menit
    public static function timeToMinutes($time)
    {
        [$hours, $minutes] = explode(':', $time);
        return (int)$hours * 60 + (int)$minutes;
    }

    // Helper untuk ubah dari menit ke "HH:MM"
    public static function minutesToTime($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
}
