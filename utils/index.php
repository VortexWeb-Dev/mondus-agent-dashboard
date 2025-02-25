<?php

function get_quarter($month)
{
    if ($month == 'Jan' || $month == 'Feb' || $month == 'Mar') {
        return 'Q1';
    } elseif ($month == 'Apr' || $month == 'May' || $month == 'Jun') {
        return 'Q2';
    } elseif ($month == 'Jul' || $month == 'Aug' || $month == 'Sep') {
        return 'Q3';
    } elseif ($month == 'Oct' || $month == 'Nov' || $month == 'Dec') {
        return 'Q4';
    }
}

function clearCache($fileName)
{
    $filePath = 'cache/' . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}
