<?php

function l(String $text)
{
    return $text;
}

function caslog(String $text)
{
    $line = "\n" . date("d-M-Y H:i:s") . "  " . $text;
    $log_path = LOG_DIR . "admin.log";
    file_put_contents($log_path, $line, FILE_APPEND);
}