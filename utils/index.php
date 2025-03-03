<?php

function get_quarter($month)
{
    $quarters = [
        'Jan' => 'Q1',
        'Feb' => 'Q1',
        'Mar' => 'Q1',
        'Apr' => 'Q2',
        'May' => 'Q2',
        'Jun' => 'Q2',
        'Jul' => 'Q3',
        'Aug' => 'Q3',
        'Sep' => 'Q3',
        'Oct' => 'Q4',
        'Nov' => 'Q4',
        'Dec' => 'Q4'
    ];

    return $quarters[$month] ?? 'Q1';
}

function clearCache($fileName)
{
    $filePath = 'cache/' . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}
